<?php
/*
 * Plugin Name: Deploy Date
 * Description: Display when was the last update on the server files. It checks for wp-content folder only. Refresh rate is 20 minutes, because otherways it takes too much time to check all files.
 * Version: 1.0.0
 * Stable tag: 1.0.0
 * Author: DevriX
 * License: GPL2
 */

add_action( 'wp_footer', 'dx_write_deploy_date' );

dx_deploy_time_init();

function dx_deploy_time_init() {
	if( isset( $_COOKIE['dx_deploy_timer_co§oke'] ) ) {
		dx_has_cookie();
	} else {
		$message = get_date_mod();
		$date = dx_no_cookie( $message );
	}
}

/**
 * If it has, then print it.
 */
function dx_has_cookie() {
	do_action('dx_write_deploy_date', $_COOKIE['dx_deploy_timer_cooke']);
}

/**
 * If no cookie is created then make new one. This should be run only the first
 * time you visit the site after the cookies are expired.
 */
function dx_no_cookie( $mesage ) {
	setcookie('dx_deploy_timer_cooke', $mesage, time() + ( 20 * 60 ), "/"); // Check every hour
}

/**
 * Insert the date it was last deployed
 */
function dx_write_deploy_date() {

	// And some inline style. Its not needed to hook it in wp_head at all for just 4-5 properties.
	$style = "position:fixed; bottom:0; right:0; display: block; padding: 0px 2px; font-family: 'Courier New'; font-size: 10px; margin: 0; background: black; color: white;line-height:1em";

    // Print the end result
    if( isset( $_COOKIE['dx_deploy_timer_cooke'] ) ) {
		echo '<p class="deploy-date" style="'.$style.'">Deployed: '.$_COOKIE['dx_deploy_timer_cooke'].'</p>';
	} else {
		$cur_date = get_date_mod();
		echo '<p class="deploy-date" style="'.$style.'; background: blue">Deployed: '.$cur_date.' (Cookies updated)</p>';
	}
}


// Read the last modified file.
function get_date_mod() {
    $output = "";
    $cursor = -1;
    $char = '';
    $line = '';

    // "edited_files.txt"
    $file_name = plugin_dir_url( __FILE__ ) . "edited_files.txt";

    // Return if the file does not exist.
    if ( ! file_exists( $file_name ) ) {
        return "ERROR: Unable to open $file_name";
    }

    $file = fopen( $file_name, 'r' ) or die( "Unable to open file" );

    fseek( $file, $cursor, SEEK_END );
    $char = fgetc( $file );

    while ( $char === "\n" || $char === "\r" ) {
		// fseek( $file, $cursor--, SEEK_END );
		$char = fgetc( $file );
	}

	while ($char !== false && $char !== "\n" && $char !== "\r") {
	    $line = $char . $line;
	    // fseek( $file, $cursor--, SEEK_END );
	    $char = fgetc( $file );
	}

	$output = $line;

    // End work with the log file.
    fclose( $file );
	return $output;
}