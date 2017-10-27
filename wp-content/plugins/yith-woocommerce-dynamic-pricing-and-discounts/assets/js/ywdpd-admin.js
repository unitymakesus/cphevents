/**
 * admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Subscription
 * @version 1.0.0
 */

jQuery(document).ready( function($) {
    "use strict";

    var wrapper         = $( document ).find( '.ywdpd-sections-group' ),
        container       = wrapper.find( '.ywdpd-section' ),
        head            = container.find( '.section-head' ),
        remove          = head.find( '.ywdpd-remove'),
        active          = head.find( '.ywdpd-active'),
        eventType       = container.find( '.yith-ywdpd-eventype-select'),
        block_loader    = ( typeof yith_ywdpd_admin !== 'undefined' ) ? yith_ywdpd_admin.block_loader : false,
        error_msg       = ( typeof yith_ywdpd_admin !== 'undefined' ) ? yith_ywdpd_admin.error_msg : false,
        del_msg         = ( typeof yith_ywdpd_admin !== 'undefined' ) ? yith_ywdpd_admin.del_msg : false,

        input_section   = $( '#yith-ywdpd-add-section' ),
        add_section     = $( '#yith-ywdpd-add-section-button'),


    /****
     * Open function
     */
    open_func = function( head ){
        head.on( 'click', function(){
            var t            = $(this);
            t.parents( '.ywdpd-section' ).toggleClass( 'open' );
            t.next( '.section-body' ).slideToggle();
        });
    },

    /****
     * Remove function
     */
    remove_func = function( remove ) {

        remove.on('click', function (e) {
            e.stopPropagation();

            var t           = $(this),
                section     = t.data('section'),
                container   = t.parents('.ywdpd-section' ),
                confirm     = window.confirm( del_msg );

            if ( confirm == true ) {

                if (block_loader) {
                    container.block({
                        message   : null,
                        overlayCSS: {
                            background: '#fff url(' + block_loader + ') no-repeat center',
                            opacity   : 0.5,
                            cursor    : 'none'
                        }
                    });
                }

                $.post(yith_ywdpd_admin.ajaxurl, {
                    action : 'yith_dynamic_pricing_section_remove',
                    section: section
                }, function (resp) {
                    container.remove();
                })
            }

        })
    },

    /****
     * Active function
     */
    active_func = function( active ) {

        active.on('click', function (e) {

            e.stopPropagation();

            var t           = $(this),
                section     = t.data('section'),
                active_field = t.parents('.section-head').find('.active-hidden-field');

            if ( t.hasClass( 'activated' ) ) {
                t.removeClass('activated');
                active_field.val( 'no' );
            }else{
                t.addClass('activated');
                active_field.val( 'yes' );
            }

        })
    },

    /****
     * Deps function option
     */
    deps_func = function( eventType ) {
        eventType.each( function(){
            var t           = $(this),
                selected    = t.find( 'option:selected' );

            hide_show_func( t, selected.val() );

            t.on( 'change', function(){
                selected = t.find( 'option:selected' );
                hide_show_func( t, selected.val() );
            })
        });
    },

    hide_show_func = function( t, val ) {

        var  opt_apply_to    = t.parents('.ywdpd-section').find( 'tr.deps-apply_to' );

        if( val == 'categories' ) {
            opt_apply_to.show();
        }else {
            opt_apply_to.hide();
        }
    },

    category_search = function( element ){

        // Category Ajax Search
        element.ajaxChosen({
            method: 	'GET',
            url: 		yith_ywdpd_admin.ajaxurl,
            dataType: 	'json',
            afterTypeDelay: 100,
            data:		{
                action:    'ywdpd_category_search',
                security:  yith_ywdpd_admin.search_categories_nonce
            }
        }, function (data) {
            var terms = {};

            $.each(data, function (i, val) {
                terms[i] = val;
            });

            return terms;
        });
    };


    add_section.on( 'click', function(e) {
        e.preventDefault();

        var t       = $(this),
            id      = t.data( 'section_id'),
            name    = t.data( 'section_name' ),
            title   = input_section.val();

        if( title == '' ) {
            if( error_msg ) {
                t.siblings( '.ywdpd-error-input-section' ).html( error_msg );
            }
        }
        else {
            $.post( yith_ywdpd_admin.ajaxurl, { action: 'yith_dynamic_pricing_section', section: title, id: id, name: name}, function( resp ) {
                // empty input
                input_section.val('');


                // remove error msg if any
                $( '.ywdpd-error-input-section' ).remove();

                wrapper.append( resp );

                var container       = wrapper.find( '.ywdpd-section').last(),
                    head            = $(container).find( '.section-head' ),
                    eventType       = container.find( '.yith-ywdpd-eventype-select'),
                    active          = container.find( '.ywdpd-active'),
                    remove          = container.find( '.ywdpd-remove');


                // re-init
                container.find( 'select').chosen({
                    width: '100%',
                    disable_search: true
                });

                open_func( head );
                deps_func( eventType );
                remove_func( remove );
                active_func( active );
                container.find('.ajax_chosen_select_categories').each( function(){
                    category_search( $(this) );
                });
            })
        }
    });



	// Sorting
    jQuery('.ywdpd-sections-group').sortable({
        items:'.ywdpd-section',
        cursor:'move',
        axis:'y',
        handle: '.section-head',
        scrollSensitivity:40,
        forcePlaceholderSize: true,
        helper: 'clone',
        start:function(event,ui){
            ui.item.css('background-color','#f6f6f6');
        },
        stop:function(event,ui){
            ui.item.removeAttr('style');
        }
    });



    /****
     * Upload Button
     */
    $( document ).on( 'click', '.upload_img_button', function(e) {
        e.preventDefault();

        var t = $(this),
            custom_uploader;

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on( 'select' , function() {
            var attachment = custom_uploader.state().get( 'selection' ).first().toJSON(),
                input_text = t.prev( '.upload_img_url' );

            input_text.val( attachment.url );
        });

        //Open the uploader dialog
        custom_uploader.open();

    });


    // init
    open_func( head );
    remove_func( remove );
    active_func( active );
    deps_func( eventType );

    container.find('.ajax_chosen_select_categories').each( function(){
         category_search( $(this) );
    });

    container.find( 'select').chosen({
        width: '100%',
        disable_search: false
    });

});
