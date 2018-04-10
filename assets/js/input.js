(function($){
	
	
	/**
	*  initialize_field
	*
	*  This function will initialize the $field.
	*
	*  @date	30/11/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize_field( $field ) {
		
		//$field.doStuff();
		var $input = $field.find('input');
		// This must be done or mask plugin will go crazy
		$input = $( 'input[name="' + $input.attr('name') + '"]' );
		// ----------------------------------------------
		if( $input.is('[data-special-mask]') ){
			switch( $input.attr('data-special-mask') ){
				case 'cpf_cnpj':
					var $options = {
						clearIfNotMatch: true,
						onKeyPress : function( $value, e, field, $options ){
							var $masks = [ '000.000.000-009999', '00.000.000/0000-00' ];
							var $mask = ($value.length > 14) ? $masks[1] : $masks[0];
							$input.mask( $mask, $options )
						}
					};
					$input.mask( '000.000.000-009999', $options );
			}
		} else {
			var $options = {clearIfNotMatch: true};
			var $reverse = $input.attr('data-reverse-mask');
			if( typeof $reverse !== typeof undefined && $reverse !== false ){
				$options.reverse = true;
			}
			$input.mask( $input.attr('data-mask'), $options );
		}
	}
	
	if( typeof acf.add_action !== 'undefined' ) {
	
		/*
		*  ready & append (ACF5)
		*
		*  These two events are called when a field element is ready for initizliation.
		*  - ready: on page load similar to $(document).ready()
		*  - append: on new DOM elements appended via repeater field or other AJAX calls
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		acf.add_action('ready_field/type=masked_input', initialize_field );
		acf.add_action('append_field/type=masked_input', initialize_field );
		
	}

})(jQuery);
