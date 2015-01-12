<?php

/**
 * Enhancer the post edit with shortcodes analysis
 *
 * @class           WPXShortcodesManagerEnhancerPost
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-09-16
 * @version         1.0.1
 *
 * @history         1.0.1 - Improved and fixed custom and disable process.
 *
 */
class WPXShortcodesManagerEnhancerPost {

  /**
   * Return a singleton instance of WPXShortcodesManagerEnhancerPost class
   *
   * @return WPXShortcodesManagerEnhancerPost
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
   * Return a singleton instance of WPXShortcodesManagerEnhancerPost class
   *
   * @return WPXShortcodesManagerEnhancerPost
   */
  public static function get_instance()
  {
    return self::init();
  }

  /**
   * Create an instance of WPXShortcodesManagerEnhancerPost class
   *
   * @return WPXShortcodesManagerEnhancerPost
   */
  public function __construct()
  {
    if ( ! wpdk_is_ajax() ) {

      // Check enhancer via preferences
      if ( WPXShortcodesManagerPreferences::init()->general->enable_alert_unregistered_shortcodes ) {

        // Enhancer post edit with alert on unused shortcode
        add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ), 100 );
      }
    }

    // Removed shortocode from the post content
    add_filter( 'the_content', array( $this, 'the_content' ) );

    // Fires when a custom shortcode must be refresh.
    add_action( 'wpxsm_refresh_custom', array( $this, 'wpxsm_refresh_custom' ) );
    add_action( 'wp_loaded', array( $this, 'wpxsm_refresh_custom' ), 100 );

    // Fires when a shortcode is disabled.
    add_action( 'wpxsm_refresh_disabled', array( $this, 'wpxsm_refresh_disabled' ) );
    add_action( 'wp_loaded', array( $this, 'wpxsm_refresh_disabled' ), 100 );

  }

  /**
   * Fires when a custom shortcode must be refresh.
   */
  public function wpxsm_refresh_custom()
  {
    // Get shortcodes model
    $shortcodes = WPXShortcodesManagerShortcodes::init();

    // Add my custom own shortcode
    foreach ( array_keys( $shortcodes->custom ) as $shortcode ) {
      add_shortcode( $shortcode, array( $this, 'custom_shortcode' ) );
    }
  }

  /**
   * Fires when a shortcode is disabled.
   */
  public function wpxsm_refresh_disabled()
  {
    global $shortcode_tags, $WPXSM_REMOVED_SHORTCODES;

    // This is a custom global array with the list of removed shortcode
    $WPXSM_REMOVED_SHORTCODES = array();

    // Get shortcodes model
    $shortcodes = WPXShortcodesManagerShortcodes::init();

    foreach ( $shortcodes->disabled as $shortcode ) {

      // Save the callable info of shortcode in global before remove it
      $WPXSM_REMOVED_SHORTCODES[ $shortcode ] = $shortcode_tags[ $shortcode ];

      // Remove shortcode form the WordPress global list
      remove_shortcode( $shortcode );

      // If the shortcode was not removed fron the content attach a warning display
      if ( ! in_array( $shortcode, $shortcodes->hide_in_content ) ) {

        // Display a warning for this shortcode
        add_shortcode( $shortcode, array( $this, 'display_warning' ) );
      }

      // Display only the content
      else {
        add_shortcode( $shortcode, array( $this, 'display_content' ) );
      }
    }
  }

  /**
   * Filter the post content
   *
   * @param string $content The post content
   *
   * @return mixed
   */
  public function the_content( $content )
  {
    // Get shortcodes model
    $shortcodes = WPXShortcodesManagerShortcodes::init();

    if ( empty( $shortcodes->hide_in_content ) ) {
      return $content;
    }

    // Merging disabled and hide in content
    $filtrable = array_unique( array_diff( $shortcodes->hide_in_content, $shortcodes->disabled ) );

    $tagregexp = join( '|', array_map( 'preg_quote', $filtrable ) );

    $regex = '\\['                              // Opening bracket
             . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
             . "($tagregexp)"                     // 2: Shortcode name
             . '(?![\\w-])'                       // Not followed by word character or hyphen
             . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
             . '[^\\]\\/]*'                   // Not a closing bracket or forward slash
             . '(?:' . '\\/(?!\\])'               // A forward slash not followed by a closing bracket
             . '[^\\]\\/]*'               // Not a closing bracket or forward slash
             . ')*?' . ')' . '(?:' . '(\\/)'                        // 4: Self closing tag ...
             . '\\]'                          // ... and closing bracket
             . '|' . '\\]'                          // Closing bracket
             . '(?:' . '('
             // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
             . '[^\\[]*+'             // Not an opening bracket
             . '(?:' . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
             . '[^\\[]*+'         // Not an opening bracket
             . ')*+' . ')' . '\\[\\/\\2\\]'             // Closing shortcode tag
             . ')?' . ')' .
             '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]    $content = preg_replace_callback( '/' . $regex . '/s', 'strip_shortcode_tag', $content );

    $content = preg_replace_callback( '/' . $regex . '/s', 'strip_shortcode_tag', $content );

    return $content;
  }

  /**
   * Return a custom shortcode mark
   *
   * @param array  $atts      Attributes
   * @param string $content   Content
   * @param string $shortcode Shortcode (tag) name/id
   *
   * @return string
   */
  public function custom_shortcode( $atts = array(), $content = '', $shortcode )
  {
    // Get shortcodes model
    $shortcodes = WPXShortcodesManagerShortcodes::init();

    $result = '';
    if ( isset( $shortcodes->custom[ $shortcode ] ) ) {
      $result = apply_filters( 'the_content', str_replace( '$content', $content, stripslashes( base64_decode( $shortcodes->custom[ $shortcode ]['html'] ) ) ) );
    }

    return $result;
  }

  /**
   * This internal shortcode hook is used to display a warning when you disable a shortcode but do not remove it from
   * the content.
   *
   * @param array       $atts    Attributes
   * @param string|null $content Content. Could be null
   * @param string      $tag     Shortcode ID
   *
   * @return string
   */
  public function display_warning( $atts, $content = null, $tag )
  {
    /**
     * Filter the warning message appear in backend when you edit a post.
     *
     * @param string $message The warning message.
     */
    $message = apply_filters( 'wpxsm_warning_message', sprintf( __( 'The shortcode <code>[%s]</code> was disabled by Shortocode Manager!', WPXSHORTCODESMANAGER_TEXTDOMAIN ), $tag ), $tag );

    // If no content exists
    if ( empty( $content ) ) {

      // TODO style from preferences
      return sprintf( '<span style="background-color:#f60;-moz-border-radius:2px;-webkit-border-radius:2px;border-radius:2px;color:#fff;font-weight:bold;padding:2px 6px" title="%s">[%s]</span>', $message, $tag );
    }

    return sprintf( '<span style="background-color:#f60;-moz-border-radius:2px;-webkit-border-radius:2px;border-radius:2px;color:#fff;font-weight:bold;padding:2px 6px" title="%s">[%s]%s[/%s]</span>', $message, $tag, $content, $tag );
  }

  /**
   * Return the content.
   *
   * @param array       $atts    Attributes
   * @param string|null $content Content. Could be null
   * @param string      $tag     Shortcode ID
   *
   * @return string|null
   */
  public function display_content( $atts, $content = null, $tag )
  {
    if ( ! empty( $content ) ) {
      return $content;
    }
  }

  /**
   * Check the post content for unused shortcoes
   */
  public function edit_form_after_title()
  {
    // Get the current screen
    $screen = get_current_screen();

    if ( 'post' !== $screen->base ) {
      return;
    }

    global $post, $shortcode_tags;

    // If no content
    if ( empty( $post->post_content ) ) {
      return;
    }

    // Registered shortcodes
    $registered_shortcodes = array_keys( $shortcode_tags );

    // If no shortcodes
    if ( empty( $registered_shortcodes ) ) {
      return;
    }

    // Get all shortcodes
    preg_match_all( "/\[(.*?)\]/", $post->post_content, $matches );

    /*
     * array(2) {
     *   [0]=> array(3) {
     *     [0]=> string(46) "[wpdk_gist id=5701714 file=wpx-bannerize.html]"
     *     [1]=> string(8) "[cc_css]"
     *     [2]=> string(9) "[/cc_css]"
     *   }
     *   [1]=> array(3) {
     *     [0]=> string(44) "wpdk_gist id=5701714 file=wpx-bannerize.html"
     *     [1]=> string(6) "cc_css"
     *     [2]=> string(7) "/cc_css"
     *   }
     * }
     *
     */

    // No shortcode found
    if ( ! isset( $matches[1] ) || empty( $matches[1] ) ) {
      return;
    }

    // Sanitize
    $shortcodes = array();
    foreach ( $matches[1] as $shortcode ) {
      // Remove [/]
      if ( '/' == substr( $shortcode, 0, 1 ) ) {
        continue;
      }
      $parts        = explode( ' ', $shortcode );
      $shortcodes[] = $parts[0];
    }

    // Prepare unregisted, disabled, suspicious ... shortcode list
    $bads = array();

    // Check unregistered shortcodes
    foreach ( $shortcodes as $shortcode ) {
      if ( ! in_array( $shortcode, $registered_shortcodes ) ) {
        $bads[ $shortcode ] = __( 'Unregistered', WPXSHORTCODESMANAGER_TEXTDOMAIN );
      }
    }

    // Check disabled shortcodes
    foreach ( $shortcodes as $shortcode ) {
      if ( in_array( $shortcode, WPXShortcodesManagerShortcodes::init()->disabled ) ) {
        $bads[ $shortcode ] = __( 'Disabled', WPXSHORTCODESMANAGER_TEXTDOMAIN );
      }
    }

    // Check hide in content shortcodes
    foreach ( $shortcodes as $shortcode ) {
      if ( in_array( $shortcode, WPXShortcodesManagerShortcodes::init()->hide_in_content ) ) {
        $bads[ $shortcode ] = __( 'Hide in content', WPXSHORTCODESMANAGER_TEXTDOMAIN );
      }
    }

    // Useful Action
    do_action( 'wpxsm-shortcodes-analysis', $bads );

    WPXShortcodesManagerEnhancerPostAlert::init( $bads )->display();

  }

}

