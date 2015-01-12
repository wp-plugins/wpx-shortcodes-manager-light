<?php

/**
 * Shortocodes manager list view
 *
 * @class           WPXShortcodesManagerShortcodesViewController
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-27
 * @version         1.0.0
 *
 */
class WPXShortcodesManagerShortcodesViewController extends WPDKListTableViewController {

  // Option name for column e item per page
  const OPTION = 'wpxsm_shortcodes_per_page';

  /**
   * Model
   *
   * @var WPXShortcodesManagerShortcodes $model
   */
  public $model;

  /**
   * Return a singleton instance of WPXShortcodesManagerShortcodesViewController class
   *
   * @return WPXShortcodesManagerShortcodesViewController
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
   * Create an instance of WPXShortcodesManagerShortcodesViewController class
   *
   * @return WPXShortcodesManagerShortcodesViewController
   */
  public function __construct()
  {
    // Get shortcodes model
    $this->model = WPXShortcodesManagerShortcodes::init();

    // Standard List Table args
    $args = array(
      'singular' => WPXShortcodesManagerShortcodes::LIST_TABLE_SINGULAR,
      'plural'   => 'shortcodes',
      'ajax'     => false,
      // Added to fix
      'screen'   => 'toplevel_page_wpx-shortcodes-manager'
    );

    parent::__construct( 'wpxsm-shortcodes', __( 'Shortcodes', WPXSHORTCODESMANAGER_TEXTDOMAIN ), $args );

    // Help tabs
    if ( ! wpdk_is_ajax() ) {
      WPXShortcodesManagerShortcodesHelp::init()->display();
    }
  }

