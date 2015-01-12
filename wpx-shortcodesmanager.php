<?php

/**
 * WPXShortcodesManager is the main class of this plugin.
 * This class extends WPDKWordPressPlugin in order to make easy several WordPress functions.
 *
 * @class              WPXShortcodesManager
 * @author             wpXtreme, Inc.
 * @copyright          Copyright (C) wpXtreme, Inc..
 * @date               2013-12-16
 * @version            1.0.0
 *
 */
final class WPXShortcodesManager extends WPXPlugin {

  /**
   * Create and return a singleton instance of WPXShortcodesManager class
   *
   * @param string $file The main file of this plugin. Usually __FILE__ (main.php). If missing, this function is used only
   *                     to get current instance, if it exists.
   *
   * @return WPXShortcodesManager
   */
  public static function boot( $file = null )
  {
    static $instance = null;
    if ( is_null( $instance ) && ( !empty( $file ) ) ) {
      $instance = new self( $file );
    }
    return $instance;
  }

  /**
   * Return the singleton instance of WPXShortcodesManager class
   *
   * @return WPXShortcodesManager|NULL
   */
  public static function getInstance()
  {
    return self::boot();
  }

  /**
   * Create an instance of WPXShortcodesManager class
   *
   * @param string $file The main file of this plugin. Usually __FILE__ (main.php)
   *
   * @return WPXShortcodesManager object instance
   */
  public function __construct( $file = null )
  {
    parent::__construct( $file );

    // Enhancer
    add_action( 'init', array( 'WPXShortcodesManagerEnhancerPost', 'init' ), 100 );
  }

  /**
   * Register all autoload classes
   */
  public function classesAutoload()
  {
    $includes = array(
      $this->classesPath . 'admin/enhancer/wpxsm-enhancer-post.php'                 => array(
        'WPXShortcodesManagerEnhancerPost',
        'WPXShortcodesManagerEnhancerPostAlert'
      ),
      $this->classesPath . 'admin/preferences/wpxsm-preferences-viewcontroller.php' => array(
        'WPXShortcodesManagerPreferencesViewController',
        'WPXShortcodesManagerPreferencesGeneralBranchView'
      ),
      $this->classesPath . 'admin/preferences/wpxsm-preferences.php'                => array(
        'WPXShortcodesManagerPreferences',
        'WPXShortcodesManagerPreferencesGeneralBranch'
      ),
      $this->classesPath .
      'admin/shortcodes/wpxsm-shortcode-edit-view.php'                              => 'WPXShortcodesManagerShortcodeEditView',
      $this->classesPath . 'admin/shortcodes/wpxsm-shortcodes-viewcontroller.php'   => array(
        'WPXShortcodesManagerShortcodesViewController',
        'WPXShortcodesManagerShortcodesHelp'
      ),
      $this->classesPath . 'admin/shortcodes/wpxsm-shortcodes.php'                  => array(
        'WPXShortcodesManagerShortcodes',
        'method',
        'method'
      ),
      $this->classesPath . 'admin/wpxsm-admin.php'                                  => 'WPXShortcodesManagerAdmin',
      $this->classesPath . 'core/wpxsm-ajax.php'                                    => 'WPXShortcodesManagerAjax'
    );

    return $includes;
  }

  /**
   * Catch for activation. This method is called one shot.
   */
  public function activation()
  {
    WPXShortcodesManagerPreferences::init()->delta();
  }

  /**
   * Catch for admin
   */
  public function admin()
  {
    WPXShortcodesManagerAdmin::init();
  }

  /**
   * Catch for ajax
   */
  public function ajax()
  {
    WPXShortcodesManagerAjax::init();
  }

  /**
   * Ready to init plugin preferences
   */
  public function preferences()
  {
    WPXShortcodesManagerPreferences::init();
  }

}