<?php
/*
 * Plugin Name: Deploy Notification
 * Description: Display when was the last update on the server file. The plugin is controlled via WP_CLI.
 * Version: 1.0.0
 * Author: DevriX
 * License: GPL2
 */

class DX_Deploy_Notifications {

	private $message = "";
	private $message_type = "info";
	private $cookie_name = "dx_deploy_notification";

	/**
	 * Load the needed for showing the messages to the frontend.
	 *
	 * @since  v1.0.0
	 */
	public function __construct() {

		// Load the base scripts needed for notifying the users.
		add_action( 'wp_footer', array( $this, 'register_scripts' ) );

		// Show the notification window in the footer.
		add_action( 'wp_footer', array( $this, "display_message" ) );
	}

	public function deployed( $message = '', $type = 'info' ) {

		// Use the new message
		$this->message = sanitize_text_field( $message );
		$this->type = sanitize_text_field( $type );

		//  Add the action of creating new cookie
		$this->set_cookie();
	}

	/**
	 * Register scritps and styles.
	 *
	 * @return void
	 * @since  v1.0.0
	 */
	public function register_scripts() {
		wp_enqueue_script 	( 'deploy-time-cookies', plugins_url( 'js/jquery.cookie.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script 	( 'deploy-time-notification', plugins_url( 'js/notification.js', __FILE__ ), array( 'jquery' ) ) ;
		wp_enqueue_style 	( 'deploy-time-styling', plugins_url( 'css/notifications.css', __FILE__ ) );
	}

	private function set_cookie() {
		setcookie( $this->cookie_name, $this->message, time() + ( 86400 * 7 ) );
	}

	/**
	 * Deisplay the deploy me notification to the frontend.
	 *
	 * @return string
	 * @since  v1.0.0
	 */
	public function display_message() {

		$message_type = $this->message_type;
		$message = $this->message;

		if ( isset( $_COOKIE[$this->cookie_name] ) ) {
			$cookie = $_COOKIE[$this->cookie_name];
		} else {
			$cookie = false;
		}

		var_dump($_COOKIE);

		$output  = "<div class='dxdeploy-deploy-notification is-hidden {$message_type}'>";
		$output .= "<h2 class='dxdeploy-title'>Note!</h2>";
		$output .= "<p class='dxdeploy-message'>{$message}</p>";
		$output .= "</div>";

		echo $output;

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
		 * : Custom deploy message to be displayed.
		 *
		 * ## EXAMPLES
		 *
		 * wp deployme deployed "New mobile menu functionality"
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

		/**
		 * Display permanent message.
		 *
		 * ## OPTIONS
		 *
		 * <message>
		 * : Informative message to be displayed.
		 *
		 * ## EXAMPLES
		 *
		 * wp deployme info "New mobile menu functionality"
		 *
		 * @synopsis <message>
		 */
		function info( $args, $assoc_args ) {

			// Grab the message string
			list( $message ) = $args;

			// Send the data to the notifications class. It will deal with presenting
			// it to the user.
			$DX_Deploy_Notifications = new DX_Deploy_Notifications();
			$DX_Deploy_Notifications->deployed( $message );

			// Print the success message.
			WP_CLI::success( "Visitors will be notified with your message. <$message>" );
		}
	}

	// Register the new command to WP_CLI
	WP_CLI::add_command( 'deployme', 'Deploy_Me_CLI' );

}

$DX_Deploy_Notifications = new DX_Deploy_Notifications();