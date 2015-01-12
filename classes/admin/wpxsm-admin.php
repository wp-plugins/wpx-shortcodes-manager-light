<?php

/**
 * Admin model controller
 *
 * @class              WPXShortcodesManagerAdmin
 * @author             wpXtreme, Inc.
 * @copyright          Copyright (C) 2013-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-07-14
 * @version            1.0.0
 *
 */

class WPXShortcodesManagerAdmin extends WPDKWordPressAdmin {

  // This is the minumun capability required to display admin menu item
  const MENU_CAPABILITY = 'manage_options';

  /**
   * Create and return a singleton instance of WPXShortcodesManagerAdmin class
   *
   * @return WPXShortcodesManagerAdmin
   */
  public static function init()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new self();
    }
    return $instance;
  }

  /**
   * Create an instance of WPXShortcodesManagerAdmin class
   *
   * @return WPXShortcodesManagerAdmin
   */
  public function __construct()
  {
    /**
     * @var WPXShortcodesManager $plugin
     */
    $plugin = $GLOBALS['WPXShortcodesManager'];
    parent::__construct( $plugin );

    // Manage the lis table view controller options.
    add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );

  }

  /**
   * Return the value for allowed options
   *
   * @param $status
   * @param $option
   * @param $value
   *
   * @return mixed
   */
  public function set_screen_option( $status, $option, $value )
  {
    $options = array(
      WPXShortcodesManagerShortcodesViewController::OPTION,
    );
    if ( in_array( $option, $options ) ) {
      return $value;
    }
    return $status;
  }

  /**
   * Called when WordPress is ready to build the admin menu.
   * Sample hot to build a simple menu.
   */
  public function admin_menu()
  {

    // Hack for wpXtreme icon
    $icon_menu = $this->plugin->imagesURL . 'logo-16x16.png';

    $menus = array(
      'wpx-shortcodes-manager' => array(
        'menuTitle'  => __( 'Shortcodes Manager', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
        'capability' => self::MENU_CAPABILITY,
        'icon'       => $icon_menu,
        'subMenus'   => array(

          array(
            'menuTitle'      => __( 'Manage', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
            'capability'     => self::MENU_CAPABILITY,
            'viewController' => 'WPXShortcodesManagerShortcodesViewController',
          ),

          WPDKSubMenuDivider::DIVIDER,

          array(
            'menuTitle'      => __( 'Preferences', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
            'capability'     => self::MENU_CAPABILITY,
            'viewController' => 'WPXShortcodesManagerPreferencesViewController',
          ),
        )
      )
    );

    WPXMenu::init( $menus, $this->plugin );

  }

}
