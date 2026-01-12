/**
 * Trac Feed - Interactivity API module.
 *
 * @package Earthbound
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

const { state, actions, callbacks } = store( 'earthbound/trac-feed', {
	state: {
		get isLoading() {
			const context = getContext();
			return context.isLoading;
		},
		get hasMore() {
			const context = getContext();
			return context.items.length < context.totalItems;
		},
		get hasItems() {
			const context = getContext();
			return context.items.length > 0;
		},
		get buttonText() {
			const context = getContext();
			if ( context.isLoading ) {
				return 'Loading...';
			}
			return 'Load More';
		},
		get statusMessage() {
			const context = getContext();
			if ( context.isLoading ) {
				return 'Loading more contributions...';
			}
			if ( context.error ) {
				return `Error: ${ context.error }`;
			}
			return `Showing ${ context.items.length } of ${ context.totalItems } contributions`;
		},
		get error() {
			const context = getContext();
			return context.error;
		},
	},
	actions: {
		*loadMore() {
			const context = getContext();
			if ( context.isLoading ) return;

			context.isLoading = true;
			context.error = null;

			try {
				const nextPage = context.currentPage + 1;
				const response = yield fetch(
					`/wp-json/earthbound/v1/trac-activity?page=${ nextPage }&per_page=${ context.perPage }`
				);

				if ( ! response.ok ) {
					throw new Error( 'Failed to fetch data' );
				}

				const data = yield response.json();

				context.items = [ ...context.items, ...data.items ];
				context.currentPage = data.page;
				context.totalItems = data.total;

				// Render new items.
				callbacks.renderNewItems( data.items );
			} catch ( err ) {
				context.error =
					'Failed to load more contributions. Please try again.';
			} finally {
				context.isLoading = false;
			}
		},
		handleKeydown( event ) {
			if ( event.key === 'Enter' || event.key === ' ' ) {
				event.preventDefault();
				actions.loadMore();
			}
		},
	},
	callbacks: {
		renderNewItems( items ) {
			const element = getElement();
			const list = element?.ref?.querySelector( '.trac-feed__list' );
			if ( ! list ) return;

			items.forEach( ( item ) => {
				const li = document.createElement( 'li' );
				li.className = 'trac-feed__item feed-item';
				li.innerHTML = `
					<h4 class="trac-feed__title feed-item__title">
						<a href="${ escapeHtml( item.url ) }" target="_blank" rel="noopener noreferrer">
							${ escapeHtml( item.title ) }
							<span class="screen-reader-text">(opens in new tab)</span>
						</a>
					</h4>
					<div class="trac-feed__meta feed-item__meta">
						${ item.ticket_id ? `
							<span class="trac-feed__ticket">#${ escapeHtml( item.ticket_id ) }</span>
							<span class="trac-feed__separator" aria-hidden="true">&middot;</span>
						` : '' }
						${ item.date ? `
							<time class="trac-feed__date" datetime="${ escapeHtml( item.date ) }">
								${ formatDate( item.date ) }
							</time>
						` : '' }
					</div>
					${ item.description ? `
						<p class="trac-feed__description">
							${ escapeHtml( trimWords( item.description, 20 ) ) }
						</p>
					` : '' }
				`;
				list.appendChild( li );
			} );
		},
	},
} );

// Helper functions.
function escapeHtml( str ) {
	if ( typeof str !== 'string' ) return '';
	const div = document.createElement( 'div' );
	div.textContent = str;
	return div.innerHTML;
}

function formatDate( dateString ) {
	try {
		const date = new Date( dateString );
		return date.toLocaleDateString( undefined, {
			year: 'numeric',
			month: 'short',
			day: 'numeric',
		} );
	} catch ( e ) {
		return dateString;
	}
}

function trimWords( str, count ) {
	if ( typeof str !== 'string' ) return '';
	const words = str.split( /\s+/ );
	if ( words.length <= count ) return str;
	return words.slice( 0, count ).join( ' ' ) + '...';
}
