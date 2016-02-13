<?php
/*
 * Plugin Name: Deploy Notification
 * Description: Display when was the last update on the server file. The plugin is controlled via WP_CLI.
 * Version: 1.1.0
 * Author: DevriX
 * License: GPL2
 */

class DX_Deploy_Notifications {

	private $message = "";
	private $message_general = "New deployment.";

	public function __construct() {

		// Load the base scripts needed for notifying the users.
		add_action( 'wp_footer', 'dx_notification_script' );
	}

	private function set_message( $message ) {
		$this->$message = sanitize_text_field( $message );
	}

	public function deployed( $message = '' ) {

		// Use the new message
		$this->set_message( $message );

		//  Add the action of creating new cookie
		add_action( 'init', array( $this, "set_cookie" ) );
	}


	public function register_scripts() {
		wp_enqueue_script 	( 'deploy-time-cookies', plugins_url( 'js/jquery.cookie.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script 	( 'deploy-time-notification', plugins_url( 'js/notification.js', __FILE__ ), array( 'jquery' ) ) ;
		wp_enqueue_style 	( 'deploy-time-styling', plugins_url( 'css/notifications.css', __FILE__ ) );
	}

}


if( defined( 'WP_CLI' ) && WP_CLI ) {

	/**
	 * Notify users that there has been deployment to the server.
	 */
	class Deploy_Me_CLI extends WP_CLI_Command {

		/**
		 * Display notification that there has been deployment.
		 *
		 * ## OPTIONS
		 *
		 * <message>
		 * : The mesage to be displayed.
		 *
		 * ## EXAMPLES
		 *
		 * wp dxd deployed "New mobile menu functionality"
		 *
		 * @synopsis <message>
		 */
		function deployed( $args, $assoc_args ) {

			// Grab the message string
			list( $message ) = $args;

			// Send the data to the notifications class. It will deal with presenting
			// it to the user.
			$DX_Deploy_Notifications = new DX_Deploy_Notifications();
			$DX_Deploy_Notifications->deployed( $message );

			// Print the success message.
			WP_CLI::success( "Visitors will be notified for a new deployment. <$message>" );
		}
	}

	// Register the new command to WP_CLI
	WP_CLI::add_command( 'deployme', 'Deploy_Me_CLI' );

}