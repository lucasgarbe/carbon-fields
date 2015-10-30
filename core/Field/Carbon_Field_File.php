<?php

class Carbon_Field_File extends Carbon_Field {
	public $button_label = '';
	public $window_button_label = '';
	public $window_label = '';

	// empty for all types. available types: audio, video, image
	public $field_type = '';

	// alt, author, caption, dateFormatted, description, editLink, filename, height, icon, id, link, menuOrder, mime, name, status, subtype, title, type, uploadedTo, url, width
	public $value_type = 'url';

	function admin_init() {
		$this->button_label = __('Select File', 'crb');
		$this->window_button_label = __('Select File', 'crb');
		$this->window_label = __('Files', 'crb');

		$this->add_template($this->get_type() . '-Description', array($this, 'template_description'));
	}

	function set_type($type) {
		$this->field_type = $type;
		return $this;
	}

	function to_json($load) {
		$field_data = parent::to_json($load);

		$url = '';
		$thumb_url = '';
		$default_thumb_url = home_url('/wp-includes/images/media/default.png');
		$file_ext = '';
		$file_type = '';
		$value = $this->get_value();

		if ($value) {
			$url = is_numeric($value) ? wp_get_attachment_url($value) : $value;
			$filetype = wp_check_filetype($url);

			$file_ext = $filetype['ext']; // png, mp3, etc..
			$file_type = preg_replace('~\/.+$~', '', $filetype['type']); // image, video, etc..

			if ($file_type == 'image') {
				$thumb_url = $url;

				if ($this->value_type == 'id') {
					$thumb_src = wp_get_attachment_image_src($value, 'thumbnail');
					$thumb_url = $thumb_src[0];
				}
			}
		}

		$field_data = array_merge($field_data, array(
			'url' => (string) $url,
			'thumb_url' => $thumb_url,
			'default_thumb_url' => $default_thumb_url,
			'file_ext' => $file_ext,
			'file_type' => $file_type,
			'button_label' => $this->button_label,
			'window_button_label' => $this->window_button_label,
			'window_label' => $this->window_label,
			'type_filter' => $this->field_type,
			'value_type' => $this->value_type,
		));

		return $field_data;
	}

	function template() {
		?>
		<div class="input-with-button">
			<input 
				id="{{ id }}" 
				type="text" 
				name="{{ name }}" 
				value="{{ value }}" 
				class="regular-text carbon-file-field" 
				{{{ value_type === 'id' ? 'style="display:none"' : '' }}} 
			/>

			<span id="c2_open_media{{ id.replace('-', '_') }}" class="button c2_open_media icon-button">
				{{{ button_label }}}
			</span>
		</div>
		
		{{{ description }}}
		<?php
	}

	function template_description() {
		?>
		<div class="carbon-description {{{ value ? '' : 'hidden' }}}">
			<a href="{{ value }}" target="_blank" class="button carbon-view_file">
				<?php _e('View File', 'crb'); ?>
			</a>
		</div>
		<?php
	}
}
