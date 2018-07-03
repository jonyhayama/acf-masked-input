<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('jony_acf_field_masked_input') ) :


class jony_acf_field_masked_input extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct( $settings ) {
		
		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/
		
		$this->name = 'masked_input';
		
		
		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/
		
		$this->label = __('Masked Input', 'jony');
		
		
		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		
		$this->category = 'jquery';
		
		
		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/
		
		$this->defaults = array(
			'mask'	=> '',
			'reverse' => '0'
		);
		
		
		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('masked_input', 'error');
		*/
		
		$this->l10n = array(
			'error'	=> __('Error! Please enter a mask', 'jony'),
		);
		
		
		/*
		*  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		*/
		
		$this->settings = $settings;
		
		
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field_settings( $field ) {
		
		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/
		
		// prepend
		acf_render_field_setting( $field, array(
			'label'			=> __('Prepend','acf'),
			'instructions'	=> __('Appears before the input','acf'),
			'type'			=> 'text',
			'name'			=> 'prepend',
		));
		
		
		// append
		acf_render_field_setting( $field, array(
			'label'			=> __('Append','acf'),
			'instructions'	=> __('Appears after the input','acf'),
			'type'			=> 'text',
			'name'			=> 'append',
		));
		
		$instructions = '';
		$instructions = __('Type the desired Mask.','jony') . '<br/>';
		$instructions.= '0 : ' . __( 'Required digit', 'jony' ) . '<br/>';
		$instructions.= '9 : ' . __( 'Optional digit', 'jony' ) . '<br/>';
		$instructions.= '# : ' . __( 'Recursive digit', 'jony' ) . '<br/>';
		$instructions.= 'A : ' . __( 'Letter or Digit', 'jony' ) . '<br/>';
		$instructions.= 'S : ' . __( 'Letter', 'jony' ) . '<br/>';
		$instructions.= 'cpf_cnpj : ' . __( 'CPF or CNPJ (Brazillian Documents)', 'jony' ) . '<br/>';
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Mask','jony'),
			'instructions'	=> $instructions,
			'type'			=> 'text',
			'name'			=> 'mask',
		) );
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Reverse Mask','jony'),
			'instructions'	=> 'Reverse Mask',
			'type'			=> 'true_false',
			'name'			=> 'reverse',
			'ui'			  => 1
		) );
		
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Save As', 'jony'),
			'instructions'	=> 'How data should be saved in the database',
			'type'			=> 'select',
			'name'			=> 'save_as',
			'choices'		=> array(
								'text' 		=> __( 'Text', 'jony' ),
								'integer' 	=> __( 'Integer', 'jony' ),
								'float' 	=> __( 'Float', 'jony' )
							)
		) );
		
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Decimal Point', 'jony'),
			'instructions'	=> 'What character will be used as decimal point (used only if "Save As" equals "Float").',
			'type'			=> 'text',
			'name'			=> 'decimal_point',
			'maxlength'		=> '1',
			'default_value' => '.',
		) );

	}
	
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field ) {
		
		// vars
		$atts = array( 'type' => 'text' );
		$keys = array( 'id', 'class', 'name', 'value', 'placeholder', 'maxlength', 'pattern' );
		$keys2 = array( 'readonly', 'disabled', 'required' );
		$html = '';
		
		
		/*
		*  Review the data of $field.
		*  This will show what data is available
		*/
		$reverse = (int) $field['reverse'];
		$mask = esc_attr($field['mask']);
		// Special Masks
		if( in_array( $mask, array( 'cpf_cnpj' ) ) ){
			$atts['data-special-mask'] = $mask;
		} else {
			$atts['data-mask'] = $mask;
			if( $reverse ){
				$atts['data-reverse-mask'] = 'data-reverse-mask';
			}
		}
		
		// prepend
		if( $field['prepend'] !== '' ) {
			$field['class'] .= ' acf-is-prepended';
			$html .= '<div class="acf-input-prepend">' . acf_esc_html($field['prepend']) . '</div>';
		}
		
		// append
		if( $field['append'] !== '' ) {
			$field['class'] .= ' acf-is-appended';
			$html .= '<div class="acf-input-append">' . acf_esc_html($field['append']) . '</div>';
		}
		
		// atts (value="123")
		foreach( $keys as $k ) {
			if( isset($field[ $k ]) ) $atts[ $k ] = $field[ $k ];
		}
		
		// atts2 (disabled="disabled")
		foreach( $keys2 as $k ) {
			if( !empty($field[ $k ]) ) $atts[ $k ] = $k;
		}
		
		// remove empty atts
		$atts = acf_clean_atts( $atts );

		// render
		$html .= '<div class="acf-input-wrap">' . acf_get_text_input( $atts ) . '</div>';
		
		// return
		echo $html;
	}
	
		
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	
	
	function input_admin_enqueue_scripts() {
		
		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];
		
		
		// register & include JS
		wp_register_script('jquery-mask', "{$url}assets/js/jquery.mask.js", array('jquery'), '1.14.13');
		wp_register_script('jony-masked-input', "{$url}assets/js/input.js", array('acf-input', 'jquery-mask'), $version);
		wp_enqueue_script('jony-masked-input');
		
		
		// register & include CSS
		// wp_register_style('jony', "{$url}assets/css/input.css", array('acf-input'), $version);
		// wp_enqueue_style('jony');
		
	}
	
	
	
	
	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
		
	function input_admin_head() {
	
		
		
	}
	
	*/
	
	
	/*
   	*  input_form_data()
   	*
   	*  This function is called once on the 'input' page between the head and footer
   	*  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and 
   	*  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
   	*  seen on comments / user edit forms on the front end. This function will always be called, and includes
   	*  $args that related to the current screen such as $args['post_id']
   	*
   	*  @type	function
   	*  @date	6/03/2014
   	*  @since	5.0.0
   	*
   	*  @param	$args (array)
   	*  @return	n/a
   	*/
   	
   	/*
   	
   	function input_form_data( $args ) {
	   	
		
	
   	}
   	
   	*/
	
	
	/*
	*  input_admin_footer()
	*
	*  This action is called in the admin_footer action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_footer)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
		
	function input_admin_footer() {
	
		
		
	}
	
	*/
	
	
	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function field_group_admin_enqueue_scripts() {
		
	}
	
	*/

	
	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function field_group_admin_head() {
	
	}
	
	*/


	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	
	/*
	
	function load_value( $value, $post_id, $field ) {
		
		return $value;
		
	}
	
	*/
	
	
	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	
	function update_value( $value, $post_id, $field ) {
		if( isset( $field['save_as'] ) ){
			if( $field['save_as'] == 'integer' ){
				$value = (int) preg_replace( '/[^0-9]/', '', $value );
			} else if( $field['save_as'] == 'float' ){
				$dec_point = ( isset( $field['decimal_point'] ) ) ? $field['decimal_point'] : '.';
				$value = preg_replace( "/[^0-9{$dec_point}]/", '', $value );
				$value = (float) str_replace( $dec_point, '.', $value );
			}
		}
		return $value;
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
		
	/*
	
	function format_value( $value, $post_id, $field ) {
		
		// bail early if no value
		if( empty($value) ) {
		
			return $value;
			
		}
		
		
		// apply setting
		if( $field['font_size'] > 12 ) { 
			
			// format the value
			// $value = 'something';
		
		}
		
		
		// return
		return $value;
	}
	
	*/
	
	
	/*
	*  validate_value()
	*
	*  This filter is used to perform validation on the value prior to saving.
	*  All values are validated regardless of the field's required setting. This allows you to validate and return
	*  messages to the user if the value is not correct
	*
	*  @type	filter
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$valid (boolean) validation status based on the value and the field's required setting
	*  @param	$value (mixed) the $_POST value
	*  @param	$field (array) the field array holding all the field options
	*  @param	$input (string) the corresponding input name for $_POST value
	*  @return	$valid
	*/
	
	/*
	
	function validate_value( $valid, $value, $field, $input ){
		
		// Basic usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = false;
		}
		
		
		// Advanced usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = __('The value is too little!','jony'),
		}
		
		
		// return
		return $valid;
		
	}
	
	*/
	
	
	/*
	*  delete_value()
	*
	*  This action is fired after a value has been deleted from the db.
	*  Please note that saving a blank value is treated as an update, not a delete
	*
	*  @type	action
	*  @date	6/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (mixed) the $post_id from which the value was deleted
	*  @param	$key (string) the $meta_key which the value was deleted
	*  @return	n/a
	*/
	
	/*
	
	function delete_value( $post_id, $key ) {
		
		
		
	}
	
	*/
	
	
	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0	
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/
	
	/*
	
	function load_field( $field ) {
		
		return $field;
		
	}	
	
	*/
	
	
	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/
	
	/*
	
	function update_field( $field ) {
		
		return $field;
		
	}	
	
	*/
	
	
	/*
	*  delete_field()
	*
	*  This action is fired after a field is deleted from the database
	*
	*  @type	action
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	n/a
	*/
	
	/*
	
	function delete_field( $field ) {
		
		
		
	}	
	
	*/
	
	
}


// initialize
new jony_acf_field_masked_input( $this->settings );


// class_exists check
endif;

?>