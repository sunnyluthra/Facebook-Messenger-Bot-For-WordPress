<?php

if (!defined('ABSPATH')) {
	exit;
}

class GMB_Post_Types {
	function __construct() {
		$this->create_cpt();
		// add_action('cmb2_admin_init', array($this, 'meta_boxes'));

	}

	function create_cpt() {
		$responses = new CPT('response',
			array(
				'supports' => array('title'),
			)
		);

		$responses->register_taxonomy('user-say',
			array(
				'hierarchical' => false,
			)
		);
	}

}