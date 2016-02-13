jQuery( document ).ready( function ( $ ) {
	'use strict';

	// Read/Write cookies.
	var cookieDeployed = $.cookie( "dx_deploy_timer_cooke" );
	var cookieNotification = $.cookie( "dx_deploy_timer_notification" );

	console.log( cookieNotification );

	/**
	 * @return {Boolean}
	 */
	function is_modalSeen() {

	}

	function cookieCreate() {
		$.cookie( "dx_deploy_timer_notification", 0 );
	}

	function cookieReset() {

	}
});