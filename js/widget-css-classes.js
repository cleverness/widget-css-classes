/**
 * Widget CSS Classes Plugin
 *
 * @author C.M. Kendrick <cindy@cleverness.org>
 * @package widget-css-classes
 * @version 1.5.0
 */

jQuery( document ).ready( function ( $ ) {

	// Change opacity if predefined classes is disabled.
	$( 'input.wcssc_type' ).on( 'change', function() {
		var val = $(this).val();
		if ( '2' === val || '3' === val ) {
			$('.wcssc_defined_classes').parents('tr').css({'opacity':''});
		} else {
			$('.wcssc_defined_classes').parents('tr').css({'opacity':'.5'});
		}
	} ).filter(':checked').trigger('change');

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