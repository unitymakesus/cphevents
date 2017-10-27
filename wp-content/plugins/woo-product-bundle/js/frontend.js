jQuery( document ).ready( function( jQuery ) {
	woosb_check_variables();

	jQuery( '.product-type-woosb .woosb-products select' ).on( 'change', function() {
		jQuery( this ).closest( '.woosb-product' ).attr( 'data-id', 0 );
		woosb_check_variables();
	} );

	jQuery( document ).on( 'found_variation', function( e, t ) {
		jQuery( e['target'] ).closest( '.woosb-product' ).attr( 'data-id', t['variation_id'] );
		jQuery( e['target'] ).closest( '.woosb-product' ).attr( 'data-price', t['display_price'] );
		woosb_check_variables();
		woosb_save_ids();
	} );

	jQuery( '.single_add_to_cart_button' ).click( function() {
		jQuery.ajax( {
			url: woosb_vars.ajax_url,
			type: "POST",
			data: {
				action: 'woosb_custom_data',
				woosb_ids: jQuery( '#woosb_ids' ).val(),
				woosb_nonce: woosb_vars.woosb_nonce
			},
			async: false,
		} );
	} );

	jQuery( '.product-type-woosb' ).on( 'click', '.single_add_to_cart_button.disabled', function( e ) {
		e.preventDefault();
		alert( woosb_vars.alert_text );
	} );
} );

function woosb_check_variables() {
	if ( jQuery( '#woosb_products' ).attr( 'data-variables' ) == 'yes' ) {
		var is_ok = true;
		jQuery( '.product-type-woosb .woosb-products .woosb-product' ).each( function() {
			if ( jQuery( this ).attr( 'data-id' ) == 0 ) {
				is_ok = false;
				return;
			}
		} );
		if ( is_ok ) {
			jQuery( '.product-type-woosb .single_add_to_cart_button' ).removeClass( 'disabled' ).removeClass( 'wc-variation-selection-needed' );
			woosb_total_price();
		} else {
			jQuery( '.product-type-woosb .single_add_to_cart_button' ).addClass( 'disabled' ).addClass( 'wc-variation-selection-needed' );
			jQuery( '#woosb_total' ).slideUp();
		}
	}
}

function woosb_total_price() {
	var total = 0;
	var total_html = woosb_vars.bundle_price_text + ' ';
	if ( jQuery( '#woosb_products' ).attr( 'data-sale' ) > 0 ) {
		total = jQuery( '#woosb_products' ).attr( 'data-sale' );
	} else {
		jQuery( '.product-type-woosb .woosb-products .woosb-product' ).each( function() {
			if ( jQuery( this ).attr( 'data-price' ) > 0 ) {
				total += jQuery( this ).attr( 'data-price' ) * jQuery( this ).attr( 'data-qty' );
			}
		} );
		if ( (
			     jQuery( '#woosb_products' ).attr( 'data-percent' ) > 0
		     ) && (
			     jQuery( '#woosb_products' ).attr( 'data-percent' ) < 100
		     ) ) {
			total = (
				        total * jQuery( '#woosb_products' ).attr( 'data-percent' )
			        ) / 100;
		}
	}
	total = woosb_format_money( total, woosb_vars.price_decimals, '', woosb_vars.price_thousand_separator, woosb_vars.price_decimal_separator );
	switch ( woosb_vars.price_format ) {
		case '%1$s%2$s':
			total_html += woosb_vars.currency_symbol + '' + total;
			break;
		case '%1$s %2$s':
			total_html += woosb_vars.currency_symbol + ' ' + total;
			break;
		case '%2$s%1$s':
			total_html += total + '' + woosb_vars.currency_symbol;
			break;
		case '%2$s %1$s':
			total_html += total + ' ' + woosb_vars.currency_symbol;
			break;
		default:
			total_html = '';
	}
	jQuery( '#woosb_total' ).html( total_html ).slideDown();
}

function woosb_save_ids() {
	var listId = Array();
	jQuery( '.product-type-woosb .woosb-products .woosb-product' ).each( function() {
		if ( jQuery( this ).attr( 'data-id' ) != 0 ) {
			listId.push( jQuery( this ).attr( 'data-id' ) + '/' + jQuery( this ).attr( 'data-qty' ) );
		}
	} );
	jQuery( '#woosb_ids' ).val( listId.join( ',' ) );
}

function woosb_format_money( number, places, symbol, thousand, decimal ) {
	number = number || 0;
	places = ! isNaN( places = Math.abs( places ) ) ? places : 2;
	symbol = symbol !== undefined ? symbol : "$";
	thousand = thousand || ",";
	decimal = decimal || ".";
	var negative = number < 0 ? "-" : "",
		i = parseInt( number = Math.abs( + number || 0 ).toFixed( places ), 10 ) + "",
		j = (
			    j = i.length
		    ) > 3 ? j % 3 : 0;
	return symbol + negative + (
			j ? i.substr( 0, j ) + thousand : ""
		) + i.substr( j ).replace( /(\d{3})(?=\d)/g, "$1" + thousand ) + (
		       places ? decimal + Math.abs( number - i ).toFixed( places ).slice( 2 ) : ""
	       );
}