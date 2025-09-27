<?php
/**
*	Plugin Name: Vulnerable proxy (SSRF Demo)
*	Description: Deliberately vulnerable SSRF endpoint for education. DO NOT USE IN PRODUCTION.
*	Version: 0.0.1
*/

if (! defined('ABSPATH')) { exit; }	// proper uri access

add_action('wp_ajax_nopriv_vp_proxy', 'vp_proxy_handler');	// when unauthenticated user visits vp proxy
add_action('wp_ajax_vp_proxy', 'vp_proxy_handler');		// when a logged in user enters vp proxy

function vp_proxy_handler() {					// action that will fire

	$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';	// check if url is not null

	if (empty ($url)) {
		status_header(400);
		echo "missing url parameter";
		exit;
	}
						// intentionally no validation
	$response = wp_remote_get($url, [
		'timeout'=>8,		// secs before fail
		'redirection'=>5,	// # of fails
	]);

	if(is_wp_error($response)) {		// if the retrieved response above is an error
		status_header(502);
		echo "Error: " . $response->get_error_message();
		exit;
	}
	$code = wp_remote_retrieve_response_code($response);	// http request code (ex 101, 200, 301, 404, 503)

	status_header($code);

	echo wp_remote_retrieve_body($response);		// dump data

	exit;

}	// No validation of retrieved response besides error handling (really bad)
