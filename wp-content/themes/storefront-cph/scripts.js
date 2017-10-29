jQuery(document).ready(function($) {
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
});
