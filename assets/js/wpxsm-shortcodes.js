/**
 * Shortocdes Admin Backend Area
 *
 * @class           WPXShortcodesManagerShortcodes
 * @author          wpXtreme, Inc.
 * @copyright       Copyright (C) 2013-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-07
 * @version         1.0.1
 */

jQuery( function ( $ )
{
  "use strict";

  window.WPXShortcodesManagerShortcodes = (function ()
  {

    /**
     * This Object
     *
     * @type {{}}
     */
    var $t = {
      version : '1.0.1',
      init    : _init
    };

    /**
     * Return an instance of WPXShortcodesManagerShortcodes object
     *
     * @return WPXShortcodesManagerShortcodes
     */
    function _init()
    {
      // Your init here
      _initSwipeEnable();
      _initSwipeShowInContent();
      _initEditView();

      return $t;
    }

    /**
     * Init the new/edit view
     * @private
     */
    function _initEditView()
    {
      // Get the form
      var $form = $( 'form#wpxsm-shortocde-edit' );

      // If exists
      if ( $form.length ) {

        // Input fields
        var $shortcode = $( 'input#shortcode_id' );
        var $shortcode_html = $( 'textarea#wpxsm_shortcode_html' );

        // Focus
        $shortcode.focus();
      }
    }

    /**
     * Init swipe for enable/disable
     *
     * @private
     */
    function _initSwipeEnable()
    {
      // Live...
      $( document ).on( WPDKUIComponentEvents.SWIPE_CHANGED, '[id^="swipe-enabled-"]', function ( el, knob, enabled )
      {
        // Ajax
        $.post( wpdk_i18n.ajaxURL, {
            action       : 'wpxsm_action_enable',
            shortcode_id : $( this ).data( 'userdata' ),
            enable       : enabled
          }, function ( data )
          {
            var response = new WPDKAjaxResponse( data );

            if ( empty( response.error ) ) {
              $( knob ).parents( 'tr' ).replaceWith( response.data.row );
              $( 'ul.subsubsub' ).replaceWith( response.data.views );

              // Ask wpXtreme refresh
              $( document ).trigger( WPXtremeAdmin.REFRESH_TABLE_ACTIONS );
            }
            else {
              alert( response.error );
            }
          }
        );
      } );
    }

    /**
     * Init swipe for show/hide int content
     *
     * @private
     */
    function _initSwipeShowInContent()
    {
      // Live...
      $( document ).on( WPDKUIComponentEvents.SWIPE_CHANGED, '[id^="swipe-show-in-content-"]', function ( el, knob, enabled )
      {
        // Ajax
        $.post( wpdk_i18n.ajaxURL, {
            action       : 'wpxsm_action_show_in_content',
            shortcode_id : $( this ).data( 'userdata' ),
            enable       : enabled
          }, function ( data )
          {
            var response = new WPDKAjaxResponse( data );

            if ( empty( response.error ) ) {
              $( knob ).parents( 'tr' ).replaceWith( response.data.row );
              $( 'ul.subsubsub' ).replaceWith( response.data.views );

              // Ask wpXtreme refresh
              $( document ).trigger( WPXtremeAdmin.REFRESH_TABLE_ACTIONS );
            }
            else {
              alert( response.error );
            }
          }
        );
      } );
    }

    return $t.init();

  })();

} );
