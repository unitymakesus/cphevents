var woosbTimeout = null;
jQuery( document ).ready( function( jQuery ) {
	woosb_active_settings();
	jQuery( '#product-type' ).on( 'change', function() {
		woosb_active_settings();
	} );

	// hide search result box by default
	jQuery( '#woosb_results' ).hide();
	jQuery( '#woosb_loading' ).hide();

	// total price
	if ( jQuery( '#product-type' ).val() == 'woosb' ) {
		woosb_change_total();
	}

	// set regular price
	jQuery( '#woosb_set_regular_price' ).on( 'click', function() {
		if ( jQuery( '#woosb_disable_auto_price' ).is( ':checked' ) ) {
			jQuery( 'li.general_tab a' ).trigger( 'click' );
			jQuery( '#_regular_price' ).prop( 'readonly', false );
			jQuery( '#_regular_price' ).focus();
		} else {
			jQuery( '#_regular_price' ).prop( 'readonly', true );
			alert( 'You must disable auto calculate regular price first!' );
		}
	} );

	// set sale price
	jQuery( '#woosb_set_sale_price' ).on( 'click', function() {
		jQuery( 'li.general_tab a' ).trigger( 'click' );
		if ( jQuery( '#woosb_disable_auto_price' ).is( ':checked' ) ) {
			jQuery( '#_regular_price' ).prop( 'readonly', false );
		} else {
			jQuery( '#_regular_price' ).prop( 'readonly', true );
		}
		jQuery( '#_sale_price' ).focus();
	} );

	// checkbox
	jQuery( '#woosb_disable_auto_price' ).change( function() {
		woosb_change_total();
	} );

	// search input
	jQuery( '#woosb_keyword' ).keyup( function() {
		if ( jQuery( '#woosb_keyword' ).val() != '' ) {
			if ( woosbTimeout != null ) {
				jQuery( '#woosb_loading' ).show();
				clearTimeout( woosbTimeout );
			}
			woosbTimeout = setTimeout( woosb_ajax_get_data, 300 );
			return false;
		}
	} );

	// actions on search result items
	jQuery( '#woosb_results' ).on( 'click', 'li', function() {
		jQuery( this ).children( 'span.qty' ).html( '<input type="number" value="1" min="1"/>' );
		jQuery( this ).children( 'span.remove' ).html( '×' );
		jQuery( '#woosb_selected ul' ).append( jQuery( this ) );
		jQuery( '#woosb_results' ).hide();
		jQuery( '#woosb_keyword' ).val( '' );
		woosb_get_ids();
		woosb_change_total();
		return false;
	} );

	// change qty of each item
	jQuery( '#woosb_selected' ).on( 'keyup change click', '.qty input', function() {
		var num = jQuery( this ).val();
		var cid = jQuery( this ).parent().parent().attr( 'data-id' );
		woosb_get_ids();
		woosb_change_total();
		return false;
	} );

	// actions on selected items
	jQuery( '#woosb_selected' ).on( 'click', 'span.remove', function() {
		jQuery( this ).parent().remove();
		woosb_get_ids();
		woosb_change_total();
		return false;
	} );

	// hide search result box if click outside
	jQuery( document ).on( 'click', function( e ) {
		if ( jQuery( e.target ).closest( jQuery( '#woosb_results' ) ).length == 0 ) {
			jQuery( '#woosb_results' ).hide();
		}
	} );

	// order
	jQuery( '#woosb_selected li' ).arrangeable( {
		dragEndEvent: 'woosbDragEndEvent',
		dragSelector: '.move'
	} );

	jQuery( document ).on( 'woosbDragEndEvent', function() {
		woosb_get_ids();
	} );
} );

function woosb_get_ids() {
	var listId = new Array();
	jQuery( '#woosb_selected li' ).each( function() {
		listId.push( jQuery( this ).attr( 'data-id' ) + '/' + jQuery( this ).find( 'input' ).val() );
	} );
	if ( listId.length > 0 ) {
		jQuery( '#woosb_ids' ).val( listId.join( ',' ) );
	} else {
		jQuery( '#woosb_ids' ).val( '' );
	}
}

function woosb_active_settings() {
	if ( jQuery( '#product-type' ).val() == 'woosb' ) {
		jQuery( 'input#_downloadable' ).prop( 'checked', false );
		jQuery( 'input#_virtual' ).removeAttr( 'checked' );
		jQuery( '.show_if_external' ).hide();
		jQuery( '.show_if_simple' ).show();
		jQuery( '.show_if_woosb' ).show();
		jQuery( 'input#_downloadable' ).closest( '.show_if_simple' ).hide();
		jQuery( 'input#_virtual' ).closest( '.show_if_simple' ).hide();
		jQuery( '.product_data_tabs li' ).removeClass( 'active' );
		jQuery( '.woosb_tab' ).addClass( 'active' );
		jQuery( '.panel-wrap .panel' ).hide();
		jQuery( '#woosb_settings' ).show();
		jQuery( '#_regular_price' ).prop( 'readonly', true );
	} else {
		jQuery( '#_regular_price' ).prop( 'readonly', false );
		jQuery( '.show_if_woosb' ).hide();
	}
}

function woosb_change_total() {
	var total = 0;
	var total_max = 0;
	jQuery( '#woosb_selected li' ).each( function() {
		if ( ! jQuery( this ).hasClass( 'out-of-stock' ) ) {
			total += jQuery( this ).attr( 'data-price' ) * jQuery( this ).find( 'input' ).val();
			total_max += jQuery( this ).attr( 'data-price-max' ) * jQuery( this ).find( 'input' ).val();
		}
	} );
	if ( total == total_max ) {
		jQuery( '#woosb_regular_price' ).html( total );
	} else {
		jQuery( '#woosb_regular_price' ).html( total + ' - ' + total_max );
	}
	if ( ! jQuery( '#woosb_disable_auto_price' ).is( ':checked' ) ) {
		jQuery( '#_regular_price' ).val( total );
	}
}

function woosb_ajax_get_data() {
	// ajax search product
	woosbTimeout = null;
	data = {
		action: 'woosb_get_search_results',
		woosb_keyword: jQuery( '#woosb_keyword' ).val(),
		woosb_ids: jQuery( '#woosb_ids' ).val(),
		woosb_nonce: woosb_vars.woosb_nonce
	};
	jQuery.post( ajaxurl, data, function( response ) {
		jQuery( '#woosb_results' ).show();
		jQuery( '#woosb_results' ).html( response );
		jQuery( '#woosb_loading' ).hide();
	} );
}