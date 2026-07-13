<?php
/**
 * Security and Performance Optimization
 *
 * @package JerseyPlug
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. HONEYPOT VALIDATION FOR WOOCOMMERCE REGISTRATION
 */
add_action( 'woocommerce_register_post', 'jerseyplug_validate_honeypot', 10, 3 );
function jerseyplug_validate_honeypot( $username, $email, $validation_errors ) {
	if ( isset( $_POST['trap_email'] ) && ! empty( $_POST['trap_email'] ) ) {
		// If honeypot field is filled, it's a bot. Add a generic error to stop registration.
		$validation_errors->add( 'registration-error-invalid-data', __( 'Registration failed due to suspicious activity. Please try again.', 'woocommerce' ) );
	}
}

/**
 * 2. RATE LIMITING FOR LOGIN (Brute-force Protection)
 */
add_action( 'wp_authenticate', 'jerseyplug_check_login_attempts', 10, 2 );
function jerseyplug_check_login_attempts( $username, $password ) {
	// Skip if empty (will be caught by WP anyway)
	if ( empty( $username ) || empty( $password ) ) {
		return;
	}

	$ip = $_SERVER['REMOTE_ADDR'];
	$transient_name = 'login_attempts_' . md5( $ip );
	$attempts = get_transient( $transient_name );

	// Block if 5 or more failed attempts
	if ( $attempts && $attempts >= 5 ) {
		wp_die( 
			__( '<strong>ERROR</strong>: Too many failed login attempts. Please try again in 15 minutes.', 'woocommerce' ), 
			__( 'Rate Limited', 'woocommerce' ), 
			array( 'response' => 429 ) 
		);
	}
}

add_action( 'wp_login_failed', 'jerseyplug_log_failed_attempt' );
function jerseyplug_log_failed_attempt( $username ) {
	$ip = $_SERVER['REMOTE_ADDR'];
	$transient_name = 'login_attempts_' . md5( $ip );
	$attempts = get_transient( $transient_name );

	if ( false === $attempts ) {
		$attempts = 0;
	}

	$attempts++;
	
	// Set transient to expire in 15 minutes (900 seconds)
	set_transient( $transient_name, $attempts, 15 * MINUTE_IN_SECONDS );
}

add_action( 'wp_login', 'jerseyplug_clear_login_attempts', 10, 2 );
function jerseyplug_clear_login_attempts( $user_login, $user ) {
	$ip = $_SERVER['REMOTE_ADDR'];
	$transient_name = 'login_attempts_' . md5( $ip );
	delete_transient( $transient_name );
}

/**
 * 3. OPTIMIZE CART FRAGMENTS ON ACCOUNT PAGE
 */
add_action( 'wp_enqueue_scripts', 'jerseyplug_dequeue_cart_fragments', 99 );
function jerseyplug_dequeue_cart_fragments() {
	// If the user is on the account page and NOT logged in, we don't need cart fragments yet
	if ( function_exists( 'is_account_page' ) && is_account_page() && ! is_user_logged_in() ) {
		wp_dequeue_script( 'wc-cart-fragments' );
	}
}
