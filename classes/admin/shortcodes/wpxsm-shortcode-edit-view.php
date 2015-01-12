<?php

/**
 * Shortcode New/Edit view
 *
 * @class           WPXShortcodesManagerShortcodeEditView
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-06
 * @version         1.0.0
 *
 */
class WPXShortcodesManagerShortcodeEditView extends WPDKView {

  // View ID
  const ID = 'wpxsm-shortcode-edit-view';

  /**
   * Shortcode
   *
   * @var array|bool $shortcode
   */
  public $model;

  /**
   * Return a singleton instance of WPXShortcodesManagerShortcodeEditView class
   *
   * @param string $id Optional. Custom Shortcode
   *
   * @return WPXShortcodesManagerShortcodeEditView
   */
  public static function init( $id = '' )
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new self( $id );
    }
    return $instance;
  }

  /**
   * Create an instance of WPXShortcodesManagerShortcodeEditView class
   *
   * @param string $id Optional. Custom Shortcode
   *
   * @return WPXShortcodesManagerShortcodeEditView
   */
  public function __construct( $id = '' )
  {
    parent::__construct( self::ID, 'wpdk-border-container' );

    $custom      = WPXShortcodesManagerShortcodes::get_instance()->custom;
    $this->model = isset( $custom[$id] ) ? $custom[$id] : false;
  }

  /**
   * Display
   */
  public function draw()
  {
    // View controller
    $uri = WPDKMenu::url( 'WPXShortcodesManagerShortcodesViewController' );

    // Add query args
    $uri = add_query_arg( array(
      WPXShortcodesManagerShortcodes::COLUMN_STATUS => isset( $_GET[ WPXShortcodesManagerShortcodes::COLUMN_STATUS ] ) ? $_GET[ WPXShortcodesManagerShortcodes::COLUMN_STATUS ] : false
    ), $uri );

    $back = sprintf( '<a class="button button-large alignleft" href="%s">%s %s</a>', $uri, WPDKGlyphIcons::html( WPDKGlyphIcons::ANGLE_LEFT ), __( 'Back', WPXSHORTCODESMANAGER_TEXTDOMAIN ) );

    $submit = WPDKUI::button( $this->model ? __( 'Update', WPXSHORTCODESMANAGER_TEXTDOMAIN ) : __( 'Create', WPXSHORTCODESMANAGER_TEXTDOMAIN ), array( 'additional_classes' => 'button-large' ) );

    $layout = new WPDKUIControlsLayout( $this->fields() );
    $form   = new WPDKHTMLTagForm( $layout->html() . $back . $submit );

    $form->name   = 'wpxsm-shortocde-edit';
    $form->id     = $form->name;
    $form->method = WPDKHTTPVerbs::POST;
    //$form->action = remove_query_arg( WPXShortcodesManagerShortcodes::COLUMN_ID );
    $form->display();
  }
  
  /**
   * Return the array
   *
   * @return array
   */
  private function fields()
  {
    // Legend for new or Edit
    $legend = $this->model ? sprintf( __( 'Edit %s', WPXSHORTCODESMANAGER_TEXTDOMAIN ), $this->model['name'] ) : __( 'Create a new Shortcode', WPXSHORTCODESMANAGER_TEXTDOMAIN );

    $fields = array(
      $legend => array(

        array(
          'type'  => WPDKUIControlType::HIDDEN,
          'name'  => 'action',
          'value' => $this->model ? WPXShortcodesManagerShortcodes::ACTION_UPDATE : WPXShortcodesManagerShortcodes::ACTION_INSERT,
        ),

        $this->model ? array(
          'type'  => WPDKUIControlType::HIDDEN,
          'name'  => WPXShortcodesManagerShortcodes::LIST_TABLE_SINGULAR,
          'value' => $this->model[ WPXShortcodesManagerShortcodes::COLUMN_NAME ],
        ) : '',

        __( 'This is the shortcode name that you will use in you post content.', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
        array(
          array(
            'type'        => WPDKUIControlType::TEXT,
            'name'        => WPXShortcodesManagerShortcodes::COLUMN_ID_NOCONFLICT,
            'value'       => $this->model ? $this->model[ WPXShortcodesManagerShortcodes::COLUMN_NAME ] : '',
            'label'       => __( 'Shortcode', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
            'placeholder' => __( 'Eg: notice', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
            'required'    => 'required'
          )
        ),

        __( 'Edit any HTML markup and use the <code>$content</code> placeholder if you wish wrap shortcode content.', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
        array(
          array(
            'type'        => WPDKUIControlType::TEXTAREA,
            'name'        => WPXShortcodesManagerShortcodes::COLUMN_HTML,
            'value'       => $this->model ? stripslashes( base64_decode( $this->model['html'] ) ) : '',
            'placeholder' => esc_attr( __( 'Eg: <span color="#f00">I am red $content</span>', WPXSHORTCODESMANAGER_TEXTDOMAIN ) ),
            'label'       => __( 'HTML Markup', WPXSHORTCODESMANAGER_TEXTDOMAIN ),
          )
        ),
      ),

    );
    return $fields;
  }

}