/**
 * Earthbound Navigation - Interactivity API module.
 *
 * @package Earthbound
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

const focusableSelectors = [
	'a[href]',
	'button:not([disabled])',
	'[tabindex]:not([tabindex="-1"])',
];

const { state, actions } = store( 'earthbound/navigation', {
	state: {
		get isMenuOpen() {
			const context = getContext();
			return context.isOpen;
		},
		get isSubmenuOpen() {
			const context = getContext();
			const element = getElement();
			const submenuId = element?.ref?.dataset?.submenu;
			return context.activeSubmenu === submenuId;
		},
		get isSubmenuActive() {
			const context = getContext();
			const element = getElement();
			const parent = element?.ref?.closest( '[data-submenu]' );
			const submenuId = parent?.dataset?.submenu;
			return context.activeSubmenu === submenuId;
		},
		get statusMessage() {
			const context = getContext();
			if ( context.isOpen ) {
				return 'Navigation menu expanded';
			}
			return '';
		},
	},
	actions: {
		toggleMenu() {
			const context = getContext();
			context.isOpen = ! context.isOpen;

			if ( context.isOpen ) {
				actions.trapFocus();
			} else {
				actions.releaseFocus();
				context.activeSubmenu = null;
			}
		},
		toggleSubmenu( event ) {
			const context = getContext();
			const submenuId = event.target.dataset.submenu;

			if ( context.activeSubmenu === submenuId ) {
				context.activeSubmenu = null;
			} else {
				context.activeSubmenu = submenuId;
			}
		},
		openSubmenu( event ) {
			const context = getContext();
			const submenuId = event.target.dataset.submenu;
			context.activeSubmenu = submenuId;
		},
		closeSubmenu() {
			const context = getContext();
			context.activeSubmenu = null;
		},
		handleKeydown( event ) {
			const context = getContext();

			switch ( event.key ) {
				case 'Escape':
					event.preventDefault();
					actions.closeAll();
					break;
			}
		},
		handleSubmenuKeydown( event ) {
			const context = getContext();

			switch ( event.key ) {
				case 'ArrowDown':
					event.preventDefault();
					actions.openSubmenu( event );
					actions.focusFirstSubmenuItem( event );
					break;
				case 'ArrowUp':
					event.preventDefault();
					actions.openSubmenu( event );
					actions.focusLastSubmenuItem( event );
					break;
				case 'ArrowRight':
					event.preventDefault();
					actions.focusNextMenuItem( event );
					break;
				case 'ArrowLeft':
					event.preventDefault();
					actions.focusPreviousMenuItem( event );
					break;
				case 'Enter':
				case ' ':
					event.preventDefault();
					actions.toggleSubmenu( event );
					break;
				case 'Escape':
					event.preventDefault();
					actions.closeSubmenu();
					event.target.focus();
					break;
			}
		},
		trapFocus() {
			const element = getElement();
			const nav = element?.ref?.closest( '.earthbound-nav' );
			if ( ! nav ) return;

			const focusables = nav.querySelectorAll(
				focusableSelectors.join( ',' )
			);
			if ( focusables.length === 0 ) return;

			const firstFocusable = focusables[ 0 ];
			const lastFocusable = focusables[ focusables.length - 1 ];

			nav.addEventListener( 'keydown', ( e ) => {
				if ( e.key !== 'Tab' ) return;

				if ( e.shiftKey ) {
					if ( document.activeElement === firstFocusable ) {
						e.preventDefault();
						lastFocusable.focus();
					}
				} else {
					if ( document.activeElement === lastFocusable ) {
						e.preventDefault();
						firstFocusable.focus();
					}
				}
			} );

			// Focus the first menu item.
			const firstMenuItem = nav.querySelector( '.nav-menu__link' );
			if ( firstMenuItem ) {
				firstMenuItem.focus();
			}
		},
		releaseFocus() {
			const element = getElement();
			const toggle = element?.ref
				?.closest( '.earthbound-nav' )
				?.querySelector( '.nav-toggle' );
			if ( toggle ) {
				toggle.focus();
			}
		},
		focusFirstSubmenuItem( event ) {
			const parent = event.target.closest( '.nav-menu__item--has-children' );
			if ( ! parent ) return;

			const firstItem = parent.querySelector( '.nav-submenu__link' );
			if ( firstItem ) {
				setTimeout( () => firstItem.focus(), 50 );
			}
		},
		focusLastSubmenuItem( event ) {
			const parent = event.target.closest( '.nav-menu__item--has-children' );
			if ( ! parent ) return;

			const items = parent.querySelectorAll( '.nav-submenu__link' );
			if ( items.length > 0 ) {
				setTimeout( () => items[ items.length - 1 ].focus(), 50 );
			}
		},
		focusNextMenuItem( event ) {
			const currentItem = event.target.closest( '.nav-menu__item' );
			if ( ! currentItem ) return;

			const nextItem = currentItem.nextElementSibling;
			if ( nextItem ) {
				const link = nextItem.querySelector(
					'.nav-menu__link, .nav-menu__link--parent'
				);
				if ( link ) link.focus();
			}
		},
		focusPreviousMenuItem( event ) {
			const currentItem = event.target.closest( '.nav-menu__item' );
			if ( ! currentItem ) return;

			const prevItem = currentItem.previousElementSibling;
			if ( prevItem ) {
				const link = prevItem.querySelector(
					'.nav-menu__link, .nav-menu__link--parent'
				);
				if ( link ) link.focus();
			}
		},
		focusNext() {
			const element = getElement();
			const nav = element?.ref?.closest( '.earthbound-nav' );
			if ( ! nav ) return;

			const focusables = Array.from(
				nav.querySelectorAll( focusableSelectors.join( ',' ) )
			);
			const currentIndex = focusables.indexOf( document.activeElement );

			if ( currentIndex < focusables.length - 1 ) {
				focusables[ currentIndex + 1 ].focus();
			}
		},
		focusPrevious() {
			const element = getElement();
			const nav = element?.ref?.closest( '.earthbound-nav' );
			if ( ! nav ) return;

			const focusables = Array.from(
				nav.querySelectorAll( focusableSelectors.join( ',' ) )
			);
			const currentIndex = focusables.indexOf( document.activeElement );

			if ( currentIndex > 0 ) {
				focusables[ currentIndex - 1 ].focus();
			}
		},
		closeAll() {
			const context = getContext();
			context.isOpen = false;
			context.activeSubmenu = null;
			actions.releaseFocus();
		},
	},
} );
