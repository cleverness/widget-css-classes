jQuery( document ).ready( function ( $ ) {

	$( '.wcssc_copy' ).relCopy( {} );

	$( '.wcssc_remove' ).live( 'click', function(e) {
		e.preventDefault();
		$( this ).parent().slideUp( function () {
			$( this ).remove();
		} );
	} );

} );