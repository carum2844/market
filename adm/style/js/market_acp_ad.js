$(document).ready(function(){


	switch ( $('#ad_item_action').val() ) {
		case '0':
			// Sell
			$('#ad_method').removeAttr('disabled');
			$('#ad_method').fadeIn();
			break;
		case '1':
			// Buy
			$('#ad_method').val('0');
			$('#ad_method').trigger('change');
			$('#ad_method').attr('disabled','disabled');
			break;
		case '1':
			// Catalog
			$('#ad_method').attr('disabled','disabled');
			$('#ad_method').trigger('change');
			$('#ad_method').fadeOut();
			break;
	}

	switch ( $('#ad_method').val() ) {
		case '0':
			// ask price
			$('.ad_bid').fadeOut();
			$('.ad_ask_price').fadeIn();
			break;
		case '1':
			// Bid - Make offer
			$('.ad_bid').fadeIn();
			$('.ad_ask_price').fadeOut();
			break;
	}

	if ( $('#ad_item_new_used').val() == '1' ){
		$('#ad_item_state').val('1');
		$('#ad_item_state').attr('disabled','disabled');
	}
	
	switch ( $('#ad_type_item').val() ) {
		case '1':
			//machine
			$('.machine').fadeIn();
			$('.maqart').fadeIn();
			$('.article').fadeOut();
			$('.service').fadeOut();

			// Populate selects
			$('#ad_item_product_name').attr('disabled','disabled');
			$('#ad_item_alt_maker_name').fadeOut();
			$('label[for=ad_item_model_id], input#ad_item_model_id').fadeOut();
			$('#ad_item_model_id').attr('disabled','disabled');
			$('#ad_item_alt_model_name').fadeOut();

			// Makers
			url_call = '/json_list_makers/MAKER_ID ';
			url_call = url_call.replace('MAKER_ID', item_maker_id);
			$.ajax({
				url: url_call,
				dataType: 'json',
			})
			.done(function(makers) {
				cargaSelect('ad_item_maker_id', item_maker_id, makers);
			});
			
			if ( item_maker_id != '0' ){

				// Models
				url_call = '/json_list_models/MAKER_ID/MODEL_ID ';
				url_call = url_call.replace('MAKER_ID', item_maker_id);
				url_call = url_call.replace('MODEL_ID', item_model_id);
				$.ajax({
					url: url_call,
					dataType: 'json',
				})
				.done(function(models) {
					cargaSelect('ad_item_model_id', item_model_id, models);
					$('label[for=ad_item_model_id], input#ad_item_model_id').fadeIn();
					$('#ad_item_model_id').removeAttr('disabled'); 
				});
				
				if ( item_model_id == '0' ){
					$('#ad_item_alt_model_name').fadeIn();
				}

			}else{
				$('#ad_item_alt_maker_name').fadeIn();
				var model_obj = $('#ad_item_model_id');
				model_obj.find('option').remove();
				model_obj.empty();
				model_obj.append('<option value="0">' + not_in_the_list + '</option>');
				$('#ad_item_model_id').fadeIn();
				$('#ad_item_alt_model_name').fadeIn();
			}
			
			break;
		case '2':
			//article
			$('.machine').fadeOut();
			$('.article').fadeIn();
			$('.maqart').fadeIn();
			$('.service').fadeOut();
			break;
		case '3':
			//service
			$('.machine').fadeOut();
			$('.article').fadeOut();
			$('.maqart').fadeOut();
			$('.service').fadeIn();
			break;
	}

});
(function($) {  // Avoid conflicts with other libraries

'use strict';

$('#ad_item_action').change(function(){

	switch ( $('#ad_item_action').val() ) {
		case '0':
			// Sell
			$('#ad_method').removeAttr('disabled');
			$('#ad_method').fadeIn();
			break;
		case '1':
			// Buy
			$('#ad_method').val('0');
			$('#ad_method').trigger('change');
			$('#ad_method').attr('disabled','disabled');
			break;
		case '1':
			// Catalog
			$('#ad_method').attr('disabled','disabled');
			$('#ad_method').trigger('change');
			$('#ad_method').fadeOut();
			break;
	}
	
});

$('#ad_method').change(function(){

	switch ( $('#ad_method').val() ) {
		case '0':
			// ask price
			$('.ad_bid').fadeOut();
			$('.ad_ask_price').fadeIn();
			break;
		case '1':
			// Bid - Make offer
			$('.ad_bid').fadeIn();
			$('.ad_ask_price').fadeOut();
			break;
	}
	
});

$('#ad_type_item').change(function(){
     
	switch ( $('#ad_type_item').val() ) {
		case '1':
			//machine
			$('.machine').fadeIn();
			$('.maqart').fadeIn();
			$('.article').fadeOut();
			$('.service').fadeOut();
			break;
		case '2':
			//article
			$('.machine').fadeOut();
			$('.article').fadeIn();
			$('.maqart').fadeIn();
			$('.service').fadeOut();
			break;
		case '3':
			//service
			$('.machine').fadeOut();
			$('.article').fadeOut();
			$('.maqart').fadeOut();
			$('.service').fadeOut();
			break;
	}
	
});

$('#ad_item_maker_id').change(function(){
	
	// Populate selects
	$('#ad_item_alt_maker_name').fadeOut();
	$('label[for=ad_item_model_id], input#ad_item_model_id').fadeOut();
	$('#ad_item_model_id').attr('disabled','disabled');
	$('#ad_item_alt_model_name').fadeOut();
	
	if ( $('#ad_item_maker_id').val() != '0' ){

		item_maker_id = $('#ad_item_maker_id').val();
		item_model_id = '-1';

		// Models
		url_call = '/json_list_models/MAKER_ID/MODEL_ID ';
		url_call = url_call.replace('MAKER_ID', item_maker_id);
		url_call = url_call.replace('MODEL_ID', item_model_id);
		$.ajax({
			url: url_call,
			dataType: 'json',
		})
		.done(function(models) {
			cargaSelect('ad_item_model_id', item_model_id, models);
			$('label[for=ad_item_model_id], input#ad_item_model_id').fadeIn();
			$('#ad_item_model_id').removeAttr('disabled'); 
		});

	}else{
		$('#ad_item_alt_maker_name').fadeIn();
		var model_obj = $('#ad_item_model_id');
		model_obj.find('option').remove();
		model_obj.empty();
		model_obj.append('<option value="0">' + not_in_the_list + '</option>');
		$('#ad_item_model_id').fadeIn();
		$('#ad_item_alt_model_name').fadeIn();
	}
});

$('#ad_item_model_id').change(function(){
	if ( $('#ad_item_model_id').val() != '0' ){
		$('#ad_item_alt_model_name').fadeOut();
	}else{
		$('#ad_item_alt_model_name').fadeIn();
	}
});


})(jQuery); // Avoid conflicts with other libraries
/**
*	Charge a select JSON data
*	@param url				@type String:	route to get the JSON
*	@param data				@type Array:	Sending data
*	@param cmb				@type String:	Name of the select object to charge
*	@param seleccionTexto	@type String:	Text for the option 'Select'
*	@param seleccion		@type String:	Value for the option 'Selected'
*/
function cargaSelect(objeto, seleccion, data){

	var miselect = $('#'+objeto);
	var popo = '';

	miselect.find('option').remove();
	miselect.empty();
	$.each( data, function( index, value ) {
		miselect.append('<option value="' + index + '">' + value + '</option>');
	});

	miselect.each(function() {        
							// Sort all the options by text. I could easily sort these by val.
							$(this).html($('option', $(this)).sort(function(a, b) {
								return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
							}));
						});

	$('#'+objeto+' option').each(function() {
		if($(this).val() == seleccion) {
			$(this).prop('selected', true);
		}
	});

};  
