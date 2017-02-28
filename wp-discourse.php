<?php
/**
 * Plugin Name: WP-Discourse
 * Description: Use Discourse as a community engine for your WordPress blog
 * Version: 1.2.2
 * Author: Discourse
 * Text Domain: wp-discourse
 * Domain Path: /languages
 * Author URI: https://github.com/discourse/wp-discourse
 * Plugin URI: https://github.com/discourse/wp-discourse
 * GitHub Plugin URI: https://github.com/discourse/wp-discourse
 *
 * @package WPDiscourse
 */

/**  Copyright 2014 Civilized Discourse Construction Kit, Inc (team@discourse.org)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

define( 'WPDISCOURSE_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPDISCOURSE_URL', plugins_url( '', __FILE__ ) );
define( 'MIN_WP_VERSION', '4.4' );
define( 'MIN_PHP_VERSION', '5.4.0' );
define( 'WPDISCOURSE_VERSION', '1.2.2' );

register_activation_hook( __FILE__, 'wp_discourse_check_versions_and_activate' );

require_once( __DIR__ . '/lib/utilities.php' );
require_once( __DIR__ . '/lib/sso.php' );
require_once( __DIR__ . '/templates/html-templates.php' );
require_once( __DIR__ . '/templates/template-functions.php' );
require_once( __DIR__ . '/lib/discourse.php' );
require_once( __DIR__ . '/lib/wordpress-email-verification.php' );
require_once( __DIR__ . '/lib/discourse-sso.php' );
require_once( __DIR__ . '/lib/discourse-publish.php' );
require_once( __DIR__ . '/lib/discourse-comment.php' );
require_once( __DIR__ . '/lib/meta-box.php' );

require_once( __DIR__ . '/lib/nonce.php' );
require_once( __DIR__ . '/lib/shortcodes/sso-client.php' );
require_once( __DIR__ . '/lib/sso/client.php' );
require_once( __DIR__ . '/lib/sso/query-redirect.php' );
require_once( __DIR__ . '/lib/sso-login-form.php' );
require_once( __DIR__ . '/lib/sso/sso-url.php' );
require_once( __DIR__ . '/lib/sso/button-markup.php' );

require_once( __DIR__ . '/admin/admin.php' );

$discourse                    = new WPDiscourse\Discourse\Discourse();
$discourse_publisher          = new WPDiscourse\DiscoursePublish\DiscoursePublish();
$discourse_comment            = new WPDiscourse\DiscourseComment\DiscourseComment();
$wordpress_email_verifier     = new WPDiscourse\WordPressEmailVerification\WordPressEmailVerification( 'discourse_email_verification_key', 'discourse' );
$discourse_sso                = new WPDiscourse\DiscourseSSO\DiscourseSSO( $wordpress_email_verifier );
$discourse_publish_metabox    = new WPDiscourse\MetaBox\MetaBox();

$discourse_external_sso       = new WPDiscourse\sso\Client();
$discourse_query_redirect = new WPDiscourse\sso\QueryRedirect();


function wp_discourse_check_versions_and_activate() {
	global $wp_version;
	$flags = array();

	// Halt activation if requirements aren't met.
	if ( version_compare( PHP_VERSION, MIN_PHP_VERSION, '<' ) ) {
		$flags['php_version'] = 'The WP Discourse plugin requires at least PHP version ' . MIN_PHP_VERSION . '.';
	}

	if ( version_compare( $wp_version, MIN_WP_VERSION, '<' ) ) {
		$flags['wordpress_version'] = 'The WP Discourse plugin requires at least WordPress version ' . MIN_WP_VERSION . '.';
	}

	if ( ! empty( $flags ) ) {
		$message = '';
		foreach ( $flags as $flag ) {
			$message .= '<p><strong>' . $flag . '</strong></p>';
		}

		deactivate_plugins( plugin_basename( __FILE__ ), false, true );

		wp_die( esc_html( $message ), 'Plugin Activation Error', array( 'response' => 200, 'back_link' => true ) );
	}

	update_option( 'discourse_version', WPDISCOURSE_VERSION );
}
