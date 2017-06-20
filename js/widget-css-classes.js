/**
 * Widget CSS Classes Plugin
 *
 * @author C.M. Kendrick <cindy@cleverness.org>
 * @package widget-css-classes
 * @version 1.5.0
 */

jQuery( document ).ready( function ( $ ) {

	$( '.wcssc_copy' ).relCopy( {} );

	$( 'p' ).on( 'click', '.wcssc_remove', function(e) {
		e.preventDefault();
		$( this ).parent().slideUp( function () {
			$( this ).remove();
		} );
	} );

	if ( $.isFunction( $.fn.sortable ) ) {
		$('.wcssc_sortable .wcssc_sort').show();
		$('.wcssc_sortable').sortable({
			items: 'p:not(.wcssc_sort_fixed)',
			placeholder: 'wcssc_drop_placeholder'
		});
	}

} );