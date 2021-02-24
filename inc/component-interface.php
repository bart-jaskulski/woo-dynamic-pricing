<?php
/**
 * Dentonet\WP\Common\Component_Interface interface
 *
 * @package denotnet
 */

namespace Dentonet\WP\Common;

defined( 'ABSPATH' ) || exit;

/**
 * Implement this interface if any WordPress hooks are in use.
 */
interface Component_Interface {

	/**
	 * Add all hooks and filters to integrate with WordPress.
	 */
	public function initialize();
}
