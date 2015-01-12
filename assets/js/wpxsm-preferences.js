/**
 * Preferences
 *
 * @class           WPXShortcodesManagerPreferences
 * @author          wpXtreme, Inc.
 * @copyright       Copyright (C) 2013-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-27
 * @version         1.0.0
 */

jQuery( function ( $ )
{
  "use strict";

  window.WPXShortcodesManagerPreferences = (function ()
  {

    /**
     * This Object
     *
     * @type {{}}
     */
    var $t = {
      version : '1.0.0',
      init    : _init
    };

    /**
     * Return an instance of WPXShortcodesManager object
     *
     * @return WPXShortcodesManager
     */
    function _init ()
    {
      /* Your init here. */
      $( '#value_one' ).change( function ()
      {
        alert( 'This event handler is in the Javascript file under /assets/js/..-preferences.js' );
      } );

      return $t;
    }

    return $t.init();

  })();

} );