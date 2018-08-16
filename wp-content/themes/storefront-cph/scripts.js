jQuery(document).ready(function($) {

  function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
  };

  $(".storefront-primary-navigation").sticky({topSpacing:0});

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

  // On page load, make sure all guest data is available
  if ($('body').hasClass('woocommerce-cart')) {
    $('.woocommerce-cart-form select.copy-data').each(function() {
      $(this).children('option[value]').not('[value=new]').each(function() {
        var ticket = $(this).closest('.ticket-details').data('ticket-key'),
            full_name = $(this).text(),
            sanitized_name = $(this).attr('value'),
            guest_data = $('#guest-data');

        // Add new guest data to DOM
        var guest = guest_data.find('[data-ticket-name="' + sanitized_name + '"]');
        if (guest.length == 0) {
          setup_guest_data('new', ticket, full_name, sanitized_name, true, false);
        }
      });
    });
  }

  // Logic to determine if they are eligible for GAA discount opt-in
  function guest_can_gaa_disc(category, teacher, gaa_member) {
    if (category == 'adventures-in-ideas-seminar' || category == 'dialogues-seminar') {
      if (gaa_member == true && teacher == false) {
        return true;
      } else {
        return false;
      }
    }
  }

  // Function that adds new guest to the ticket options
  function setup_guest_data(handler, ticket, full_name, sanitized_name, init, hidden_set) {
    var all_fields = [],
        all_valid = [],
        ticket_details = $('.ticket-details[data-ticket-key=' + ticket + ']'),
        ticket_selects = $('.woocommerce-cart-form select.copy-data');

    ticket_details.find('.form-row').not('.control-copy').each(function() {

      var row = $(this),
          field = row.attr('id').match(/ticket_\d+_(.*)_field/);

      row.find('.input-text, select, input:checkbox').each(function() {

        // Save field/value pairs to array
        if ($(this).is('input:checkbox')) {
          // Handle check boxes
          if ($(this).is(':checked')) {
            all_fields[field[1]] = 1;
          } else {
            all_fields[field[1]] = 0;
          }
        } else {
          all_fields[field[1]] = $(this).val();
        }

        // Validate required fields
        if (row.hasClass('validate-required') && init==false) {
          $(this).trigger('validate').promise().done(function() {
            // If this validates, add 1 to counting array
            if ($(this).closest('.form-row').hasClass('woocommerce-validated')) {
              all_valid.push(1);
            } else {
              all_valid.push(0);
            }
          });
        } else {
          all_valid.push(1);
        }
      });

    }).promise().done(function() {

      // Spread all values in array and find if there are any 0s
      if (Math.min.apply(null, all_valid) == 0) {
        return false;
      } else {
        // If all fields validate

        // Handle new guest
        if (handler == 'new') {

          // Add new guest to all dropdowns
          ticket_selects.each(function() {
            var match = $(this).find('option[value="' + sanitized_name + '"]');
            if (match.length == 0) {
              $(this).find('option[value="new"]').before('<option value="' + sanitized_name + '">' + full_name + '</option>');
            }
          }).promise().done(function() {
            // Select new guest for this ticket
            if (init==false) {
              $('select.copy-data[id*="' + ticket + '"]').val(sanitized_name);
              ticket_details.find('.hidden-fields').removeClass('visible');
              $('.ticket-details[data-ticket-key=' + ticket + '] .edit-guest').removeClass('hide');
            }
          });

          // Add new guest data to DOM
          var new_guest = $('<div data-ticket-name="' + sanitized_name + '"></div>');
          for (field in all_fields) {
            if (typeof all_fields[field] !== 'function') {
              new_guest.attr('data-' + field, all_fields[field]);
            }
          }
          $('#guest-data').append(new_guest);

        } else {

          // Clean up teacher and GAA fields
          if (all_fields['teacher'] == false) {
            all_fields['teacher_type'] = '';
            all_fields['teacher_school'] = '';
            all_fields['teacher_county'] = '';
          }

          if (all_fields['gaa'] == false) {
            all_fields['gaa_type'] = '';
          }

          all_fields['gaa_discount_bulk_flyleaf'] = '';
          all_fields['gaa_discount_flyleaf'] = '';
          all_fields['gaa_discount_seminar'] = '';

          // Update master instance of this guest's info
          for (field in all_fields) {
            if (typeof all_fields[field] !== 'function') {
              handler.attr('data-' + field, all_fields[field]);
            }
          }

          // Update all instances of this guest's tickets
          ticket_selects.each(function() {
            if ($(this).val() == sanitized_name) {
              update_ticket_data(sanitized_name, $(this).closest('.ticket-details'));
            }
          });
        }

        // Show relevant GAA opt-in discount options
        if (guest_can_gaa_disc(ticket_details.attr('data-category'), all_fields['teacher'], all_fields['gaa'])) {
          hidden_set.siblings('.gaa-opt-in').addClass('visible');
        } else {
          hidden_set.siblings('.gaa-opt-in').removeClass('visible');
        }

        // Hide guest info fields
        if (hidden_set.length) {
          hidden_set.removeClass('visible');
          ticket_details.find('.edit-guest').removeClass('hide');
        }

      }
    });
  }

  // Function that updates the ticket info with master data
  function update_ticket_data(sanitized_name, ticket) {
    var guest_data = $('#guest-data'),
        guest = guest_data.find('[data-ticket-name="' + sanitized_name + '"]');

    ticket.find('.form-row:not(.control-copy)').each(function() {
      // Get global name of field
      var check = "false",
          field = $(this).attr('id').match(/ticket_\d+_(.*)_field/);

      // Reset validation
      $(this).removeClass('woocommerce-validated woocommerce-invalid woocommerce-invalid-required-field');

      if (sanitized_name == 'new') {

        // Reset fields to blank/unchecked
        if ($(this).find('[name$=' + field[1] + ']').is(':checkbox')) {
          $(this).find('[name$=' + field[1] + ']').prop('checked', false);
        } else {
          $(this).find('[name$=' + field[1] + ']').val('');
        }

      } else {

        // Update fields with master guest info
        if ($(this).find('[name$=' + field[1] + ']').is(':checkbox')) {
          if (field[1].indexOf('gaa_discount') == -1) {
            check = (guest.attr('data-' + field[1]) == 1 ? true : false );
            $(this).find('[name$=' + field[1] + ']').prop('checked', check);
          }
        } else {
          $(this).find('[name$=' + field[1] + ']').val(guest.attr('data-' + field[1]));
        }

        // Show conditional fields
        if ($(this).hasClass('validation-checkbox')) {
          $(this).find('input[type="checkbox"]').each(function() {
            if ($(this).is(':checked')) {
              $(this).closest('.validation-checkbox').siblings('.conditional-fields').addClass('visible');
            } else {
              $(this).closest('.validation-checkbox').siblings('.conditional-fields').removeClass('visible');
            }
          });
        }

      }

      // Woo function (is this necessary?)
      if (ticket.find('select.state_select').length !== 0) {
        ticket.find('select.state_select').trigger('change');
      }
    });
  }

  // When the update button is clicked, either update guest data or
  // add new guest as ticket option
  $('.woocommerce-cart-form .ticket-update').on('click', function(e) {
    e.preventDefault();

    var ticket = $(this).data('ticket'),
        ticket_details = $('.ticket-details[data-ticket-key=' + ticket + ']'),
        first_name = ticket_details.find('[id*="first_name"]').find('input[type="text"]').val(),
        last_name = ticket_details.find('[id*="last_name"]').find('input[type="text"]').val(),
        full_name = first_name + ' ' + last_name,
        sanitized_name = full_name.toLowerCase().replace(/[^a-z0-9 _-]/g, '').replace(/\s+/g, '-'),
        find_guest = $('#guest-data').find('[data-ticket-name="' + sanitized_name + '"]'),
        hidden_set = $(this).closest('.hidden-fields');

    $(ticket).find('.edit-guest').removeClass('hide');

    // Check if this is existing guest
    if (find_guest.length > 0) {
      // Update guest info
      setup_guest_data(find_guest, ticket, full_name, sanitized_name, false, hidden_set);
    } else {
      // We gotta set up a new guest
      setup_guest_data('new', ticket, full_name, sanitized_name, false, hidden_set);
    }

  });

  // Set guest data on select
  $('.woocommerce-cart-form .ticket-details').on('change', 'select.copy-data', function(e) {
    var sanitized_name = $(this).val(),
        ticket = $(this).closest('.ticket-details');

    // Update fields to match selection
    update_ticket_data(sanitized_name, ticket);

    if (sanitized_name == 'new') {
      // Show guest info fields
      $(ticket).find('.hidden-fields').addClass('visible');
      $(ticket).find('.edit-guest').addClass('hide');
    } else {
      // Hide guest info fields
      $(ticket).find('.hidden-fields').removeClass('visible');
      $(ticket).find('.edit-guest').removeClass('hide');
    }
  });

  // Show guest info on edit link click
  $('.woocommerce-cart-form .ticket-details a.edit-guest').on('click', function(e) {
    e.preventDefault();

    $(this).closest('.ticket-details').find('.hidden-fields.contact-info').addClass('visible');
    $(this).addClass('hide');
  });

  // Hide guest info on cancel button click
  $('.woocommerce-cart-form .ticket-details button.cancel').on('click', function(e) {
    e.preventDefault();

    var ticket = $(this).closest('.ticket-details'),
        discount_val = $(this).closest('.discount-validation');

    // if (discount_val.length) {
    //   discount_val.find('input[type="checkbox"]').prop('checked', false);
      // $(ticket).find('.edit-discount').removeClass('hide');
    // }

    $(this).closest('.hidden-fields').removeClass('visible');
    $(ticket).find('.edit-guest').removeClass('hide');
  });


  // Set conditional fields to display if checked on init
  $('.ticket-details').each(function() {
    $(this).find('.validation-checkbox input[type="checkbox"]').each(function() {
      if ($(this).is(':checked')) {
        $(this).closest('.validation-checkbox').siblings('.conditional-fields').addClass('visible');
      } else {
        $(this).closest('.validation-checkbox').siblings('.conditional-fields').removeClass('visible');
      }
    });

    var category = $(this).attr('data-category'),
        teacher = $(this).find('input[name$="_teacher"]').val(),
        gaa_member = $(this).find('input[name$="_gaa"]').val();

    if (guest_can_gaa_disc(category, teacher, gaa_member)) {
      $(this).find('.gaa-opt-in').addClass('visible');
    } else {
      $(this).find('.gaa-opt-in').removeClass('visible');
    }
  });

  // Show/hide conditional fields
  $('.validation-checkbox input[type="checkbox"]').on('change', function() {
    var hidden_fields = $(this).closest('.discount-validation').children('.conditional-fields');

    if ($(this).is(':checked')) {
      hidden_fields.addClass('visible');
      hidden_fields.children('.form-row').addClass('validate-required');
    } else {
      hidden_fields.removeClass('visible');
      hidden_fields.children('.form-row').removeClass('validate-required');
      // $(this).siblings('.edit-discount').addClass('hide');
    }
  });

  // $('.woocommerce-cart-form .discount-validation a.edit-discount').on('click', function(e) {
  //   e.preventDefault();
  //
  //   $(this).closest('.discount-validation').children('.hidden-fields').addClass('visible');
  //   $(this).addClass('hide');
  // });

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
        'special_needs' : $(this).find('textarea[name$="_special_needs"]').val(),
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
        meta['gaa_type'] = $(this).find('select[name$="_gaa_type"]').val();

        var $gaa_discount_seminar = $(this).find('input[name$="_gaa_discount_seminar"]');
        if ($gaa_discount_seminar.is(':checked')) {
          meta['gaa_discount_seminar'] = $gaa_discount_seminar.val();
        }

        var $gaa_discount_flyleaf = $(this).find('input[name$="_gaa_discount_flyleaf"]');
        if ($gaa_discount_flyleaf.is(':checked')) {
          meta['gaa_discount_flyleaf'] = $gaa_discount_flyleaf.val();
        }

        var $gaa_discount_bulk_flyleaf = $(this).find('input[name$="_gaa_discount_bulk_flyleaf"]');
        if ($gaa_discount_bulk_flyleaf.is(':checked')) {
          meta['gaa_discount_bulk_flyleaf'] = $gaa_discount_bulk_flyleaf.val();
        }
      }

      custom_fields.push(meta);
    });

    var data = {
      'action': 'cph_update_cart_meta',
      'custom_fields': custom_fields
    };

    $.post(wc_cart_params.ajax_url, data, function (response) {
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

  // var updateTimer,dirtyInput = false,xhr;
  //
  // function update_order_review_table(billingstate,billingcountry) {
  //   if ( xhr ) xhr.abort();
  //
  //   $( '#order_methods, #order_review' ).block({ message: null, overlayCSS: { background: '#fff url(' + wc_checkout_params.ajax_loader_url + ') no-repeat center', backgroundSize:'16px 16px', opacity: 0.6 } });
  //
  //   var data = {
  //     action: 'woocommerce_update_order_review',
  //     security: wc_checkout_params.update_order_review_nonce,
  //     billing_state: billingstate,
  //     billing_country : billingcountry,
  //     post_data: $( 'form.checkout' ).serialize()
  //   };
  //
  //   xhr = $.ajax({
  //     type: 'POST',
  //     url: wc_checkout_params.ajax_url,
  //     data: data,
  //     success: function( response ) {
  //       var order_output = $(response);
  //       $( '#order_review' ).html( response['fragments']['.woocommerce-checkout-review-order-table']+response['fragments']['.woocommerce-checkout-payment']);
  //       $('body').trigger('update_checkout');
  //     },
  //     error: function(code){
  //       console.log('ERROR');
  //     }
  //   });
  // }
  //
  // jQuery('.state_select').change(function(e, params){
  //   update_order_review_table(jQuery(this).val(),jQuery('#billing_country').val());
  // });

  /*****************************************************************************
  * ERROR PAGE
  *****************************************************************************/

  // Replace [EXT_TRANS_ID] with the ID from URL param
  var EXT_TRANS_ID = getUrlParameter('EXT_TRANS_ID');
  if (EXT_TRANS_ID.length > 0) {
    $('.entry-content p').each(function() {
      $(this).html(function(index, text) {
        return text.replace("[EXT_TRANS_ID]", EXT_TRANS_ID);
      });
    });
  }

});
