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
		$current_time = time();

		// Load the base scripts needed for notifying the users.
		add_action( 'wp_footer', array( $this, 'register_scripts' ) );

		// Show the notification window in the footer.
		add_action( 'wp_footer', array( $this, "display_message" ) );

		add_action( 'init', array( $this, "check_cookies" ) );

		$this->check_timings();

		// Store the display marker.
		add_option( "dx_deploy_cookie_time" );
		add_option( "dx_deploy_cookie_time_compare", $current_time );

	}

	public function deployed( $message = '', $type = 'info', $current_time ) {

		// Use the new message
		$this->message = sanitize_text_field( $message );
		$this->type = sanitize_text_field( $type );

		//  Add the action of creating new cookie
		$this->set_option( $message, $type, $current_time );

	}

	public function check_timings() {
		$comparison = get_option( "dx_deploy_cookie_time_compare" );
		$cookie_content = get_option( "dx_deploy_cookie_time" );
		$cookie_time = $cookie_content["current_time"];

		if ( $comparison != $cookie_time ) {
			setcookie("dx_deploy_cookie", 1);
			update_option( "dx_deploy_cookie_time_compare", $cookie_time );
		}
	}

	public function check_cookies() {
		if ( isset( $_COOKIE["dx_deploy_cookie"] ) && $_COOKIE["dx_deploy_cookie"] == 1 ) {
			$this->display_notification = true;
		}
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

	private function set_option( $message, $type, $current_time ) {
		update_option( "dx_deploy_cookie_time", array(
			"message" => $message,
			"type" => $type,
			"current_time" => $current_time )
		);
	}

	/**
	 * Deisplay the deploy me notification to the frontend.
	 *
	 * @return string
	 * @since  v1.0.0
	 */
	public function display_message() {
		$display_class = '';

		if ( $this->display_notification ) {
			$display_class = 'is-visible';
		}

		// Stored data from deploy command in wp_cli
		$message_data 		= get_option( 'dx_deploy_cookie_time' );
		$message_content 	= $message_data["message"];
		$message_type 		= $message_data["type"];
		$message_time 		= date( 'd M Y - [G:i:s] P e', $message_data["current_time"] );

		$output  = "<div class='dxdeploy-deploy-notification {$display_class} {$message_type}'>";
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
		 * wp deployme deployed "New mobile menu functionality"
		 *
		 * @synopsis <message>
		 */
		function deployed( $args, $assoc_args ) {

			// Grab the message string
			list( $message ) = $args;

			$current_time = time();

			// Send the data to the notifications class. It will deal with presenting
			// it to the user.
			$DX_Deploy_Notifications = new DX_Deploy_Notifications();
			$DX_Deploy_Notifications->deployed( $message, "deploy", $current_time );

			// Print the success message.
			WP_CLI::success( "Visitors will be notified for a new deployment. <$message>" );
		}
	}

	// Register the new command to WP_CLI
	WP_CLI::add_command( 'deployme', 'Deploy_Me_CLI' );

}

$DX_Deploy_Notifications = new DX_Deploy_Notifications();