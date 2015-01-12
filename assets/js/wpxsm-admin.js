/**
 * Admin Backend Area
 *
 * @class           WPXShortcodesManager
 * @author          wpXtreme, Inc.
 * @copyright       Copyright (C) 2013-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-27
 * @version         1.0.0
 */

jQuery( function ( $ )
{
  "use strict";

  window.WPXShortcodesManager = (function ()
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
    function _init()
    {
      /* Your init here. */

      return $t;
    }

    return $t.init();

  })();

} );
