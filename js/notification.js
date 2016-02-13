jQuery( document ).ready( function ( $ ) {
	'use strict';

	// Read/Write cookies.
	var cookieDeployed = $.cookie( "dx_deploy_cookie" );

	$(".dxdeploy-deploy-notification .button-ok").on("click", function() {
		$.cookie("dx_deploy_cookie", 0);
		$('.dxdeploy-deploy-notification').removeClass("is-visible");
	});
});