<?php
/**
 * Preferences View controller
 *
 * @class              WPXShortcodesManagerPreferencesViewController
 * @author             wpXtreme, Inc.
 * @copyright          Copyright (C) 2013-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-01-27
 * @version            1.0.0
 *
 */

class WPXShortcodesManagerPreferencesViewController extends WPDKPreferencesViewController {

  /**
   * Return a singleton instance of WPXShortcodesManagerPreferencesViewController class
   *
   * @return WPXShortcodesManagerPreferencesViewController
   */
  public static function init()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new WPXShortcodesManagerPreferencesViewController();
    }
    return $instance;
  }

  /**
   * Create an instance of WPXShortcodesManagerPreferencesViewController class
   *
   * @return WPXShortcodesManagerPreferencesViewController
   */
  public function __construct()
  {
    // Single instances of tab content.
    $general = new WPXShortcodesManagerPreferencesGeneralBranchView();

    // Create each single tab.
    $tabs = array(
      new WPDKjQueryTab( $general->id, __( 'General', WPXSHORTCODESMANAGER_TEXTDOMAIN ), $general->html() ),
    );

    parent::__construct( WPXShortcodesManagerPreferences::init(), __( 'Preferences', WPXSHORTCODESMANAGER_TEXTDOMAIN ), $tabs );
  }

  /**
   * Fires when styles are printed for a specific admin page based on $hook_suffix.
   *
   * @since WP 2.6.0
   * @since 1.6.0
   */
  public function admin_print_styles()
  {
    wp_enqueue_style( 'wpxsm-preferences', WPXSHORTCODESMANAGER_URL_CSS . 'wpxsm-preferences.css', array(), WPXSHORTCODESMANAGER_VERSION );
  }

}


/**
 * General branch view.
 *
 * @class           WPXShortcodesManagerPreferencesGeneralBranchView
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-27
 * @version         1.0.0
 *
 */
class WPXShortcodesManagerPreferencesGeneralBranchView extends WPDKPreferencesView {

  /**
   * Create an instance of WPXShortcodesManagerPreferencesGeneralBranchView class
   *
   * @return WPXShortcodesManagerPreferencesGeneralBranchView
   */
  public function __construct()
  {
    $preferences = WPXShortcodesManagerPreferences::init();
    parent::__construct( $preferences, 'general' );
  }

  /**
   * Return the array fields
   *
   * @param WPXShortcodesManagerPreferencesGeneralBranch $general
   *
   * @return array|void
   */
  public function fields( $general )
  {
    $fields = array(
      __( 'Shortcodes Analysis', WPXSHORTCODESMANAGER_TEXTDOMAIN ) => array(

        __( 'Enable a Shortcodes Analysis when you edit a post and display an Alert Message whether found any <strong>unregistered</strong> shortcodes.', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
        array(
          array(
            'type'  => WPDKUIControlType::SWIPE,
            'name'  => WPXShortcodesManagerPreferencesGeneralBranch::ENABLE_ALERT_UNREGISTERED_SHORTCODES,
            'label' => __( 'Enable', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
            'value' => $general->enable_alert_unregistered_shortcodes ? 'on' : 'off'
          )
        ),
      ),
    );
    return $fields;
  }
}