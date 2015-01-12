<?php
if ( wpdk_is_ajax() ) {

  /**
   * Ajax gateway engine
   *
   * @class              WPXShortcodesManagerAjax
   * @author             =undo= <info@wpxtre.me>
   * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
   * @date               2014-09-16
   * @version            1.0.1
   *
   * @history            1.0.1 - Adde action
   *
   */
  final class WPXShortcodesManagerAjax extends WPDKAjax {

    /**
     * Create or return a singleton instance of WPXShortcodesManagerAjax
     *
     * @return WPXShortcodesManagerAjax
     */
    public static function getInstance()
    {
      $instance = null;
      if ( is_null( $instance ) ) {
        $instance = new WPXShortcodesManagerAjax();
      }

      return $instance;
    }

    /**
     * Alias of getInstance();
     *
     * @return WPXShortcodesManagerAjax
     */
    public static function init()
    {
      return self::getInstance();
    }

    /**
     * Return the array with the list of ajax allowed methods
     *
     * @breief Allow ajax action
     *
     * @return array
     */
    protected function actions()
    {
      $actionsMethods = array(
        'wpxsm_action_enable'          => false,
        'wpxsm_action_show_in_content' => false,
      );

      return $actionsMethods;
    }

    /**
     * Enable/disable
     *
     * @return string
     */
    public function wpxsm_action_enable()
    {
      $response = new WPDKAjaxResponse();

      // Get the shortcode id
      $shortcode_id = esc_attr( $_POST['shortcode_id'] );

      if ( empty( $shortcode_id ) ) {
        $response->error = __( 'No Shortcode ID!', WPXSHORTCODESMANAGER_TEXTDOMAIN );
        $response->json();
      }

      // Get status
      $enable = wpdk_is_bool( esc_attr( $_POST['enable'] ) );

      // Get model
      $model = WPXShortcodesManagerShortcodes::init();

      // Updated
      $model->enable( $shortcode_id, $enable );

      /**
       * Fires when a shortcode is disabled.
       */
      do_action( 'wpxsm_refresh_disabled' );

      // Gets the row
      $list_table = WPXShortcodesManagerShortcodesViewController::init();
      $item       = $model->select( array( WPXShortcodesManagerShortcodes::COLUMN_ID => $shortcode_id ) );

      // Prepare response
      $this->data = array();

      // HTML for single row
      WPDKHTML::startCompress();
      $list_table->single_row( $item );
      $response->data['row'] = WPDKHTML::endHTMLCompress();

      // Views
      WPDKHTML::startCompress();
      $list_table->views();
      $response->data['views'] = WPDKHTML::endHTMLCompress();

      $response->json();
    }

    /**
     * Show/hide in content
     *
     * @return string
     */
    public function wpxsm_action_show_in_content()
    {
      $response = new WPDKAjaxResponse();

      // Get the shortcode id
      $shortcode_id = esc_attr( $_POST['shortcode_id'] );

      if ( empty( $shortcode_id ) ) {
        $response->error = __( 'No Shortcode ID!', WPXSHORTCODESMANAGER_TEXTDOMAIN );
        $response->json();
      }

      // Get status
      $enable = ! wpdk_is_bool( esc_attr( $_POST['enable'] ) );

      // Get model
      $model = WPXShortcodesManagerShortcodes::init();

      // Updated
      $model->hide_in_content( $shortcode_id, $enable );

      /**
       * Fires when a shortcode is disabled.
       */
      do_action( 'wpxsm_refresh_disabled' );

      // Gets the row
      $list_table = WPXShortcodesManagerShortcodesViewController::init();
      $item       = $model->select( array( 'shortcode_id' => $shortcode_id ) );

      // Prepare response
      $this->data = array();

      WPDKHTML::startCompress();
      $list_table->single_row( $item );
      $response->data['row'] = WPDKHTML::endHTMLCompress();

      // Views
      WPDKHTML::startCompress();
      $list_table->views();
      $response->data['views'] = WPDKHTML::endHTMLCompress();

      $response->json();
    }
  }
}