  /**
   * This delegate method is called before load the view
   */
  public function load()
  {
    if ( in_array( $this->action(), array( WPDKDBListTableModel::ACTION_EDIT, WPDKDBListTableModel::ACTION_NEW ) ) ) {
      add_filter( 'screen_options_show_screen', '__return_false' );

      return;
    }

    // Screen options
    global $wpxsm_shortcodes_listtable_viewcontroller;

    $args = array(
      'label'   => __( 'Items per page', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
      'default' => 20,
      'option'  => self::OPTION
    );
    add_screen_option( 'per_page', $args );

    $wpxsm_shortcodes_listtable_viewcontroller = $this;
  }

  /**
   * Fires when styles are printed for a specific admin page based on $hook_suffix.
   *
   * @since WP 2.6.0
   * @since 1.6.0
   */
  public function admin_print_styles()
  {
    wp_enqueue_style( 'wpxsm-shortcodes', WPXSHORTCODESMANAGER_URL_CSS . 'wpxsm-shortcodes.css', array(), WPXSHORTCODESMANAGER_VERSION );
  }

  /**
   * This static method is called when the head of this view controller is loaded by WordPress.
   * It is used by WPDKMenu for example, as 'admin_head-' action.
   */
  public function admin_head()
  {
    wp_enqueue_script( 'wpxsm-shortcodes', WPXSHORTCODESMANAGER_URL_JAVASCRIPT . 'wpxsm-shortcodes.js', array(), WPXSHORTCODESMANAGER_VERSION );
  }

  /**
   * Added extra tools filter in table nav
   *
   * @param string $which Top or bottom table nav
   */
  public function extra_tablenav( $which )
  {
    if ( $which == 'top' ) {
      //      $table_nav_view = new WPXSmartShopProducersTableNavView();
      //      $table_nav_view->display();
    }
  }

  /**
   * Generates content for a single row of the table.
   *
   * @note  This method override the parent
   *
   * @param object $item The current item
   */
  public function single_row( $item )
  {
    // Prepare stack class
    $classes = array();

    // Check for custom
    $classes[] = $item[ WPXShortcodesManagerShortcodes::COLUMN_CUSTOM ] ? 'custom' : '';

    // Check for enabled
    $classes[] = ( false == $item[ WPXShortcodesManagerShortcodes::COLUMN_ENABLED ] ) ? 'disabled' : '';

    // Check for enabled
    $classes[] = ( false == $item[ WPXShortcodesManagerShortcodes::COLUMN_SHOW_IN_CONTENT ] ) ? 'show_in_content' : '';

    echo '<tr class="' . WPDKHTMLTag::classInline( $classes ) . '">';

    // Parent
    $this->single_row_columns( $item );

    echo '</tr>';
  }

  /**
   * Display a content of cel for a column.
   *
   * @param array  $item        The item
   * @param string $column_name Column name
   *
   * @return mixed|string
   */
  public function column_default( $item, $column_name )
  {
    switch ( $column_name ) {

      // Column ID (name of shortcode )
      case WPXShortcodesManagerShortcodes::COLUMN_ID:
        // Use a custom content
        $status = array(
          WPXShortcodesManagerShortcodes::COLUMN_SHOW_IN_CONTENT => $item[ WPXShortcodesManagerShortcodes::COLUMN_SHOW_IN_CONTENT ],
          WPXShortcodesManagerShortcodes::COLUMN_ENABLED         => $item[ WPXShortcodesManagerShortcodes::COLUMN_ENABLED ],
          WPXShortcodesManagerShortcodes::COLUMN_CUSTOM          => $item[ WPXShortcodesManagerShortcodes::COLUMN_CUSTOM ],
        );

        return $this->actions_column( $item, sprintf( '<code>[%s]</code>', $item[ $column_name ] ), $status );

      // Enable/disable column action
      case WPXShortcodesManagerShortcodes::COLUMN_ENABLED:
        $item    = array(
          'name'       => 'wpxsm-enabled',
          'id'         => 'swipe-enabled-' . $item[ WPXShortcodesManagerShortcodes::COLUMN_ID ],
          'userdata'   => $item[ WPXShortcodesManagerShortcodes::COLUMN_ID ],
          'afterlabel' => '',
          'title'      => '', // @too display a readable status
          'value'      => $item[ WPXShortcodesManagerShortcodes::COLUMN_ENABLED ] ? 'on' : 'off'
        );
        $control = new WPDKUIControlSwipe( $item );

        return $control->html();
        break;

      // Custom
      case WPXShortcodesManagerShortcodes::COLUMN_CUSTOM:
        $custom = $item[ WPXShortcodesManagerShortcodes::COLUMN_CUSTOM ];
        $glyph  = $custom ? WPDKGlyphIcons::html( WPDKGlyphIcons::PENCIL ) : WPDKGlyphIcons::html( WPDKGlyphIcons::EMO_UNHAPPY );
        if ( $custom ) {
          $args = array(
            '_action_result'        => null,
            'action'                => WPXShortcodesManagerShortcodes::ACTION_EDIT,
            $this->args['singular'] => $item[ WPXShortcodesManagerShortcodes::COLUMN_ID ]
          );
          $uri = ( isset( $_SERVER['HTTP_REFERER'] ) && wpdk_is_ajax() ) ? $_SERVER['HTTP_REFERER'] : false;

          return sprintf( '<a class="button" href="%s">%s</a>', add_query_arg( $args, $uri ), $glyph );
        }
        else {
          return $glyph;
        }
        break;

      // Content remove column action
      case WPXShortcodesManagerShortcodes::COLUMN_SHOW_IN_CONTENT:
        $item    = array(
          'name'       => 'wpxsm-show-in-content',
          'id'         => 'swipe-show-in-content-' . $item[ WPXShortcodesManagerShortcodes::COLUMN_ID ],
          'userdata'   => $item[ WPXShortcodesManagerShortcodes::COLUMN_ID ],
          'afterlabel' => '',
          'title'      => '', // @too display a readable status
          'value'      => $item[ WPXShortcodesManagerShortcodes::COLUMN_SHOW_IN_CONTENT ] ? 'on' : 'off'
        );
        $control = new WPDKUIControlSwipe( $item );

        return $control->html();
        break;

      // Callback
      case WPXShortcodesManagerShortcodes::COLUMN_CALLBACK:
        $callable = $item[ WPXShortcodesManagerShortcodes::COLUMN_CALLABLE ] ? WPDKGlyphIcons::html( WPDKGlyphIcons::OK ) : WPDKGlyphIcons::html( WPDKGlyphIcons::ATTENTION );

        return sprintf( '<div class="wpxsm-overflow-cel">%s <code>%s</code></div>', $callable, $item[ $column_name ] );
        break;
    }

    return print_r( $item, true );
  }

  /**
   * Process the bulk actions and standard actions
   *
   * @return bool TRUE to stop display the list view, FALSE to display the list.
   */
  public function process_bulk_action()
  {
    // Get the shortocode id if exists
    $id = isset( $_REQUEST[ WPXShortcodesManagerShortcodes::LIST_TABLE_SINGULAR ] ) ? $_REQUEST[ WPXShortcodesManagerShortcodes::LIST_TABLE_SINGULAR ] : '';

    switch ( ( $action = $this->action() ) ) {

      // New custom shortcode
      case WPXShortcodesManagerShortcodes::ACTION_NEW:
      case WPXShortcodesManagerShortcodes::ACTION_EDIT:
        WPXShortcodesManagerShortcodeEditView::init( $id )->display();

        return true;
        break;
    }

    return parent::process_bulk_action();
  }

}


/**
 * Shortcodes Manager Shortcodes Manage Help view
 *
 * @class           WPXShortcodesManagerShortcodesHelp
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-03
 * @version         1.0.0
 *
 */
class WPXShortcodesManagerShortcodesHelp extends WPDKScreenHelp {

