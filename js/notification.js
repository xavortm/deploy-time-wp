jQuery( document ).ready( function ( $ ) {
	'use strict';

	var time = Math.floor(new Date().getTime() / 1000);

	if($.cookie( "dx_deploy_cookie" ) == "visible") {
		$('.dxdeploy-deploy-notification').addClass("is-visible");
	}

	$(".dxdeploy-deploy-notification .button-ok").on("click", function() {
		$.cookie("dx_deploy_cookie", time);
		$('.dxdeploy-deploy-notification').removeClass("is-visible");
	});

});