<?php

/**
 * Model used in list table view controller
 *
 * @class           WPXShortcodesManagerShortcodes
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-09-16
 * @version         1.0.1
 *
 * @history         1.0.1 - Add actions dor update and disable
 *
 */
class WPXShortcodesManagerShortcodes extends WPDKListTableModel {

  // Columns
  const COLUMN_ID              = 'shortcode_id';
  const COLUMN_ID_NOCONFLICT   = 'shortcode_id_no_conflict';
  const COLUMN_ENABLED         = 'enabled';
  const COLUMN_SHOW_IN_CONTENT = 'show_in_content';
  const COLUMN_CALLBACK        = 'callback';
  const COLUMN_CUSTOM          = 'custom';
  const COLUMN_STATUS          = 'status';
  const COLUMN_NAME            = 'name';
  const COLUMN_HTML            = 'html';

  // List table id
  const LIST_TABLE_SINGULAR = 'listtable_shortcode_id';
  const LIST_TABLE_PLURAL   = 'shortcodes';

  // Internal
  const COLUMN_CALLABLE              = 'callable';
  const COLUMN_PREVIOUS_SHORTCODE_ID = 'previous_shortcode_id';

  // Actions
  const ACTION_NEW             = 'new';
  const ACTION_INSERT          = 'insert';
  const ACTION_UPDATE          = 'update';
  const ACTION_EDIT            = 'action_edit';
  const ACTION_DELETE          = 'action_delete';
  const ACTION_ENABLE          = 'action_enable';
  const ACTION_DISABLE         = 'action_disable';
  const ACTION_HIDE_IN_CONTENT = 'action_hide_in_content';
  const ACTION_SHOW_IN_CONTENT = 'action_show_in_content';

  // Statues
  const STATUS_CUSTOM          = 'custom';
  const STATUS_ENABLED         = 'enabled';
  const STATUS_DISABLED        = 'disabled';
  const STATUS_SHOW_IN_CONTENT = 'show_in_content';
  const STATUS_HIDE_IN_CONTENT = 'hide_in_content';

  // Option keys for disabled shortcodes list
  const OPTION_KEY_DISABLED_SHORTCODES_LIST        = 'wpxsm-shortcodes-disabled';
  const OPTION_KEY_HIDE_IN_CONTENT_SHORTCODES_LIST = 'wpxsm-shortcodes-removed';
  const OPTION_KEY_CUSTOM_SHORTCODES_LIST          = 'wpxsm-shortcodes-custom';

  /**
   * List of custom shortcodes
   *
   * @var array $custom
   */
  public $custom = array();

  /**
   * List of hide in content shortcodes
   *
   * @var array $hide_in_content
   */
  public $hide_in_content = array();

  /**
   * List of disabled shortcodes
   *
   * @var array $disabled
   */
  public $disabled = array();

  /**
   * Return a singleton instance of WPXShortcodesManagerShortcodes class
   *
   * @return WPXShortcodesManagerShortcodes
   */
  public static function get_instance()
  {
    return self::init();
  }

  /**
   * Return a singleton instance of WPXShortcodesManagerShortcodes class
   *
   * @return WPXShortcodesManagerShortcodes
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
   * Create an instance of WPXShortcodesManagerShortcodes class
   *
   * @return WPXShortcodesManagerShortcodes
   */
  public function __construct()
  {
    // Get the list of custom shortcodes
    $this->custom = get_site_option( self::OPTION_KEY_CUSTOM_SHORTCODES_LIST, array() );

    // Get the hide in content shortcodes
    $this->hide_in_content = get_site_option( self::OPTION_KEY_HIDE_IN_CONTENT_SHORTCODES_LIST, array() );

    // Get the disabled list
    $this->disabled = get_site_option( self::OPTION_KEY_DISABLED_SHORTCODES_LIST, array() );

    // Init the model and process action before wp is loaded
    parent::__construct();

  }

