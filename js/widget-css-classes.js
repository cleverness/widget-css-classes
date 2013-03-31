jQuery( document ).ready( function ( $ ) {

	$( '.wcssc_copy' ).relCopy( {} );

	$( 'p' ).on( 'click', '.wcssc_remove', function(e) {
		e.preventDefault();
		$( this ).parent().slideUp( function () {
			$( this ).remove();
		} );
	} );

} );