<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class GMB_Data_Source {
	public $command = [];
	function process() {
		$attr = $this->command['attr'];
		$permalink_button_title = $attr['permalink_button_title'] ? $attr['permalink_button_title'] : "View Story";
		$summary_button_title = $attr['summary_button_title'] ? $attr['summary_button_title'] : "Get Summary";
		$posts = $this->get();
		if ($posts) {
			$elements = [];
			global $post;
			foreach ($posts as $post) {
				setup_postdata($post);
				$title = get_the_title();
				$subtitle = strip_tags(get_the_excerpt());
				$permalink = get_the_permalink();

				if (has_post_thumbnail($post->ID)) {
					$image_url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'thumbnail');
				}

				$data = array(
					"title" => $title,
					"subtitle" => $subtitle,
					"buttons" => array(),
				);
				if ($image_url) {

				}
				$buttons = [];
				$buttons[] = array(
					"type" => "web_url",
					"url_or_postback" => $permalink,
					"title" => $permalink_button_title,
				);
				if ($attr['want_summary_button'] === 'yes') {
					$buttons[] = array(
						"type" => "postback",
						"url_or_postback" => "_ds_summary_" . $post->ID,
						"title" => $summary_button_title,
					);
				}

				$data['buttons'] = $buttons;
				$elements[] = $data;
			}
			wp_reset_postdata();

			$response = [];
			$response['generic_template'] = $elements;
			GMB()->messenger->templates[] = GMB()->templates->generic($response);
		} else {
			$text = array('text' => 'Sorry we don\'t have anything to show.');
			GMB()->messenger->templates[] = GMB()->templates->text($text);
		}

	}
	function get() {
		$attr = $this->command['attr'];

		$args = array(
			'posts_per_page' => $attr['number_of_posts'],
			'offset' => 0,
			'orderby' => 'date',
			'order' => 'DESC',
			'post_type' => 'post',
			'post_status' => 'publish',
		);
		$posts = $this->get_posts($args);
		return $posts;
	}
	function get_posts($args) {
		$posts = get_posts($args);
		return $posts;
	}
	function postback($postback) {
		$postback = str_replace("_ds_", "", $postback);
		$args = explode("_", $postback);
		$command = $args[0];
		switch ($command) {
			case "summary":
				$this->summary($args);
				break;
			case "posts":
				$this->command['args'] = $args[1];
				$this->process();
				break;
		}
	}
	function summary($args) {
		$post_id = $args[1];
		global $post;
		$post = get_post($post_id);
		if ($post->post_status !== 'publish' || $post->post_type !== 'post') {
			return;
		}
		setup_postdata($post);
		$summary = strip_tags(get_the_excerpt());
		$permalink = get_the_permalink();
		$permalink_button_title = "Read More";
		$response = [];
		$response['text'] = $summary;

		$buttons = [];
		$buttons[] = array(
			"type" => "web_url",
			"url" => $permalink,
			"title" => $permalink_button_title,
		);
		$response['buttons'] = $buttons;
		GMB()->messenger->templates[] = GMB()->templates->button_template($response);
	}

}