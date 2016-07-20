<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}
use Carbon_Fields\Container;
use Carbon_Fields\Field;

class GMB_Fields {
	function __construct() {
		add_action('carbon_register_fields', array($this, 'meta_boxes'));
	}
	function get_post_meta($id, $name, $type = null) {
		return carbon_get_post_meta($id, GMB()->prefix . $name, $type);
	}
	function get_option($name, $type = null) {
		return carbon_get_theme_option(GMB()->prefix . $name, $type);
	}
	function meta_boxes() {
		Container::make('post_meta', 'Response Settings')
			->show_on_post_type('response')
			->add_fields(array(
				Field::make("radio", GMB()->prefix . "response_method", "Response Method")
					->help_text('You can select how you want to respond to a response. Template will send a template created by you and Data will send latest posts of the post type you selected.')
					->add_options(array(
						'normal' => 'Normal',
						'data' => 'Data Module',
					))
					->set_default_value('normal'),
				Field::make('complex', GMB()->prefix . "data_attr", "Data Attributes")
					->set_min(1)
					->set_max(1)
					->add_fields(array(
						Field::make('text', 'permalink_button_title')
							->set_default_value('View Story')
						,
						Field::make('radio', 'want_summary_button')
							->add_options(array(
								'yes' => 'Yes',
								'no' => 'No',
							))
							->set_default_value('yes'),
						Field::make('text', 'summary_button_title')
							->set_conditional_logic(array(
								array(
									'field' => 'want_summary_button',
									'value' => 'yes',
								),
							))
							->set_default_value('Get Summary'),
						Field::make("select", "number_of_posts", "Number of posts")
							->add_options(array(
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
								'5' => '5',
								'6' => '6',
								'7' => '7',
								'8' => '8',
								'9' => '9',
								'10' => '10',
							))
							->set_default_value('10'),
					))
					->set_conditional_logic(array(
						'relation' => 'AND',
						array(
							'field' => GMB()->prefix . "response_method",
							'value' => 'data',
							'compare' => '=',
						),
					)),
				Field::make("radio", GMB()->prefix . "template", "Select Template")
					->help_text("Select which template you want to send")
					->add_options(array(
						'text' => __('Text', GMB()->namespace),
						'image' => __('image', GMB()->namespace),
						'audio' => __('Audio', GMB()->namespace),
						'video' => __('Video', GMB()->namespace),
						'file' => __('File', GMB()->namespace),
						'button' => __('Button', GMB()->namespace),
						'generic' => __('Generic', GMB()->namespace),
					))
					->set_default_value('text')
					->set_conditional_logic(array(
						'relation' => 'AND',
						array(
							'field' => GMB()->prefix . "response_method",
							'value' => 'normal',
							'compare' => '=',
						),
					)),

				Field::make("complex", GMB()->prefix . "text_template", "Text Template")
					->add_fields(array(
						Field::make('textarea', 'text'),
					))
					->set_min(1)
					->set_max(1)
					->set_conditional_logic(array(
						'relation' => 'AND',
						array(
							'field' => GMB()->prefix . "response_method",
							'value' => 'normal',
							'compare' => '=',
						),
						array(
							'field' => GMB()->prefix . "template",
							'value' => 'text',
							'compare' => '=',
						),
					)),
				Field::make("complex", GMB()->prefix . "image_template", "Image Template")
					->add_fields(array(
						Field::make('image', 'image')
							->set_value_type('url'),
					))
					->set_min(1)
					->set_max(1)
					->set_conditional_logic(array(
						'relation' => 'AND',
						array(
							'field' => GMB()->prefix . "response_method",
							'value' => 'normal',
							'compare' => '=',
						),
						array(
							'field' => GMB()->prefix . "template",
							'value' => 'image',
							'compare' => '=',
						),
					)),
				Field::make("complex", GMB()->prefix . "audio_template", "Audio Template")
					->add_fields(array(
						Field::make('text', 'audio', 'Audio URL'),
					))
					->set_min(1)
					->set_max(1)
					->set_conditional_logic(array(
						'relation' => 'AND',
						array(
							'field' => GMB()->prefix . "response_method",
							'value' => 'normal',
							'compare' => '=',
						),
						array(
							'field' => GMB()->prefix . "template",
							'value' => 'audio',
							'compare' => '=',
						),
					)),
				Field::make("complex", GMB()->prefix . "video_template", "Video Template")
					->add_fields(array(
						Field::make('text', 'video', 'Video URL'),
					))
					->set_min(1)
					->set_max(1)
					->set_conditional_logic(array(
						'relation' => 'AND',
						array(
							'field' => GMB()->prefix . "response_method",
							'value' => 'normal',
							'compare' => '=',
						),
						array(
							'field' => GMB()->prefix . "template",
							'value' => 'video',
							'compare' => '=',
						),
					)),
				Field::make("complex", GMB()->prefix . "file_template", "File Template")
					->add_fields(array(
						Field::make('text', 'file', 'File URL'),
					))
					->set_min(1)
					->set_max(1)
					->set_conditional_logic(array(
						'relation' => 'AND',
						array(
							'field' => GMB()->prefix . "response_method",
							'value' => 'normal',
							'compare' => '=',
						),
						array(
							'field' => GMB()->prefix . "template",
							'value' => 'file',
							'compare' => '=',
						),
					)),
				Field::make("complex", GMB()->prefix . "button_template", "Button Template")
					->add_fields(array(
						Field::make('textarea', 'text', 'Text'),
						Field::make('complex', 'buttons', 'Buttons')
							->set_min(1)
							->set_max(3)
							->add_fields(array(
								Field::make('text', 'title', 'Title'),
								Field::make('radio', 'type', 'Type')
									->add_options(array(
										'URL' => __('URL', GMB()->namespace),
										'Postback' => __('Postback', GMB()->namespace),
									))
									->set_default_value('URL'),
								Field::make('text', 'url_or_postback', 'Url or Payload'),

							)),
					))
					->set_min(1)
					->set_max(1)
					->set_conditional_logic(array(
						'relation' => 'AND',
						array(
							'field' => GMB()->prefix . "response_method",
							'value' => 'normal',
							'compare' => '=',
						),
						array(
							'field' => GMB()->prefix . "template",
							'value' => 'button',
							'compare' => '=',
						),
					)),
				Field::make("complex", GMB()->prefix . "generic_template", "Generic Template")
					->add_fields(array(
						Field::make('text', 'title', 'Title'),
						Field::make('text', 'subtitle', 'Subtitle'),
						Field::make('image', 'image', 'Image')
							->set_value_type('url'),
						Field::make('complex', 'buttons', 'Buttons')
							->set_min(1)
							->set_max(3)
							->add_fields(array(
								Field::make('text', 'title', 'Title'),
								Field::make('radio', 'type', 'Type')
									->add_options(array(
										'web_url' => __('URL', GMB()->namespace),
										'postback' => __('Postback', GMB()->namespace),
									))
									->set_default_value('URL'),
								Field::make('text', 'url_or_postback', 'Url or Payload'),

							)),
					))
					->set_min(1)
					->set_max(10)
					->set_conditional_logic(array(
						'relation' => 'AND',
						array(
							'field' => GMB()->prefix . "response_method",
							'value' => 'normal',
							'compare' => '=',
						),
						array(
							'field' => GMB()->prefix . "template",
							'value' => 'generic',
							'compare' => '=',
						),
					)),
			));

		$home = get_home_url();
		$webhook = $home . '/wp-json/' . GMB()->namespace . GMB()->endpoints->rest_base;
		$webhook = "<b>Webhook:</b> <br/>" . $webhook;
		$webhook .= "<br/> <br/><b>How to setup fb messenger bot:</b> <br/> <a href='https://developers.facebook.com/docs/messenger-platform/product-overview/setup' target='_blank'>https://developers.facebook.com/docs/messenger-platform/product-overview/setup</a>";
		Container::make('theme_options', 'BotPress')
			->set_page_parent('options-general.php')
		// ->add_tab('Help', array(
		// 	Field::make("html", "help_data", "Help Resources")
		// 		->set_html($this->help_data()),
		// ))
			->add_tab('Settings', array(
				Field::make("html", "webhook_url", "Webhook URL")
					->set_html($webhook),
				Field::make('text', GMB()->prefix . 'verify_token', 'Verify Token')
					->set_default_value(wp_generate_password(16)),
				Field::make('text', GMB()->prefix . 'access_token', 'Access Token'),
			));

	}
	function help_data() {
		ob_start();
		?>
		<div class="onepcssgrid-1200">
		   <div class="onerow">
				<div class="col3">Column 3</div>
				<div class="col3">Column 3</div>
				<div class="col3">Column 3</div>
				<div class="col3 last">Column 3</div>
			</div>
		</div>
		<?php
$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}