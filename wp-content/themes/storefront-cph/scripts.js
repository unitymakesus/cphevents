jQuery(document).ready(function($) {

  function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
  };

  /*****************************************************************************
  * EVENT LIST PAGE
  *****************************************************************************/

  // Scroll to event identified in URL
  var event_id = getUrlParameter('event_id'),
      $event = $( '.product.post-' + event_id );
  if (event_id.length > 0) {
    $event.addClass('active');
    $( 'html, body' ).animate({
      scrollTop: ( $event.offset().top - 50 )
    }, 1000 );

    setTimeout(function() {
      $event.removeClass('active');
    }, 5000);
  }

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
  * CART PAGE
  *****************************************************************************/

  // Put all tickets as options to copy details from
  $('.woocommerce-cart-form .ticket-details').each(function() {
    var title = $(this).siblings('h3').text(),
        ticket = $(this).children('h4').text(),
        ticket_key = $(this).data('ticket-key');

    $('.woocommerce-cart-form .ticket-details:not([class*=' + ticket_key + ']) select.copy-data').each(function() {
      $(this).append('<option value="' + ticket_key + '">' + title + ': ' + ticket + '</option>');
    });
  });

  // Copy details from selected fields
  $('.woocommerce-cart-form .ticket-details').on('change', 'select.copy-data', function(e) {
    var which = $(this).val(),
        ticket = $(this).closest('.ticket-details'),
        ticket_key = ticket.data('ticket-key')
        account = $('#account-data');

    if (which !== '') {
      // Loop through fields that need replacing
      $(ticket).find('.form-row:not(.control-copy)').each(function() {
        // Get global name of field
        var field = $(this).attr('id').replace(ticket_key + '_', '').replace('_field', '');

        // If copying from account details
        if (which == 'account') {
          if (typeof account.data(field) !== "undefined") {
            $(ticket).find('[name$=' + field + ']').val(account.data(field));
          }
        } else {
          if ((field == 'teacher' || field == 'gaa')) {
            // Handle checkbox fields (teacher and gaa are checkboxes)
            $(ticket).find('[name$=' + field + ']').prop('checked', $('[class*=' + which + ']').find('[name$=' + field + ']').prop('checked')).trigger('change');
          } else {
            // Handle input and select fields
            $(ticket).find('[name$=' + field + ']').val($('[class*=' + which + ']').find('[name$=' + field + ']').val());
          }
        }

        if (ticket.find('select.state_select').length !== 0) {
          ticket.find('select.state_select').trigger('change');
        }
      });
    }
  });

  // Set conditional fields to display if checked on init
  $('.validation-checkbox input[type="checkbox"]').each(function() {
    if ($(this).is(':checked')) {
      $(this).closest('.discount-validation').find('.hidden-fields').show();
    }
  });

  // Inline validation
  $('.woocommerce-cart-form').on( 'input validate change', '.input-text, select, input:checkbox', function( e ) {
		var $this             = $( this ),
			$parent           = $this.closest( '.form-row' ),
			validated         = true,
			validate_required = $parent.is( '.validate-required' ),
			validate_email    = $parent.is( '.validate-email' ),
			event_type        = e.type;

		if ( 'input' === event_type ) {
			$parent.removeClass( 'woocommerce-invalid woocommerce-invalid-required-field woocommerce-invalid-email woocommerce-validated' );
		}

		if ( 'validate' === event_type || 'change' === event_type ) {

			if ( validate_required ) {
				if ( 'checkbox' === $this.attr( 'type' ) && ! $this.is( ':checked' ) ) {
					$parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-required-field' );
					validated = false;
				} else if ( $this.val() === '' ) {
					$parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-required-field' );
					validated = false;
				}
			}

			if ( validate_email ) {
				if ( $this.val() ) {
					/* https://stackoverflow.com/questions/2855865/jquery-validate-e-mail-address-regex */
					var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);

					if ( ! pattern.test( $this.val()  ) ) {
						$parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-email' );
						validated = false;
					}
				}
			}

			if ( validated ) {
				$parent.removeClass( 'woocommerce-invalid woocommerce-invalid-required-field woocommerce-invalid-email' ).addClass( 'woocommerce-validated' );
			}
		}
	});

  // Handle saving custom fields to session data
  $('.cart-collaterals .wc-proceed-to-checkout a.checkout-button').on('click', function(e) {
    e.preventDefault();

    var custom_fields = []
        checkout_page = $(this).attr('href');

    // Send all ticket details to array
    $('.ticket-details').each(function(){
      meta = {
        'product_id' : $(this).data('product'),
        'ticket_key' : $(this).data('ticket-key'),
        'first_name' : $(this).find('input[name$="_first_name"]').val(),
        'last_name' : $(this).find('input[name$="_last_name"]').val(),
        'address_1' : $(this).find('input[name$="_address_1"]').val(),
        'address_2' : $(this).find('input[name$="_address_2"]').val(),
        'city' : $(this).find('input[name$="_city"]').val(),
        'state' : $(this).find('input[name$="_state"]').val(),
        'postcode' : $(this).find('input[name$="_postcode"]').val(),
        'phone' : $(this).find('input[name$="_phone"]').val(),
        'email' : $(this).find('input[name$="_email"]').val(),
        'special_needs' : $(this).find('input[name$="_special_needs"]').val(),
      };

      var $teacher_checkbox = $(this).find('input[name$="_teacher"]');
      if ($teacher_checkbox.is(':checked')) {
        meta['teacher'] = $teacher_checkbox.val();
        meta['teacher_type'] = $(this).find('select[name$="_teacher_type"]').val();
        meta['teacher_school'] = $(this).find('input[name$="_teacher_school"]').val();
        meta['teacher_county'] = $(this).find('input[name$="_teacher_county"]').val();
      }

      var $gaa_checkbox = $(this).find('input[name$="_gaa"]');
      if ($gaa_checkbox.is(':checked')) {
        meta['gaa'] = $gaa_checkbox.val();

        var $gaa_discount_seminar = $(this).find('input[name$="_gaa_discount_seminar"]');
        if ($gaa_discount_seminar.is(':checked')) {
          meta['gaa_discount_seminar'] = $gaa_discount_seminar.val();
          meta['gaa_type'] = $(this).find('select[name$="_gaa_type"]').val();
        }

        var $gaa_discount_flyleaf = $(this).find('input[name$="_gaa_discount_flyleaf"]');
        if ($gaa_discount_flyleaf.is(':checked')) {
          meta['gaa_discount_flyleaf'] = $gaa_discount_flyleaf.val();
          meta['gaa_type'] = $(this).find('select[name$="_gaa_type"]').val();
        }

        var $gaa_discount_bulk_flyleaf = $(this).find('input[name$="_gaa_discount_bulk_flyleaf"]');
        if ($gaa_discount_bulk_flyleaf.is(':checked')) {
          meta['gaa_discount_bulk_flyleaf'] = $gaa_discount_bulk_flyleaf.val();
          meta['gaa_type'] = $(this).find('select[name$="_gaa_type"]').val();
        }
      }

      custom_fields.push(meta);
    });

    var data = {
      'action': 'cph_update_cart_meta',
      'custom_fields': custom_fields
    };

    $.post(wc_cart_params.ajax_url, data, function (response) {
      console.log(response);

      if (response.error) {
        // Display errors
        $('.woocommerce-NoticeGroup').html(response.messages);

        // Scroll up to errors
        $( 'html, body' ).animate({
  				scrollTop: ( $( 'form.woocommerce-cart-form' ).offset().top - 100 )
  			}, 1000 );
      } else {
        // Go to next checkout page
        window.location.href = checkout_page;
  			return;
      }
    });
  });

  /*****************************************************************************
  * CHECKOUT PAGE
  *****************************************************************************/

  // Apply discounts if teacher and GAA fields validate
  $('.discount-validation').each(function() {
    // Number of fields to validate
    $(this).data('n', $(this).find('input, select').length);

    // Set up data storage for tracking number of fields that validate
    $(this).data('x', 0);

    // Display teacher and GAA conditional fields
    $(this).find('.validation-checkbox input[type="checkbox"]').on('change', function() {
      if ($(this).is(':checked')) {
        $(this).closest('.discount-validation').find('.hidden-fields').show();
      } else {
        $(this).closest('.discount-validation').find('.hidden-fields').hide();
      }
    });

  });

  var updateTimer,dirtyInput = false,xhr;

  function update_order_review_table(billingstate,billingcountry) {
    if ( xhr ) xhr.abort();

    $( '#order_methods, #order_review' ).block({ message: null, overlayCSS: { background: '#fff url(' + wc_checkout_params.ajax_loader_url + ') no-repeat center', backgroundSize:'16px 16px', opacity: 0.6 } });

    var data = {
      action: 'woocommerce_update_order_review',
      security: wc_checkout_params.update_order_review_nonce,
      billing_state: billingstate,
      billing_country : billingcountry,
      post_data: $( 'form.checkout' ).serialize()
    };

    xhr = $.ajax({
      type: 'POST',
      url: wc_checkout_params.ajax_url,
      data: data,
      success: function( response ) {
        var order_output = $(response);
        $( '#order_review' ).html( response['fragments']['.woocommerce-checkout-review-order-table']+response['fragments']['.woocommerce-checkout-payment']);
        $('body').trigger('update_checkout');
      },
      error: function(code){
        console.log('ERROR');
      }
    });
  }

  jQuery('.state_select').change(function(e, params){
    update_order_review_table(jQuery(this).val(),jQuery('#billing_country').val());
  });

  /*****************************************************************************
  * ERROR PAGE
  *****************************************************************************/

  // Replace [EXT_TRANS_ID] with the ID from URL param
  var EXT_TRANS_ID = getUrlParameter('EXT_TRANS_ID');
  console.info('id', EXT_TRANS_ID);
  if (EXT_TRANS_ID.length > 0) {
    $('.entry-content p').each(function() {
      $(this).html(function(index, text) {
        return text.replace("[EXT_TRANS_ID]", EXT_TRANS_ID);
      });
    });
  }

});
