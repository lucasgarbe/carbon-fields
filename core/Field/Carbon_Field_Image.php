<?php 

class Carbon_Field_Image extends Carbon_Field_Attachment {
	public $field_type = 'image';
	public $value_type = 'url';
	
	function admin_init() {
		$this->button_label = __('Select Image', 'crb');
		$this->window_button_label = __('Select Image', 'crb');
		$this->window_label = __('Images', 'crb');

		parent::admin_init();
	}
}