/**
 * Shortcodes analysis feedback
 *
 * @class           WPXShortcodesManagerEnhancerPostAlert
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-27
 * @version         1.0.0
 *
 */
class WPXShortcodesManagerEnhancerPostAlert extends WPDKUIAlert {

  /**
   * Unregistred Shortcodes
   *
   * @var array $bads
   */
  protected $bads = array();

  /**
   * Return a singleton instance of WPXShortcodesManagerEnhancerPostAlert class
   *
   * @param array $bads Optional. Unregistered shortcodes list
   *
   * @return WPXShortcodesManagerEnhancerPostAlert
   */
  public static function init( $bads = array() )
  {
    return new self( $bads );
  }

  /**
   * Create an instance of WPXShortcodesManagerEnhancerPostAlert class
   *
   * @param array $bads Optional. Unregistered shortcodes list
   *
   * @return WPXShortcodesManagerEnhancerPostAlert
   */
  public function __construct( $bads = array() )
  {
    $this->bads = $bads;

    if ( empty( $bads ) ) {
      $title   = __( 'Shortcodes Manager Analysis', WPXSHORTCODESMANAGER_TEXTDOMAIN );
      $content = __( 'No suspicius Shortcodes found!', WPXSHORTCODESMANAGER_TEXTDOMAIN );
      $type    = WPDKUIAlertType::SUCCESS;
    }
    else {
      $content = '';
      $title   = __( 'Shortcodes Manager Analysis Warning!', WPXSHORTCODESMANAGER_TEXTDOMAIN );
      $type    = WPDKUIAlertType::WARNING;
    }

    parent::__construct( 'wpxsm-feedback', $content, $type, $title );
  }

  /**
   * Override the content method when there are unregistered shortcodes
   *
   * @return string
   */
  public function content()
  {
    WPDKHTML::startCompress();

    _e( 'Some shortcodes in your content seems <strong>suspicious</strong> Please check preview or content.', WPXSHORTCODESMANAGER_TEXTDOMAIN );
    ?>
    <ul>
      <?php foreach ( $this->bads as $shortcode => $status ) : ?>
        <li><code>[<?php echo $shortcode ?>]</code> - <strong><?php echo $status ?></strong></li>
      <?php endforeach ?>
    </ul>
    <?php
    return WPDKHTML::endHTMLCompress();
  }

}