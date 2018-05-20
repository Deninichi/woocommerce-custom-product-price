jQuery(document).ready(function($) {
	
	var newPrice = '';
	var width = '';
	var height = '';
	var productId = $( '.pricing-popup input[name="product_id"]' ).val();

	$( '.pricing-btn' ).click( function(event) {
		$( '.pricing-popup' ).show();
		$( '.pricing-popup-overlay' ).show();
	});

	$( '.pricing-popup .close, .pricing-popup-overlay' ).click( function(event) {
		$( '.pricing-popup' ).hide();
		$( '.pricing-popup-overlay' ).hide();
	});

	$( '.pricing-popup .calculate' ).click( function(event) {
		
		if ( !$('input[name="width"]').val() || !$('input[name="height"]').val() ){
			$( '.pricing-popup .price' ).after( '<p class="alert">You need fill form bellow.</p>' );
		}
		else{
			$( '.pricing-popup .alert' ).remove();

			width = $('input[name="width"]').val();
			height = $('input[name="height"]').val();

			var data = {
		        'action': 'calculate',
		        'width': width,
		        'height': height,
		    };

		    $.post( 
		        ajax_object.ajaxurl, 
		        data, 
		        function(response) {
		        	$( '.pricing-popup .price span' ).html(response);
		        	newPrice = response;
		        	$( '.pricing-popup button.add-to-cart' ).show();
		        }
		    );
		}
	});


	$( '.pricing-popup .add-to-cart' ).click( function(event) {
		console.log("Click");
		var data = {
	        'action': 'add_to_cart',
	        'product_id': productId,
	        'new_price': newPrice,
	        'width': width,
		    'height': height,
	    };

	    $.post( 
	        ajax_object.ajaxurl, 
	        data, 
	        function(response) {
	        	$( '.pricing-popup .add-to-cart' ).after( response );
	        }
	    );
	});
});