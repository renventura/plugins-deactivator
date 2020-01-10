<?php
/**
 * Plugin Name: Plugins Deactivator
 * Plugin URI: #
 * Description: Disables all plugins with one click. Remembers which plugins were active so they can also be re-activated with one click.
 * Version: 1.0.0
 * Author: Ren Ventura
 * Author URI: https://renventura.com
 * Text Domain: plugins-deactivator
 * Domain Path: /languages/
 *
 * License: GPL 2.0+
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */

/*
	Copyright 2020  Ren Ventura  (email : rv@renventura.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	Permission is hereby granted, free of charge, to any person obtaining a copy of this
	software and associated documentation files (the "Software"), to deal in the Software
	without restriction, including without limitation the rights to use, copy, modify, merge,
	publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons
	to whom the Software is furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all copies or
	substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Plugins_Deactivator' ) ) :

class Plugins_Deactivator {

	private $option_key;

	public function __construct() {

		$this->option_key = 'plugins_deactivator_active_plugins_backup';

		add_action( 'pre_current_active_plugins', array( $this, 'add_button' ) );
		add_action( 'admin_init', array( $this, 'deactivate_plugins' ) );
		add_action( 'admin_init', array( $this, 'reactivate_plugins' ) );
	}

	/**
	 * Adds the deactivate and reactivate buttons
	 *
	 * @return void
	 */
	public function add_button() {
		?>
		<div id="plugin-deactivator">
			<?php if ( ! empty( $_REQUEST['plugins_deactivated'] ) ) : ?>
				<div class="notice notice-success">
					<p>
						<?php echo (int) sanitize_text_field( $_REQUEST['plugins_deactivated'] ) . __( ' plugins deactivated successfully.', 'plugins-deactivator' ) ?>
					</p>
				</div>
			<?php elseif ( ! empty( $_REQUEST['plugins_reactivated'] ) ) : ?>
				<div class="notice notice-success">
					<p>
						<?php echo (int) sanitize_text_field( $_REQUEST['plugins_reactivated'] ) . __( ' plugins reactivated successfully.', 'plugins-deactivator' ) ?>
					</p>
				</div>
			<?php endif; ?>
			<p>
				<?php if ( ! empty( get_option( $this->option_key ) ) ) : ?>
					<a href="<?php echo wp_nonce_url( add_query_arg( 'plugins-deactivator', 'reactivate', self_admin_url( 'plugins.php' ) ), 'plugins-deactivator' ); ?>" class="button button-primary"><?php _e( 'Reactivate All Plugins', 'plugins-deactivator' ); ?></a>
				<?php else : ?>
					<a href="<?php echo wp_nonce_url( add_query_arg( 'plugins-deactivator', 'deactivate', self_admin_url( 'plugins.php' ) ), 'plugins-deactivator' ); ?>" class="button button-secondary"><?php _e( 'Deactivate All Plugins', 'plugins-deactivator' ); ?></a>
				<?php endif; ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Process request to deactivate plugins
	 *
	 * @return void
	 */
	public function deactivate_plugins() {

		if ( ! isset( $_GET['plugins-deactivator'] ) || 'deactivate' !== $_GET['plugins-deactivator'] ) {
			return;
		}

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'plugins-deactivator' ) || ! current_user_can( 'activate_plugins' ) ) {
			die();
		}

		// Get the currently active plugins
		$active_plugins = get_option( 'active_plugins' );

		// Skip this plugin
		$this_plugin_index = array_search( plugin_basename( __FILE__ ), $active_plugins );
		unset( $active_plugins[$this_plugin_index] );

		// Store the currently active plugins
		update_option( $this->option_key, $active_plugins );

		// Deactivate all the plugins (except this one)
		deactivate_plugins( $active_plugins, true );

		$_REQUEST['plugins_deactivated'] = count( $active_plugins );
	}

	/**
	 * Process request to reactivate plugins
	 *
	 * @return void
	 */
	public function reactivate_plugins() {

		if ( ! isset( $_GET['plugins-deactivator'] ) || 'reactivate' !== $_GET['plugins-deactivator'] ) {
			return;
		}

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'plugins-deactivator' ) || ! current_user_can( 'activate_plugins' ) ) {
			die();
		}

		// Get the plugins that were active
		$active_plugins = get_option( $this->option_key );		

		// Reactivate all the plugins
		activate_plugins( $active_plugins, '', false, true );

		// Delete the previously stored plugins
		delete_option( $this->option_key );

		$_REQUEST['plugins_reactivated'] = count( $active_plugins );
	}
}

endif;

add_action( 'plugins_loaded', 'plugins_deactivator_boot' );
/**
 * Initialize the plugin
 *
 * @return void
 */
function plugins_deactivator_boot() {
	new Plugins_Deactivator;
}
