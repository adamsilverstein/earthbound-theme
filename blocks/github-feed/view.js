/**
 * GitHub Feed - Interactivity API module.
 *
 * @package Earthbound
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

const { state, actions, callbacks } = store( 'earthbound/github-feed', {
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
				return 'Loading more items...';
			}
			if ( context.error ) {
				return `Error: ${ context.error }`;
			}
			return `Showing ${ context.items.length } of ${ context.totalItems } items`;
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
					`/wp-json/earthbound/v1/github-activity?page=${ nextPage }&per_page=${ context.perPage }`
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
				context.error = 'Failed to load more items. Please try again.';
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
		updateList() {
			// This callback is called when the context changes.
			// The list will be updated automatically via data-wp-each.
		},
		renderNewItems( items ) {
			const element = getElement();
			const list = element?.ref?.querySelector( '.github-feed__list' );
			if ( ! list ) return;

			items.forEach( ( item ) => {
				const li = document.createElement( 'li' );
				li.className = 'github-feed__item feed-item';
				li.innerHTML = `
					<h4 class="github-feed__title feed-item__title">
						<a href="${ escapeHtml( item.url ) }" target="_blank" rel="noopener noreferrer">
							${ escapeHtml( item.title ) }
							<span class="screen-reader-text">(opens in new tab)</span>
						</a>
					</h4>
					<div class="github-feed__meta feed-item__meta">
						<span class="github-feed__repo">${ escapeHtml( item.repo ) }</span>
						<span class="github-feed__separator" aria-hidden="true">&middot;</span>
						<span class="github-feed__state github-feed__state--${ escapeHtml(
							item.state
						) }">
							${ escapeHtml( capitalize( item.state ) ) }
						</span>
						${ item.closed_at ? `
							<span class="github-feed__separator" aria-hidden="true">&middot;</span>
							<time class="github-feed__date" datetime="${ escapeHtml( item.closed_at ) }">
								${ formatDate( item.closed_at ) }
							</time>
						` : '' }
					</div>
					${ item.labels && item.labels.length > 0 ? `
						<div class="github-feed__labels">
							${ item.labels
								.map(
									( label ) =>
										`<span class="github-feed__label feed-item__label">${ escapeHtml(
											label
										) }</span>`
								)
								.join( '' ) }
						</div>
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

function capitalize( str ) {
	if ( typeof str !== 'string' ) return '';
	return str.charAt( 0 ).toUpperCase() + str.slice( 1 );
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
