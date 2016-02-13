jQuery( document ).ready( function ( $ ) {
	'use strict';

	if($.cookie( "dx_deploy_cookie" ) == 1) {
		$('.dxdeploy-deploy-notification').addClass("is-visible")
	}

	$(".dxdeploy-deploy-notification .button-ok").on("click", function() {
		$.cookie("dx_deploy_cookie", 0);
		$('.dxdeploy-deploy-notification').removeClass("is-visible");
	});
});