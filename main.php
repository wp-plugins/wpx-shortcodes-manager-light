<?php
/// @cond private
/**
 * Plugin Name:     WPX Shortcodes Manager Light
 * Plugin URI:      https://wpxtre.me
 * Description:     A WordPress Shortcodes manager
 * Version:         1.0.8
 * Author:          wpXtreme, Inc.
 * Author URI:      https://wpxtre.me
 * Text Domain:     wpx-shortcodes-manager
 * Domain Path:     localization
 *
 * WPX PHP Min: 5.2.4
 * WPX WP Min: 3.8
 * WPX MySQL Min: 5.0
 * WPX wpXtreme Min: 1.4.10
 *
 */
/// @endcond

// Avoid directly access
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

// wpXtreme kickstart logic
require_once( trailingslashit( dirname( __FILE__ ) ) . 'wp_kickstart.php' );

// Engage
wpxtreme_wp_kickstart( __FILE__, 'wpx-shortcodes-manager_000053', 'WPXShortcodesManager', 'wpx-shortcodesmanager.php' );