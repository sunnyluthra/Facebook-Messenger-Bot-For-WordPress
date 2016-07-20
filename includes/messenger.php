<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}
class GMB_Messenger {
	public $templates = [];
	private $message;
	function init($data = false) {
		$this->message = $data;
		$this->process();
	}
	function user_said() {
		if (isset($this->message['postback']['payload'])) {
			$user_said = $this->message['postback']['payload'];
		} else if (isset($this->message['message']['text'])) {
			$user_said = $this->message['message']['text'];
		}
		$this->user_said = strtolower($user_said);
		return $this->user_said;
	}
	function process() {
		$this->user_said();
		$this->is_404 = true;

		$response = $this->get_responses();
		if ($response) {
			if ($response['response_method'] === 'normal') {
				$this->templates[] = GMB()->templates->template($response);
			} else if ($response['response_method'] === 'data') {
				GMB()->data_source->command['attr'] = $response['data_attr'][0];
				GMB()->data_source->process();
			}
		}
		if (isset($this->message['postback']['payload'])) {
			$ds_postbacks = strpos($this->user_said, '_ds_');
			if ($ds_postbacks === 0) {
				$is_ds_postback = true;
				GMB()->data_source->postback($this->user_said);
			}
		}
		$this->send(GMB()->users->user['username']);
	}

	function send($fb_user_id) {

		$templates = $this->templates;
		if (empty($templates)) {
			return;
		}
		$fb_url = GMB()->fb_message_url . GMB()->fields->get_option('access_token');
		foreach ($templates as $template) {
			// print_r($template);
			$data = array(
				'body' => array(
					"recipient" => array("id" => $fb_user_id),
					"message" => $template,
				),
			);
			$response[] = wp_remote_post($fb_url, $data);
		}
		return $response;
	}

	function get_responses() {
		$user_says = $this->user_said;
		if ($user_says) {
			$args = array(
				'fields' => 'ids',
				'posts_per_page' => 1,
				'post_type' => 'response',
				'tax_query' => array(
					array(
						'taxonomy' => 'user-say',
						'field' => 'name',
						'terms' => $user_says,
					),
				),
			);
			$responses = get_posts($args);
			if ($responses) {
				$response_id = $responses[0];
				$response = array(
					'response_method' => GMB()->fields->get_post_meta($response_id, 'response_method'),
					'data_attr' => GMB()->fields->get_post_meta($response_id, 'data_attr', 'complex'),
					'template' => GMB()->fields->get_post_meta($response_id, 'template'),
					'text_template' => GMB()->fields->get_post_meta($response_id, 'text_template', 'complex'),
					'image_template' => GMB()->fields->get_post_meta($response_id, 'image_template', 'complex'),
					'audio_template' => GMB()->fields->get_post_meta($response_id, 'audio_template', 'complex'),
					'video_template' => GMB()->fields->get_post_meta($response_id, 'video_template', 'complex'),
					'file_template' => GMB()->fields->get_post_meta($response_id, 'file_template', 'complex'),
					'button_template' => GMB()->fields->get_post_meta($response_id, 'button_template', 'complex'),
					'generic_template' => GMB()->fields->get_post_meta($response_id, 'generic_template', 'complex'),
				);

				return $response;
			}
		}
	}
}