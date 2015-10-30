<?php 

class Carbon_Field_Choose_Sidebar extends Carbon_Field_Sidebar {
	private $custom_sidebars = array();
	private $sidebar_options = array();

	function init() {
		// Set Default Sidebar Options
		$this->sidebar_options['default'] = $this->get_default_sidebar_options();

		// Setup the sidebars after all fields are registered
		add_action('carbon_after_register_fields', array($this, 'setup_sidebar_options'), 20);
		add_action('carbon_after_register_fields', array($this, 'register_custom_sidebars'), 21);
	}

	/**
	 * Returns an array with the default sidebar options
	 */
	function get_default_sidebar_options() {
		$sidebar_options = array(
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h2 class="widgettitle">',
			'after_title'   => '</h2>',
		);

		if ( function_exists( 'crb_get_default_sidebar_options' ) ) {
			$sidebar_options = crb_get_default_sidebar_options();
		}

		return $sidebar_options;
	}

	/**
	 * Sets the Sidebar Options
	 */
	function set_sidebar_options($sidebar_options) {
		// Make sure that all needed fields are in the options array
		$required_arguments = array('before_widget', 'after_widget', 'before_title', 'after_title');

		/**
		 * Set default settings
		 */
		$sidebar_options_first_element = array_values($sidebar_options);
		$sidebar_options_first_element = $sidebar_options_first_element[0];

		if ( !is_array($sidebar_options_first_element) ) {
			/**
			 * Enters here, if one array with settings is passed
			 * Makes the passed settings, the default ones
			 */
			$sidebar_options = array(
				'default' => $sidebar_options,
			);
		} elseif ( !isset($sidebar_options['default']) ) {
			/**
			 * Enters here, if the default settings are not passed
			 * Sets the default settings
			 *
			 * @see get_default_sidebar_options()
			 */
			$sidebar_options['default'] = $this->get_default_sidebar_options();
		}

		/**
		 * Check if all required arguments are passed for each of the sidebars
		 */
		foreach ($sidebar_options as $options) {
			foreach ($required_arguments as $arg) {
				if ( !isset($options[$arg]) ) {
					throw new Carbon_Exception(
						'Provide all sidebar options for ' . $this->name . ': <code>' .
						implode(', ', $required_arguments) . '</code>'
					);
				}
			}
		}

		$this->sidebar_options = $sidebar_options;
		return $this;
	}

	function setup_sidebar_options() {
		global $wp_registered_sidebars;
		$custom_sidebars = $this->get_custom_sidebars();

		// Add field options
		$sidebars = array();

		foreach ($wp_registered_sidebars as $sidebar) {
			$sidebars[] = $sidebar['name'];
		}

		$options = array_merge($sidebars, $custom_sidebars);
		$options = array_combine($options, $options);
		$this->add_options($options);
	}

	function register_custom_sidebars() {
		$custom_sidebars = $this->get_custom_sidebars();

		foreach ($custom_sidebars as $sidebar) {
			$slug = sanitize_title_with_dashes($sidebar);

			$sidebar_options = array();

			// Handles which options to use for the current sidebar
			if ( isset($this->sidebar_options[$slug]) ) {
				$sidebar_options = $this->sidebar_options[$slug];
			} else {
				$sidebar_options = $this->sidebar_options['default'];
			}

			// Registers the Sidebar
			register_sidebar(array_merge($sidebar_options, array(
				'name' => $sidebar,
				'id'   => $slug,
			)));
		}
	}

	function get_custom_sidebars() {
		global $wpdb;

		if ( !empty($this->custom_sidebars) ) {
			return $this->custom_sidebars;
		}

		$sidebars = array();

		$query_string = '';
		switch ($this->context) {
			case 'CustomFields':
				$query_string = 'SELECT meta_value AS sidebar FROM ' . $wpdb->postmeta . ' WHERE meta_key = "' .  esc_sql($this->name) . '"';
				break;
			case 'TermMeta':
				$query_string = 'SELECT meta_value AS sidebar FROM ' . $wpdb->termmeta . ' WHERE meta_key = "' .  esc_sql($this->name) . '"';
				break;
			case 'ThemeOptions':
				$query_string = 'SELECT option_value AS sidebar FROM ' . $wpdb->options . ' WHERE option_name = "' .  esc_sql($this->name) . '"';
				break;
			case 'UserMeta':
				$query_string = 'SELECT meta_value AS sidebar FROM ' . $wpdb->usermeta . ' WHERE meta_key = "' .  esc_sql($this->name) . '"';
				break;
		}

		$sidebar_names = array_filter($wpdb->get_col($query_string));

		foreach ($sidebar_names as $sidebar_name) {
			$sidebars[$sidebar_name] = 1;
		}

		$this->custom_sidebars = array_keys($sidebars);

		return $this->custom_sidebars;
	}
}