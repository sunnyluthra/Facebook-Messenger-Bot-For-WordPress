<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class GMB_Users {

	function __construct() {

	}
	function init($message) {
		$this->message = $message;
		$this->set_user();
	}
	function user_prefixed($user_id) {
		return GMB()->prefix . $user_id;
	}
	function set_user() {
		$user_id = $this->fb_get_user_id();
		$user = get_user_by('login', $user_id);
		if (!$user) {
			$user = $this->create_user($user_id);
		}
		if ($user) {

			$user = (object) $user;
			$user_data = array(
				'id' => $user->ID,
				'username' => $user->user_login,
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
			);
			$this->user = $user_data;
			// $this->user_reset();
			return;
		}
		return false;
	}
	function fb_get_user_id() {
		return isset($this->message['sender']['id']) ? $this->message['sender']['id'] : false;
	}

	function create_user($user_id) {
		$url = str_replace(array('<USER_ID>', '<PAGE_ACCESS_TOKEN>'), array($user_id, TBM()->bot->page_token), TBM()->fb_user_url);
		$user = wp_remote_get($url);

		if (is_wp_error($user) || $user['response']['code'] !== 200) {

			//there was error in getting user from fb.
			$user = array(
				'username' => $user_id,
				'first_name' => $user_id,
				'last_name' => $user_id,
			);

		} else {
			$user = json_decode($user['body'], true);
			$user['username'] = $user_id;
		}
		return $this->_create_user($user);
	}
	function _create_user($user, $fbdata = false) {
		$user_id = username_exists($user['username']);
		if ($user_id) {
			return;
		}
		$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
		$user_data = array(
			"user_pass" => $random_password,
			"user_login" => $user['username'],
			"first_name" => $user['first_name'],
			"last_name" => $user['last_name'],
		);

		$user_id = wp_insert_user($user_data);
		if (!is_wp_error($user_id)) {
			add_user_meta($user_id, GMB()->prefix . 'subscriber', true);
			add_user_meta($user_id, GMB()->prefix . 'user', true);
		}
		$user_data['ID'] = $user_id;
		return $user_data;
	}

}