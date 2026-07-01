<?php
/**
 * Uninstall cleanup.
 *
 * Portfolio posts, terms, and metadata are intentionally preserved to prevent
 * destructive data loss. Only plugin-owned options are removed.
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'mpro_portfolio_settings' );
delete_option( 'mpro_portfolio_version' );
