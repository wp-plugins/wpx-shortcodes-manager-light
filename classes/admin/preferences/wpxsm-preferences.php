<?php
/**
 * Sample configuration class. In this class you define your tree configuration.
 *
 * @class              WPXShortcodesManagerConfiguration
 * @author             wpXtreme, Inc.
 * @copyright          Copyright (C) 2013-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-01-27
 * @version            1.0.0
 */

class WPXShortcodesManagerPreferences extends WPDKPreferences {

  /**
   * The Preferences name used on database
   *
   * @var string
   */
  const PREFERENCES_NAME = 'wpxsm-preferences';

  /**
   * Your preferences property
   *
   * @var string $version
   */
  public $version = WPXSHORTCODESMANAGER_VERSION;

  /**
   * General
   *
   * @var WPXShortcodesManagerPreferencesGeneralBranch $general
   */
  public $general;

  /**
   * Return an instance of WPXShortcodesManagerPreferences class from the database or onfly.
   *
   * @return WPXShortcodesManagerPreferences
   */
  public static function init()
  {
    return parent::init( self::PREFERENCES_NAME, __CLASS__, WPXSHORTCODESMANAGER_VERSION );
  }

  /**
   * Set the default preferences
   */
  public function defaults()
  {
    $this->general = new WPXShortcodesManagerPreferencesGeneralBranch();
  }

}

/**
 * General Shortcodes preferences branch model
 *
 * @class           WPXShortcodesManagerPreferencesGeneralBranch
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-27
 * @version         1.0.0
 *
 */
class WPXShortcodesManagerPreferencesGeneralBranch extends WPDKPreferencesBranch {

  const ENABLE_ALERT_UNREGISTERED_SHORTCODES = 'wpxsm_enable_alert_unregistered_shortcodes';

  /**
   * Enable an alert for unregistered shortcodes.
   *
   * @var string $enable_alert_unregistered_shortcodes
   */
  public $enable_alert_unregistered_shortcodes;

  /**
   * Reset to defaults values
   */
  public function defaults()
  {
    $this->enable_alert_unregistered_shortcodes = true;
  }

  /**
   * Update this branch
   */
  public function update()
  {
    $this->enable_alert_unregistered_shortcodes = wpdk_is_bool( esc_attr( $_POST[self::ENABLE_ALERT_UNREGISTERED_SHORTCODES] ) );
  }
}