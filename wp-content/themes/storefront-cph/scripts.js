jQuery(document).ready(function($) {

  /*****************************************************************************
  * EVENT LIST PAGE
  *****************************************************************************/

  // Increase quantity
  $(document).on('click', '.cph-quantity-up', function() {
    var input = $(this).siblings('input[type="number"]'),
        oldVal = parseInt(input.val()),
        step = parseInt(input.attr('step')),
        max = input.attr('max');
    if (oldVal >= max) {
      input.val(oldVal);
    } else {
      input.val(oldVal + step);
    }
    input.trigger('change');
  });

  // Decrease quantity
  $(document).on('click', '.cph-quantity-down', function() {
    var input = $(this).siblings('input[type="number"]'),
        oldVal = parseInt(input.val()),
        step = parseInt(input.attr('step')),
        min = input.attr('min');
    if (oldVal <= min) {
      input.val(oldVal);
    } else {
      input.val(oldVal - step);
    }
    input.trigger('change');
  });

  // Ajax quantity adjuster
  $(document).on('change', '.cph-quantity input', function(e) {
    e.preventDefault();

    // wc_add_to_cart_params is required to continue, ensure the object exists
    if ( typeof wc_add_to_cart_params === 'undefined' ) {
      return false;
    }

    $thisinput = $(this);

    var data = {
      action: 'cph_variable_add_to_cart',
      product_id: $thisinput.data('product_id'),
      quantity: $thisinput.val(),
      variation_id: $thisinput.data('variation_id'),
      variation: $thisinput.data('variation')
    };

    // Add to cart
    cph_add_to_cart_now($thisinput.closest('.cph-quantity'), data);
  });

  // Ajax add to cart for variable products
  $(document).on('click', '.ajax_add_to_cart', function(e) {
    e.preventDefault();

    // wc_add_to_cart_params is required to continue, ensure the object exists
    if ( typeof wc_add_to_cart_params === 'undefined' ) {
      return false;
    }

    $thisbutton = $(this);

    var data = {
      action: 'cph_variable_add_to_cart',
      product_id: $thisbutton.data('product_id'),
      quantity: $thisbutton.data('quantity'),
      variation_id: $thisbutton.data('variation_id'),
      variation: $thisbutton.data('variation')
    };

    // Add to cart
    cph_add_to_cart_now($thisbutton, data);
  });

  // Ajax function that adjusts cart quantity for product
  function cph_add_to_cart_now($thistrigger, data) {

    $thistrigger.removeClass('added');
    $thistrigger.addClass('loading');

    // Trigger event
    $('body').trigger('adding_to_cart', [ $thistrigger, data ]);

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

      $thistrigger.removeClass('loading');

      fragments = response.fragments;
      cart_hash = response.cart_hash;
      cart_function = response.cart_function;

      // Block fragments class
      if ( fragments ) {
        $.each(fragments, function(key, value) {
          $(key).addClass('updating');
        });
      }

      // Block widgets and fragments
      $('.shop_table.cart, .updating, .cart_totals, .widget_shopping_cart_top').fadeTo('400', '0.6').block({message: null, overlayCSS: {background: 'opacity: 0.6', backgroundSize: '16px 16px', opacity: 0.6 } } );

      // Change button/input if necessary
      if ( cart_function == 'add_to_cart' ) {
        $thistrigger.replaceWith('<div class="cph-quantity">' +
          '<input type="number" min="0" step="1" value="' + data.quantity + '"' +
            'data-product_id="' + data.product_id + '"' +
            'data-variation_id="' + data.variation_id + '"' +
            'data-variation=\'' + JSON.stringify(data.variation) + '\' />' +
          '<button class="cph-quantity-up">+</button>' +
          '<button class="cph-quantity-down">-</button>' +
        '</div>');
      } else if ( cart_function == 'remove_cart_item' ) {
        $thistrigger.replaceWith('<a rel="nofollow"' +
          'href="/?add-to-cart=' + data.product_id + '"' +
          'data-quantity="1"' +
          'data-product_id="' + data.product_id + '"' +
          'data-variation_id="' + data.variation_id + '"' +
          'data-variation=\'' + JSON.stringify(data.variation) + '\'' +
          'class="button ajax_add_to_cart">Add to cart</a>');
      }

      // Changes button classes
      $thistrigger.addClass( 'added' );

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
  }


  /*****************************************************************************
  * CHECKOUT PAGE
  *****************************************************************************/

  // Put all tickets as options to copy details from
  $('.woocommerce-checkout .ticket-details').each(function() {
    var title = $(this).siblings('h3').text(),
        ticket = $(this).children('h4').text(),
        ticket_key = $(this).data('ticket-key');

    $('.woocommerce-checkout .ticket-details:not([class*=' + ticket_key + ']) select.copy-data').each(function() {
      $(this).append('<option value="' + ticket_key + '">' + title + ': ' + ticket + '</option>');
    });
  });

  // Copy details from selected fields
  $('.woocommerce-checkout .ticket-details').on('change', 'select.copy-data', function(e) {
    var which = $(this).val(),
        ticket = $(this).closest('.ticket-details'),
        ticket_key = ticket.data('ticket-key');

    if (which !== '') {
      $(ticket).find('.form-row:not(.control-copy)').each(function() {
        var field = $(this).attr('id').replace(ticket_key, '').replace('_field', '');
        $(ticket).find('[name$=' + field + ']').val($('[class*=' + which + ']').find('[name$=' + field + ']').val());

        if (ticket.find('select.state_select').length !== 0) {
          ticket.find('select.state_select').trigger('change');
        }
      });
    }
  });

  function adjust_valid_fields($which, amount) {
    var current_x = $which.closest('.discount-validation').data('x');
    $which.closest('.discount-validation').data('x', current_x + amount);
  }

  function check_valid_fields($which) {
    var x = $which.closest('.discount-validation').data('x');
    var n = $which.closest('.discount-validation').data('n');

    if (x == n) {
      // Add coupon code
      var data = {
        action: 'cph_add_discount',
        product_id: $which.closest('.ticket-details').data('product'),
        discount_type: $which.closest('.discount-validation').data('discount-type')
      };

    } else {
      // Remove coupon code
      var data = {
        action: 'cph_remove_discount',
        product_id: $which.closest('.ticket-details').data('product'),
        discount_type: $which.closest('.discount-validation').data('discount-type'),
        original_price: $which.closest('.discount-validation').data('original-price')
      };
    }

    $.post( wc_checkout_params.ajax_url, data, function(response) {
      response = JSON.parse(response);

      if (!response)
      return;

      console.log(response);
    });
  }

  // Apply discounts if teacher and GAA fields validate
  $('.discount-validation').each(function() {
    // Number of fields to validate
    $(this).data('n', $(this).find('input, select').length);

    // Set up data storage for tracking number of fields that validate
    $(this).data('x', 0);

    // Display teacher and GAA conditional fields
    $(this).find('.validation-checkbox input[type="checkbox"]').on('change', function() {
      if ($(this).is(':checked')) {
        // adjust_valid_fields($(this), 1);
        $(this).closest('.discount-validation').find('.hidden-fields').show();

        var data = {
          action: 'cph_add_discount',
          product_id: $(this).closest('.ticket-details').data('product'),
          discount_type: $(this).closest('.discount-validation').data('discount-type')
        };

        $.post( wc_checkout_params.ajax_url, data, function(response) {
          console.log(response);

          if (!response)
          return;

          $('body').trigger('update_checkout');
        });

      } else {
        // adjust_valid_fields($(this), -1);
        $(this).closest('.discount-validation').find('.hidden-fields').hide();

        var data = {
          action: 'cph_remove_discount',
          product_id: $(this).closest('.ticket-details').data('product'),
          discount_type: $(this).closest('.discount-validation').data('discount-type'),
          original_price: $(this).closest('.discount-validation').data('original-price')
        };
      }

      // Check that valid fields match total fields
      // check_valid_fields($(this));
    });

    // Adjust number of valid fields when these change
    // $(this).find('.hidden-fields input, .hidden-fields select').on('change', function() {
    //   if($(this).val()) {
    //     adjust_valid_fields($(this), 1);
    //   } else {
    //     adjust_valid_fields($(this), -1);
    //   }
    //
    //   // Check that valid fields match total fields
    //   check_valid_fields($(this));
    // });

  });

});
