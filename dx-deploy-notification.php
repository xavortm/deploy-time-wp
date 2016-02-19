<?php
/*
 * Plugin Name: DX Deploy Notification
 * Description: Display when was the last update on the server file. The plugin is controlled via WP_CLI.
 * Version: 1.0.0
 * Author: DevriX
 * License: GPL2
 */

class DX_Deploy_Notifications {

	private $message = "";
	private $message_type = "info";
	private $cookie_name = "dx_deploy_notification";
	private $display_notification = false;

	/**
	 * Load the needed for showing the messages to the frontend.
	 *
	 * @since  v1.0.0
	 */
	public function __construct() {

		// Load the base scripts needed for notifying the users.
		add_action( 'wp_footer', array( $this, 'register_scripts' ) );

		add_action( 'init', array( $this, "set_cookies" ) );

		// Show the notification window in the footer.
		add_action( 'wp_footer', array( $this, "display_message" ) );

		// Store the display marker.
		add_option( "dx_deploy_cookie_time" );

	}

	public function deployed( $message = '', $current_time ) {

		// Use the new message
		$this->message = sanitize_text_field( $message );

		//  Add the action of creating new cookie
		$this->set_option( $message, $current_time );

	}

	/**
	 * The problem:
	 *
	 * I can't set a flag or cookie on wp msg command. I am setting
	 * option to true, but i am never resetting this option, so it
	 * seems useless.
	 *
	 * Then I have the cookies. They have 3 values atm:
	 *   visible 	Show the cookie.
	 *   init 		Default value when cookie is not set
	 *   hide 		After having wp msg content "" command and "hide"
	 * 				button is pressed.
	 *
	 * I don't know how to toggle the cookies when a command is sent
	 * in a way to make it work for all users. Using options will work
	 * globally, so its not ideal.
	 */
	public function set_cookies() {

		// See of wp cli command has been given.
		$options = get_option( "dx_deploy_cookie_time" );

		// To eliminate PHP errors/warnings.
		if ( empty( $options["current_time"] ) ) {
			return;
		}

		// If no cookies have been set, return.
		if ( false === $this->check_cookies() ) {
			return;
		}

		if ( $options["current_time"] >= $_COOKIE["dx_deploy_cookie"] ) {
			setcookie( "dx_deploy_cookie", $options["current_time"] );
			$this->display_notification = true;
		} else 	{
			$this->display_notification = false;
		}

	}

	private function check_cookies() {
		if ( ! isset( $_COOKIE["dx_deploy_cookie"] ) ) {
			setcookie( "dx_deploy_cookie", "" );
			return false;
		}

		return true;
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

	private function set_option( $message, $current_time ) {
		update_option( "dx_deploy_cookie_time", array(
			"message" => $message,
			"current_time" => $current_time
		) );
	}

	public function clear() {
		update_option( "dx_deploy_cookie_time", array(
			"time" => 0
		) );

		unset($_COOKIE["dx_deploy_cookie"]);
	}

	/**
	 * Deisplay the deploy me notification to the frontend.
	 *
	 * @return string
	 * @since  v1.0.0
	 */
	public function display_message() {

		// Stored data from deploy command in wp_cli
		$message_data 	= get_option( 'dx_deploy_cookie_time' );
		$display_class = '';

		if ( true === $this->display_notification ) {
			$display_class = 'is-visible';
		}

		if ( empty( $message_data ) ) {
			return;
		}

		$message_content 	= $message_data["message"];
		$message_time 		= date( 'd M Y - [G:i:s] P e', $message_data["current_time"] );

		$output  = "<div class='dxdeploy-deploy-notification {$display_class}'>";
		$output .= "<h2 class='dxdeploy-title'>Note!</h2>";
		$output .= "<p class='dxdeploy-message'>{$message_content}</p>";
		$output .= "<span class='timestamp'>{$message_time}</span>";
		$output .= "<span class='button-ok'>Mark as seen.</span>";
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
		 * wp msg content "New mobile menu functionality"
		 *
		 * @synopsis <message>
		 */
		function content( $args, $assoc_args ) {

			// Grab the message string
			list( $message ) = $args;
			$current_time = time();

			// Send the data to the notifications class. It will deal with presenting
			// it to the user.
			$DX_Deploy_Notifications = new DX_Deploy_Notifications();
			$DX_Deploy_Notifications->deployed( $message, $current_time );

			// Print the success message.
			WP_CLI::success( "Visitors will be notified for a new deployment. <$message>" );
		}

		/**
		 * Clear the notifications from the front-end
		 *
		 * ## EXAMPLES
		 *
		 * wp msg clear
		 */
		function clear( $args, $assoc_args ) {

			// Send the data to the notifications class. It will deal with presenting
			// it to the user.
			$DX_Deploy_Notifications = new DX_Deploy_Notifications();
			$DX_Deploy_Notifications->clear();

			WP_CLI::success( "Notifications have been removed." );
		}
	}

	// Register the new command to WP_CLI
	WP_CLI::add_command( 'msg', 'Deploy_Me_CLI' );

}

$DX_Deploy_Notifications = new DX_Deploy_Notifications();