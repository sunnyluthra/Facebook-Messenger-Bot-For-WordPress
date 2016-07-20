<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class GMB_Endpoints {
	function __construct() {
		$this->rest_base = '/start';
		add_action('rest_api_init', array($this, 'register_routes'), 11);
	}
	function register_routes() {
		register_rest_route(GMB()->namespace, $this->rest_base, array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array($this, 'get_req'),
			),
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array($this, 'post_req'),
			),
		)
		);
	}

	function get_req($request) {
		$fromFB = $request->get_query_params();
		if ($fromFB['hub_verify_token'] === GMB()->fields->get_option('verify_token')) {
			echo $fromFB['hub_challenge'];
		} else {
			echo 'what';
		}
		die();
	}

	function post_req($request) {
		$params = $request->get_params();
		if ($params && $params['entry']) {
			foreach ((array) $params['entry'] as $entry) {
				if ($entry && $entry['messaging']) {
					foreach ((array) $entry['messaging'] as $message) {
						if (isset($message['message']['text']) || isset($message['postback'])) {
							GMB()->users->init($message);
							GMB()->messenger->init($message);
						}
					}
				}
			}
		}
		die();
	}
}