  /**
   * Return an instance of WPXShortcodesManagerShortcodesHelp class
   *
   * @return WPXShortcodesManagerShortcodesHelp
   */
  public static function init()
  {
    return new self;
  }

  /**
   * Return a key value pairs array with the list of tabs
   *
   * @param array $tabs List of tabs
   *
   * @return array
   */
  public function tabs( $tabs = array() )
  {
    $tabs = array(
      __( 'Overview', WPXSHORTCODESMANAGER_TEXTDOMAIN ) => array( $this, 'overview' )
    );

    return $tabs;
  }


  /**
   * Overview
   */
  public function overview()
  {
    WPDKHTML::startCompress(); ?>
    <style type="text/css">
      #wpxsm-columns-help
      {
        width            : 100%;
        border-collapse  : collapse;
        background-color : #fff;
      }

      #wpxsm-columns-help th
      {
        padding          : 4px;
        background-color : #e5e5e5;
        border-bottom    : 1px solid #d2d2d2;
        border-right     : 1px solid #d2d2d2;
        width            : 30px;
        white-space      : nowrap;
      }

      #wpxsm-columns-help th:last-child
      {
        width        : auto;
        border-right : none;
      }

      #wpxsm-columns-help td
      {
        font-size     : 18px;
        padding       : 0 4px;
        width         : 30px;
        border-bottom : 1px solid #E5E5E5;
        border-right  : 1px solid #e5e5e5;
        white-space   : nowrap;
        text-align    : center;
      }

      #wpxsm-columns-help td:last-child
      {
        font-size    : inherit;
        width        : auto;
        text-align   : left;
        border-right : none;
      }

      #wpxsm-columns-help td i
      {
        color : #54a75f;
      }

      #wpxsm-columns-help td i.wpdk-icon-cancel-circled
      {
        color : #f60;
      }

    </style>
    <?php echo WPDKHTML::endCSSCompress();

    WPDKHTML::startCompress(); ?>
    <h4><?php _e( 'Manage Shortcodes', WPXSHORTCODESMANAGER_TEXTDOMAIN ) ?></h4>
    <p><?php _e( 'From this view you can manage all registered shortcodes. You can disable the shortcode execution or remove it from the content! For instance, if you have in your post content something like <code>Hello, [B]World[/B]</code> you can:', WPXSHORTCODESMANAGER_TEXTDOMAIN ) ?></p>
    <table id="wpxsm-columns-help">
      <thead>
        <tr>
          <th><?php _e( 'Enabled', WPXSHORTCODESMANAGER_TEXTDOMAIN ) ?></th>
          <th><?php _e( 'In Content', WPXSHORTCODESMANAGER_TEXTDOMAIN ) ?></th>
          <th><?php _e( 'Output', WPXSHORTCODESMANAGER_TEXTDOMAIN ) ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php WPDKGlyphIcons::display( WPDKGlyphIcons::OK_CIRCLED ) ?></td>
          <td><?php WPDKGlyphIcons::display( WPDKGlyphIcons::OK_CIRCLED ) ?></td>
          <td><p>Hello, <strong>World</strong></p></td>
        </tr>
        <tr>
          <td><?php WPDKGlyphIcons::display( WPDKGlyphIcons::CANCEL_CIRCLED ) ?></td>
          <td><?php WPDKGlyphIcons::display( WPDKGlyphIcons::OK_CIRCLED ) ?></td>
          <td><p>Hello, <?php echo WPXShortcodesManagerEnhancerPost::get_instance()->display_warning( array(), 'World', 'B' ) ?></p></td>
        </tr>
        <tr>
          <td><?php WPDKGlyphIcons::display( WPDKGlyphIcons::CANCEL_CIRCLED ) ?></td>
          <td><?php WPDKGlyphIcons::display( WPDKGlyphIcons::CANCEL_CIRCLED ) ?></td>
          <td><p>Hello, World</p></td>
        </tr>
        <tr>
          <td><?php WPDKGlyphIcons::display( WPDKGlyphIcons::OK_CIRCLED ) ?></td>
          <td><?php WPDKGlyphIcons::display( WPDKGlyphIcons::CANCEL_CIRCLED ) ?></td>
          <td><p>Hello, </p></td>
        </tr>
      </tbody>
    </table>
    <?php
    echo WPDKHTML::endHTMLCompress();
  }

}