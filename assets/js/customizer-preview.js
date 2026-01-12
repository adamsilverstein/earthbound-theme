/**
 * Customizer preview script.
 *
 * @package Earthbound
 */

( function( $ ) {
	'use strict';

	// GitHub username preview.
	wp.customize( 'earthbound_github_username', function( value ) {
		value.bind( function( newval ) {
			// Username changes require a full refresh to reload data.
			wp.customize.preview.send( 'refresh' );
		} );
	} );

	// Trac username preview.
	wp.customize( 'earthbound_trac_username', function( value ) {
		value.bind( function( newval ) {
			// Username changes require a full refresh to reload data.
			wp.customize.preview.send( 'refresh' );
		} );
	} );

	// Cache duration preview (no live preview needed).
	wp.customize( 'earthbound_cache_duration', function( value ) {
		value.bind( function( newval ) {
			// Cache duration changes don't need a preview update.
		} );
	} );

	// Items per page preview.
	wp.customize( 'earthbound_items_per_page', function( value ) {
		value.bind( function( newval ) {
			// Items per page changes require a full refresh.
			wp.customize.preview.send( 'refresh' );
		} );
	} );

}( jQuery ) );