  /**
   * Return the columns array
   *
   * @return array
   */
  public function get_columns()
  {
    $columns = array(
      'cb'                         => '<input type="checkbox" />',
      self::COLUMN_ID              => __( 'Shortcode', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::COLUMN_CALLBACK        => __( 'Callable/Callback', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::COLUMN_CUSTOM          => __( 'Custom', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::COLUMN_ENABLED         => __( 'Enabled', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::COLUMN_SHOW_IN_CONTENT => __( 'In Content', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
    );

    return $columns;
  }

  /**
   * Return the sortable columns
   *
   * @return array
   */
  public function get_sortable_columns()
  {
    $sortable_columns = array(
      self::COLUMN_ID => array( self::COLUMN_ID, true ),
    );

    return $sortable_columns;
  }

  /**
   * Return the statuses
   *
   * @return array
   */
  public function get_statuses()
  {
    $statuses = array(
      WPDKDBTableRowStatuses::ALL  => __( 'All', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::STATUS_CUSTOM          => __( 'Custom', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::STATUS_ENABLED         => __( 'Enabled', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::STATUS_DISABLED        => __( 'Disabled', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::STATUS_SHOW_IN_CONTENT => __( 'Show in Content', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::STATUS_HIDE_IN_CONTENT => __( 'Hide in Content', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
    );

    return $statuses;
  }

  /**
   * Return the count of records for a status
   *
   * @param string $status
   *
   * @return int
   */
  public function count()
  {
    // Get all shortocodes
    $shortcodes = $this->shortcodes();

    $all             = count( $shortcodes );
    $disabled        = count( get_site_option( self::OPTION_KEY_DISABLED_SHORTCODES_LIST, array() ) );
    $hide_in_content = count( get_site_option( self::OPTION_KEY_HIDE_IN_CONTENT_SHORTCODES_LIST, array() ) );
    $custom          = count( get_site_option( self::OPTION_KEY_CUSTOM_SHORTCODES_LIST, array() ) );

    $results = array(
      WPDKDBTableRowStatuses::ALL  => $all,
      self::STATUS_CUSTOM          => $custom,
      self::STATUS_ENABLED         => $all - $disabled,
      self::STATUS_DISABLED        => $disabled,
      self::STATUS_SHOW_IN_CONTENT => $all - $hide_in_content,
      self::STATUS_HIDE_IN_CONTENT => $hide_in_content,
    );

    return $results;
  }


  /**
   * Return a key value pairs array with status key => count.
   *
   * @return array
   */
  public function get_count_statuses()
  {
    $counts = $this->count( self::COLUMN_STATUS );

    return $counts;
  }

  /**
   * Return the right inline action for the current status
   *
   * @param array $item   The item
   * @param array $status Describe one or more status of single item
   *
   * @return array
   */
  public function get_actions_with_status( $item, $status )
  {
    $actions = array(
      self::ACTION_EDIT            => __( 'Edit', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::ACTION_ENABLE          => __( 'Enable', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::ACTION_DISABLE         => __( 'Disable', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::ACTION_SHOW_IN_CONTENT => __( 'Display in content', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::ACTION_HIDE_IN_CONTENT => __( 'Hide in Content', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::ACTION_DELETE          => __( 'Delete', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
    );

    if ( $status[ self::COLUMN_ENABLED ] ) {
      unset( $actions[ self::ACTION_ENABLE ] );
    }
    else {
      unset( $actions[ self::ACTION_DISABLE ] );
    }

    if ( $status[ self::COLUMN_SHOW_IN_CONTENT ] ) {
      unset( $actions[ self::ACTION_SHOW_IN_CONTENT ] );
    }
    else {
      unset( $actions[ self::ACTION_HIDE_IN_CONTENT ] );
    }

    if ( ! $status[ self::COLUMN_CUSTOM ] ) {
      unset( $actions[ self::ACTION_EDIT ] );
      unset( $actions[ self::ACTION_DELETE ] );
    }

    return $actions;
  }

  /**
   * Return the right combo menu bulk actions for the current status
   *
   * @param string $status Usually this is the status in the URI, when user select 'All', 'Publish', etc...
   *
   * @return array
   */
  public function get_bulk_actions_with_status( $status )
  {
    $actions = array(
      self::ACTION_EDIT            => __( 'Edit', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::ACTION_ENABLE          => __( 'Enable', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::ACTION_DISABLE         => __( 'Disable', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::ACTION_SHOW_IN_CONTENT => __( 'Display in content', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::ACTION_HIDE_IN_CONTENT => __( 'Hide in Content', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      self::ACTION_DELETE          => __( 'Delete', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
    );

    // No custom
    if ( self::STATUS_CUSTOM != $status ) {
      unset( $actions[ self::ACTION_EDIT ] );
      unset( $actions[ self::ACTION_DELETE ] );
    }

    // Enabled
    if ( self::STATUS_ENABLED == $status ) {
      unset( $actions[ self::ACTION_ENABLE ] );
    }

    // Show in content
    if ( self::STATUS_SHOW_IN_CONTENT == $status ) {
      unset( $actions[ self::ACTION_SHOW_IN_CONTENT ] );
    }

    // Hide in content
    if ( self::STATUS_HIDE_IN_CONTENT == $status ) {
      unset( $actions[ self::ACTION_HIDE_IN_CONTENT ] );
    }

    return $actions;
  }

  /**
   * Process actions
   */
  public function process_bulk_action()
  {
    // Get the shortocode id if exists
    $id = isset( $_REQUEST[ self::LIST_TABLE_SINGULAR ] ) ? $_REQUEST[ self::LIST_TABLE_SINGULAR ] : '';

    switch ( $this->current_action( self::LIST_TABLE_PLURAL ) ) {

      // Insert
      case self::ACTION_INSERT:
        $this->action_result( $this->insert( $_POST ) );
        break;

      // Update
      case self::ACTION_UPDATE:
        $this->action_result( $this->update( $_POST ) );
        break;

      // Delete
      case self::ACTION_DELETE:
        $this->action_result( $this->delete( $id ) );
        break;

      // Disable
      case self::ACTION_DISABLE:
        $this->action_result( $this->enable( $id, false ) );
        break;

      // Enable
      case self::ACTION_ENABLE:
        $this->action_result( $this->enable( $id ) );
        break;

      // Hide in content
      case self::ACTION_HIDE_IN_CONTENT:
        $this->action_result( $this->hide_in_content( $id ) );
        break;

      // Show in content
      case self::ACTION_SHOW_IN_CONTENT:
        $this->action_result( $this->hide_in_content( $id, false ) );
        break;
    }

    parent::process_bulk_action();
  }

  // -------------------------------------------------------------------------------------------------------------------
  // CRUD
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return the shortcodes items enable/disable
   *
   * @param array $args Optional. Argument for select
   *
   * @return array
   */
  public function select( $args = array() )
  {
    global $shortcode_tags, $WPXSM_REMOVED_SHORTCODES;

    // Get the disabled list
    $disabled = $this->disabled;

    // Get the content removed list
    $hide_in_content = $this->hide_in_content;

    // Get the custom list
    $custom = array_keys( $this->custom );

    // Get the list of all shortcode
    $shortcodes = $this->shortcodes();

    // Prepare results
    $items = array();

    // Defaults args
    $defaults = array(
      self::COLUMN_STATUS => isset( $_REQUEST[ self::COLUMN_STATUS ] ) ? $_REQUEST[ self::COLUMN_STATUS ] : false,
      self::COLUMN_ID     => false
    );

    // Merging
    $args = wp_parse_args( $args, $defaults );

    // Loop
    foreach ( $shortcodes as $shortcode ) {

      // Get info
      $info = isset( $shortcode_tags[ $shortcode ] ) ? $shortcode_tags[ $shortcode ] : $WPXSM_REMOVED_SHORTCODES[ $shortcode ];

      // Filters
      if ( ! empty( $args[ self::COLUMN_STATUS ] ) && WPDKDBTableRowStatuses::ALL != $args[ self::COLUMN_STATUS ] ) {

        // Custom
        if ( self::STATUS_CUSTOM == $args[ self::COLUMN_STATUS ] && ! in_array( $shortcode, $custom ) ) {
          continue;
        }

        // Enabled
        if ( self::STATUS_ENABLED == $args[ self::COLUMN_STATUS ] && in_array( $shortcode, $disabled ) ) {
          continue;
        }

        // Disabled
        if ( self::STATUS_DISABLED == $args[ self::COLUMN_STATUS ] && ! in_array( $shortcode, $disabled ) ) {
          continue;
        }

        // Show in content
        if ( self::STATUS_SHOW_IN_CONTENT == $args[ self::COLUMN_STATUS ] && in_array( $shortcode, $hide_in_content )
        ) {
          continue;
        }

        // Hide in content
        if ( self::STATUS_HIDE_IN_CONTENT == $args[ self::COLUMN_STATUS ] && ! in_array( $shortcode, $hide_in_content )
        ) {
          continue;
        }
      }

      $item = array(
        self::COLUMN_ID              => $shortcode,
        self::LIST_TABLE_SINGULAR    => $shortcode,
        self::COLUMN_CALLBACK        => '',
        self::COLUMN_CALLABLE        => false,
        self::COLUMN_CUSTOM          => in_array( $shortcode, $custom ),
        self::COLUMN_ENABLED         => ! in_array( $shortcode, $disabled ),
        self::COLUMN_SHOW_IN_CONTENT => ! in_array( $shortcode, $hide_in_content ),
      );

      // Extract addition info and callback
      if ( is_string( $info ) ) {
        $item[ self::COLUMN_CALLBACK ] = $info;
        $item[ self::COLUMN_CALLABLE ] = is_callable( $info, true );
      }
      // Object class method
      elseif ( is_array( $info ) && is_object( $info[0] ) && is_string( $info[1] ) ) {
        $item[ self::COLUMN_CALLBACK ] = sprintf( '%s::%s()', get_class( $info[0] ), $info[1] );
        $item[ self::COLUMN_CALLABLE ] = is_callable( $info, true );
      }
      // Object string class method
      elseif ( is_array( $info ) && is_string( $info[0] ) && is_string( $info[1] ) ) {
        $item[ self::COLUMN_CALLBACK ] = sprintf( '%s::%s()', $info[0], $info[1] );
        $item[ self::COLUMN_CALLABLE ] = is_callable( $info, true );
      }

      // Single item
      if ( ! empty( $args[ self::COLUMN_ID ] ) && $shortcode == $args[ self::COLUMN_ID ] ) {
        return $item;
      }

      $items[] = $item;
    }

    return $items;
  }

  /**
   * Return all registered shortocdes
   *
   * @return array
   */
  public function shortcodes()
  {
    global $shortcode_tags;

    // Get the disabled list
    $disabled = $this->disabled;

    // Get the hide in content list
    $hide_in_content = $this->hide_in_content;

    // Get the custom list
    $custom = array_keys( $this->custom );

    // Get the list of all shortcode
    $shortcodes = array_unique( array_merge( array_keys( $shortcode_tags ), $disabled, $custom, $hide_in_content ) );

    sort( $shortcodes );

    return $shortcodes;
  }

  /**
   * Insert a custom shortcode
   *
   * @param array $post_data Optional. Post data
   */
  public function insert( $post_data = array() )
  {
    // Stability
    if ( empty( $post_data ) ) {
      return;
    }

    // Shortcode ID and Shortcode name are the same thing
    $shortcode_id = $post_data[ self::COLUMN_ID_NOCONFLICT ];

    $this->custom[ $shortcode_id ] = array(
      self::COLUMN_NAME => $shortcode_id,
      self::COLUMN_HTML => base64_encode( $post_data[ self::COLUMN_HTML ] )
    );

    // Update for insert
    update_site_option( self::OPTION_KEY_CUSTOM_SHORTCODES_LIST, $this->custom );

    /**
     * Fires when a custom shortcode must be refresh.
     */
    do_action( 'wpxsm_refresh_custom' );

    return true;
  }

  /**
   * Update a custom shortcode
   *
   * @param array $post_data Optional. Post data
   *
   * @return bool
   */
  public function update( $post_data = array() )
  {
    // Stability
    if ( empty( $post_data ) ) {
      return false;
    }

    // Get original shortcode id/name
    $original_shortcode_id = $post_data[ self::LIST_TABLE_SINGULAR ];

    // Check previous
    if ( isset( $this->custom[ $original_shortcode_id ] ) ) {

      // Delete previous instance
      unset( $this->custom[ $original_shortcode_id ] );

      $shortcode_id = $_REQUEST[ self::LIST_TABLE_SINGULAR ] = $post_data[ self::COLUMN_ID_NOCONFLICT ];

      $this->custom[ $shortcode_id ] = array(
        self::COLUMN_NAME => $shortcode_id,
        self::COLUMN_HTML => base64_encode( $post_data[ self::COLUMN_HTML ] )
      );

      // Update
      update_site_option( self::OPTION_KEY_CUSTOM_SHORTCODES_LIST, $this->custom );

      /**
       * Fires when a custom shortcode must be refresh.
       */
      do_action( 'wpxsm_refresh_custom' );

      return true;
    }

    return false;
  }

  /**
   * Delete one or more custom shortcode
   *
   * @param string|array $shortcode_id Shortcodes names
   */
  public function delete( $shortcode_id )
  {
    foreach ( $this->custom as $shortcode => $info ) {
      if ( in_array( $shortcode, (array) $shortcode_id ) ) {
        unset( $this->custom[ $shortcode ] );
      }
    }

    // Updated
    update_site_option( self::OPTION_KEY_CUSTOM_SHORTCODES_LIST, $this->custom );

    /**
     * Fires when a custom shortcode must be refresh.
     */
    do_action( 'wpxsm_refresh_custom' );

    return true;
  }

  /**
   * Disable one or more shortcodes
   *
   * @param string|array $shortcode_id Shortcodes list
   * @param bool         $enable       Optional. TRUE for enable, FALSE for disable. Default TRUE
   *
   * @return bool
   */
  public function enable( $shortcode_id, $enable = true )
  {
    // Remove shortcodes from the disabled list
    if ( $enable ) {
      $this->disabled = array_unique( array_diff( (array) $this->disabled, (array) $shortcode_id ) );
    }
    // Add the shortcodes in the disabled list
    else {
      $this->disabled = array_unique( array_merge( (array) $this->disabled, (array) $shortcode_id ) );
    }

    update_site_option( self::OPTION_KEY_DISABLED_SHORTCODES_LIST, $this->disabled );

    /**
     * Fires when a shortcode is disabled.
     */
    do_action( 'wpxsm_refresh_disabled' );

    return true;
  }

  /**
   * Hide in content one or more shortcodes
   *
   * @param string|array $shortcode_id Shortcodes list
   * @param bool         $hide         Optional. TRUE for hide, FALSE for show. Default TRUE
   */
  public function hide_in_content( $shortcode_id, $hide = true )
  {
    // Add the shortcodes in the hide list
    if ( $hide ) {
      $this->hide_in_content = array_unique( array_merge( (array) $this->hide_in_content, (array) $shortcode_id ) );
    }
    // Remove shortcodes from the hide list
    else {
      $this->hide_in_content = array_unique( array_diff( (array) $this->hide_in_content, (array) $shortcode_id ) );
    }
    update_site_option( self::OPTION_KEY_HIDE_IN_CONTENT_SHORTCODES_LIST, $this->hide_in_content );

    /**
     * Fires when a shortcode is disabled.
     */
    do_action( 'wpxsm_refresh_disabled' );

    return true;
  }

}