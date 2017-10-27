// Note: I18n is loaded into wjecf_admin_i18n

jQuery( function( $ ) {

	var init = function() {
		//Move before the product_ids selector and append AND/OR to the label
		var element_product_ids = $("#woocommerce-coupon-data .form-field:has('[name=\"product_ids[]\"]')"); //Since WC3.0.0
		if (element_product_ids.length != 1) element_product_ids = $("#woocommerce-coupon-data .form-field:has('[name=\"product_ids\"]')"); //Prior to WC3.0.0
		if (element_product_ids.length == 1) {
			$("#woocommerce-coupon-data .form-field:has('#_wjecf_products_and')").detach().insertBefore( element_product_ids );
			//Append AND/OR to the label
			element_product_ids.children("label").append( ' <strong><span id="wjecf_products_and_label"></span></strong>' );
			//Update AND or OR when checkbox value changes
			$("#_wjecf_products_and").click( update_wjecf_products_and );
		}

		//Move before the product_categories selector and append AND/OR to the label
		var element_product_categories = $("#woocommerce-coupon-data .form-field:has('[name=\"product_categories[]\"]')");
		if (element_product_categories.length == 1) {
			$("#woocommerce-coupon-data .form-field:has('#_wjecf_categories_and')").detach().insertBefore( element_product_categories );
			//Append AND/OR to the label
			element_product_categories.children("label").append( ' <strong><span id="wjecf_categories_and_label"></span></strong>' );
			//Update AND or OR when checkbox value changes
			$("#_wjecf_categories_and").click( update_wjecf_categories_and );
		}

		//Update now
		update_wjecf_products_and();
		update_wjecf_categories_and();

		$( 'select#discount_type' ).on( 'change', update_discount_type ).change();
	};

	var update_wjecf_products_and = function() { 
		$("#wjecf_products_and_label").html( 
			$("#_wjecf_products_and").val() == 'yes' ? wjecf_admin_i18n.label_and : wjecf_admin_i18n.label_or
		);
	};

	var update_wjecf_categories_and = function() { 
		$("#wjecf_categories_and_label").html( 
			$("#_wjecf_categories_and").val() == 'yes' ? wjecf_admin_i18n.label_and : wjecf_admin_i18n.label_or
		);
	};

	/** Toggle visibility depending on selected discount type **/
	var update_discount_type = function() {
		// Get value
		var select_val = $( 'select#discount_type' ).val();

		if ( select_val === 'fixed_cart' ) {
			$( '.wjecf_hide_on_fixed_cart_discount' ).hide();
		} else {
			$( '.wjecf_hide_on_fixed_cart_discount' ).show();
		}

		if ( select_val === 'fixed_product' || select_val === 'percent_product' ) {
			$( '.wjecf_hide_on_product_discount' ).hide();
		} else {
			$( '.wjecf_hide_on_product_discount' ).show();
		}
	}

	init();
} );