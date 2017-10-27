jQuery(document).ready(function($) {
  // Adjust quantity with UI buttons
  $('.cph-quantity').each(function() {
    var spinner = $(this),
      input = spinner.find('input[type="number"]'),
      btnUp = spinner.find('.cph-quantity-up'),
      btnDown = spinner.find('.cph-quantity-down'),
      min = input.attr('min'),
      max = input.attr('max');

    btnUp.click(function() {
      var oldValue = parseFloat(input.val());
      if (oldValue >= max) {
        var newVal = oldValue;
      } else {
        var newVal = oldValue + 1;
      }
      spinner.find("input").val(newVal);
      spinner.find("input").trigger("change");
    });

    btnDown.click(function() {
      var oldValue = parseFloat(input.val());
      if (oldValue <= min) {
        var newVal = oldValue;
      } else {
        var newVal = oldValue - 1;
      }
      spinner.find("input").val(newVal);
      spinner.find("input").trigger("change");
    });

  });


  // Ajax add to cart for variable products
  $(document).on('click', '.product_type_variable.add_to_cart_button', function(e) {
    e.preventDefault();

    // wc_add_to_cart_params is required to continue, ensure the object exists
    if ( typeof wc_add_to_cart_params === 'undefined' ) {
      return false;
    }

    $thisbutton = $(this);

    $thisbutton.removeClass('added');
    $thisbutton.addClass('loading');

    var data = {
      action: 'cph_variable_add_to_cart',
      product_id: $thisbutton.data('product_id'),
      quantity: $thisbutton.data('quantity'),
      variation_id: $thisbutton.data('variation_id'),
      variation: $thisbutton.data('variation')
    };


    // Trigger event
    $('body').trigger('adding_to_cart', [ $thisbutton, data ]);

    // AJAX add to cart action
    $.post( wc_add_to_cart_params.ajax_url, data, function(response) {
      response = JSON.parse(response);

      if (!response)
      return;

      if ( response.error && response.product_url ) {
        window.location = response.product_url;
        return;
      }

      var this_page = window.location.toString();
      this_page = this_page.replace( 'add-to-cart', 'added-to-cart' );

      $thisbutton.removeClass('loading');

      fragments = response.fragments;
      cart_hash = response.cart_hash;

      // Block fragments class
      if ( fragments ) {
        $.each(fragments, function(key, value) {
          $(key).addClass('updating');
        });
      }

      // Block widgets and fragments
      $('.shop_table.cart, .updating, .cart_totals,.widget_shopping_cart_top').fadeTo('400', '0.6').block({message: null, overlayCSS: {background: 'opacity: 0.6', backgroundSize: '16px 16px', opacity: 0.6 } } );

      // Changes button classes
      $thisbutton.addClass( 'added' );

      // View cart text
      if ( ! wc_add_to_cart_params.is_cart && $thisbutton.parent().find( '.added_to_cart' ).size() === 0 ) {
        $thisbutton.after( ' <a href="' + wc_add_to_cart_params.cart_url + '" class="added_to_cart wc-forward" title="' +
        wc_add_to_cart_params.i18n_view_cart + '">' + wc_add_to_cart_params.i18n_view_cart + '</a>' );
      }

      // Replace fragments
      if ( fragments ) {
        $.each(fragments, function(key, value) {
          $(key).replaceWith(value);
        });
      }

      // Unblock
      $('.widget_shopping_cart, .updating, .widget_shopping_cart_top').stop(true).css('opacity', '1').unblock();

      // Cart page elements
      $('.widget_shopping_cart_top').load( this_page + ' .widget_shopping_cart_top:eq(0) > *', function() {

        $("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").addClass('buttons_added').append('<input type="button" value="+" id="add1" class="plus" />').prepend('<input type="button" value="-" id="minus1" class="minus" />');

        $('.widget_shopping_cart_top').stop(true).css('opacity', '1').unblock();

        $('body').trigger('cart_page_refreshed');
      });

      // Cart page elements
      $('.shop_table.cart').load( this_page + ' .shop_table.cart:eq(0) > *', function() {

        $("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").addClass('buttons_added').append('<input type="button" value="+" id="add1" class="plus" />').prepend('<input type="button" value="-" id="minus1" class="minus" />');

        $('.shop_table.cart').stop(true).css('opacity', '1').unblock();

        $('body').trigger('cart_page_refreshed');
      });

      $('.cart_totals').load( this_page + ' .cart_totals:eq(0) > *', function() {
        $('.cart_totals').stop(true).css('opacity', '1').unblock();
      });

      // Trigger event so themes can refresh other areas
      $('body').trigger( 'added_to_cart', [ fragments, cart_hash ] );
    });

    return false;

  });
});
