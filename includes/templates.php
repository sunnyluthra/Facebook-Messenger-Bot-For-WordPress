<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class GMB_Templates {
	private $template;
	function template($response) {
		if (method_exists($this, $response['template'])) {
			$this->template = $this->$response['template']($response);
			return $this->template;
		}
	}

	function text($response) {
		$text = $response['text_template'][0]['text'];
		$template = ['text' => $this->truncate($this->format_text($text), 300)];
		return $template;
	}

	function image($response) {
		$template = [
			'attachment' => [
				'type' => 'image',
				'payload' => [
					'url' => $response['image_template'][0]['image'],
				],
			],
		];
		return $template;
	}

	function audio($response) {
		$template = [
			'attachment' => [
				'type' => 'audio',
				'payload' => [
					'url' => $response['audio_template'][0]['audio'],
				],
			],
		];
		return $template;
	}
	function video($response) {
		$template = [
			'attachment' => [
				'type' => 'video',
				'payload' => [
					'url' => $response['video_template'][0]['video'],
				],
			],
		];
		return $template;
	}
	function file($response) {
		$template = [
			'attachment' => [
				'type' => 'file',
				'payload' => [
					'url' => $response['file_template'][0]['file'],
				],
			],
		];
		return $template;
	}
	function button($response) {
		$buttons_data = $response['button_template'][0];
		return $this->button_template($buttons_data);

	}
	function button_template($data) {
		$template = [
			'attachment' => [
				'type' => 'template',
				'payload' => [
					'template_type' => 'button',
					'text' => $this->truncate($this->format_text($data['text']), 300),
					'buttons' => $this->buttons_array($data['buttons']),
				],
			],
		];
		return $template;
	}
	function generic($response) {
		$elements_data = $response['generic_template'];
		$template = [
			'attachment' => [
				'type' => 'template',
				'payload' => [
					'template_type' => 'generic',
					'elements' => $this->elements_array($elements_data),
				],
			],
		];
		return $template;
	}
	function elements_array($elements) {
		$e = [];
		foreach ($elements as $element) {
			$_e = array();
			if (!empty($element['image'])) {
				$_e['image_url'] = $element['image'];
			}
			$_e['title'] = $this->truncate($element['title'], 45);
			$_e['subtitle'] = $this->truncate($element['subtitle'], 80);
			$buttons = $this->buttons_array($element['buttons']);
			$_e['buttons'] = $buttons;
			$e[] = $_e;
		}
		return $e;
	}
	function buttons_array($buttons) {
		$b = [];
		foreach ($buttons as $button) {
			$_b = array();
			$_b['title'] = $button['title'];
			$_b['type'] = $button['type'];

			if ($button['type'] === 'web_url') {
				$_b['url'] = $this->format_url($button['url_or_postback']);
			} else {
				$_b['payload'] = $button['url_or_postback'];
			}
			$b[] = $_b;
		}
		// print_r($b);
		return $b;
	}
	function truncate($text, $length) {
		$length = abs((int) $length);
		$text = trim(preg_replace("/&#?[a-z0-9]{2,8};/i", "", $text));
		if (strlen($text) > $length) {
			$text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
		}
		return ($text);
	}
	function format_text($text) {
		$search = array_keys(GMB()->users->user);
		$replace = array_values(GMB()->users->user);
		foreach ($search as &$key) {
			$key = '%user_' . $key . '%';
		}
		unset($key);
		$text = str_replace($search, $replace, $text);
		return $text;
	}
	function format_url($url) {

		return $url;
	}
}