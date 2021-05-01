<?php
/**
 * Plugin Name: Custom Login
 * Description: Custom login plugin for WP.
 * Plugin URI: 
 * Author: Jesse
 * Author URI: 
 * Version: 1.0.0
 * License: GPL2
 * Text Domain: custom-login
 * 
 */

/**
 * Enqueue scripts
 *
 * == main.js ==  This file adds client side checking to our login and register pages.
 */


function jquery_validate_scripts() {
    wp_enqueue_script( 'jquery-validate', plugins_url() . '/custom-login/js/jquery.validate.min.js', false, false, false );
}
add_action( 'wp_enqueue_scripts', 'jquery_validate_scripts' );
function custom_enqueue_scripts() {
    wp_enqueue_script( 'main-js', plugins_url() . '/custom-login/js/main.js', array( 'jquery' ), false, false );
}
add_action( 'wp_enqueue_scripts', 'custom_enqueue_scripts' );
// Define constants so we can use them as shortcuts
define("SHORTCODES_PATH", plugin_dir_path( __FILE__ ) . 'shortcodes');
define("FUNCTIONS_PATH", plugin_dir_path( __FILE__ ) . 'functions');
// Include the required Files
require_once SHORTCODES_PATH . '/login-shortcode.php';
require_once FUNCTIONS_PATH . '/login-functions.php';
//require_once FUNCTIONS_PATH . '/custom-login-functions.php';

add_action( 'wp_enqueue_scripts', 'so_enqueue_scripts' );
function so_enqueue_scripts(){
  wp_register_script( 
    'ajaxHandle', 
    plugins_url() . '/custom-login/js/main.js', 
    array(), 
    false, 
    true 
  );
  wp_enqueue_script( 'ajaxHandle' );
  wp_localize_script( 
    'ajaxHandle', 
    'ajax_object', 
    array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) 
  );
}

add_action( "wp_ajax_myaction", "process_custom_login_form" );
add_action( "wp_ajax_nopriv_myaction", "process_custom_login_form" );
/*
function process_custom_login_form() {
	
    $name = $_POST['custom_user'];
    $pass = $_POST['custom_pass'];

    echo json_encode(array('loggedin'=>true, 'message'=>__($name)));

    wp_die(); // ajax call must die to avoid trailing 0 in your response
}
*/

function process_custom_login_form() {
    // Validate the username or email field before submitting
    if ( isset($_POST['custom_user'])) {
        $custom_user =  trim($_POST['custom_user']);
    }
    
    // Validate password field before submitting
    if ( isset($_POST['custom_pass']) ) {
        $custom_pass = trim($_POST['custom_pass']);
    }
    if( wp_verify_nonce( (isset($_POST['custom_login_nonce']) ? $_POST['custom_login_nonce'] : ''), 'custom-login-nonce' ) ) {
        $user_info = get_user_by( 'login', $custom_user );
        
        $user_id = $user_info->ID;
      
        if( isset($_POST['remember_me']) ) {
            $_POST['remember_me'] = true;
        } else {
            $_POST['remember_me'] = false;
        }

        process_custom_log_in_user( $custom_user, $custom_pass, $_POST['remember_me'] );
    }
}

function process_custom_log_in_user( $username, $password, $remember ) {

	// Allow our return messages to be filtered.
	$email_login_fail_message = apply_filters( 'mm_ajax_login_email_login_fail_message', __( 'The e-mail address you entered does not match a current user.', 'mm-ajax-login' ) );
	$login_success_message = apply_filters( 'mm_ajax_login_success_message', __( 'Login successful, redirecting...', 'mm-ajax-login' ) );
	$login_fail_message = apply_filters( 'mm_ajax_login_fail_message', __( 'Wrong username or password.', 'mm-ajax-login' ) );

	$info = array();

	// Allow others to disable e-mail login.
	$allow_email_login = apply_filters( 'mm_ajax_login_allow_email_login', true );

	// Check whether we're using an e-mail or username to login.
	if ( is_email( $username ) && $allow_email_login ) {

		// We have an e-mail, so attempt to get the username
		$user = get_user_by( 'email', $username );

		if ( $user ) {

			// We have a matching user, so proceed with using their username.
			$username = $user->user_login;

		} else {

			$message = esc_html( $email_login_fail_message );

			// We don't have a matching user, so return a message.
			echo json_encode( array(
				'logged_in' => false,
				'message' => $message
			) );
		}
	}

    // Build the array of login credentials.
	$info['user_login'] = $username;
	$info['user_password'] = $password;
	$info['remember'] = $remember;

	// Sign in the user.
	$user_signin = wp_signon( $info, true );

	// Handle success or failure.
	if ( ! is_wp_error( $user_signin ) ) {

        //wp_set_auth_cookie( $username, $remember );

        $message = esc_html( $login_success_message );

		// Login success.
		echo json_encode( array(
			'logged_in' => true,
			'message' => $message
		) );

	    //wp_redirect(home_url());
		//do_action( 'wp_login', $username, get_user_by( 'id', $username ) );

	} else {

		$message = esc_html( $login_fail_message );

		// Login fail.
		echo json_encode( array(
			'logged_in' => false,
			'message' =>  $message
		) );
	}
    //die;
    wp_die();
}

?>