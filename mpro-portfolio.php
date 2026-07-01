<?php
/**
 * Plugin Name: MPRO Portfolio
 * Plugin URI: https://github.com/moghadampro/mpro-portfolio
 * Description: A structured portfolio manager with reusable card styles, Elementor integration, shortcodes, archives, and Rank Math support.
 * Version: 1.1.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Sayid Moghadam
 * Text Domain: mpro-portfolio
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package MPRO_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MPRO_PORTFOLIO_VERSION', '1.1.0' );
define( 'MPRO_PORTFOLIO_FILE', __FILE__ );
define( 'MPRO_PORTFOLIO_DIR', plugin_dir_path( __FILE__ ) );
define( 'MPRO_PORTFOLIO_URL', plugin_dir_url( __FILE__ ) );

require_once MPRO_PORTFOLIO_DIR . 'includes/class-mpro-portfolio-plugin.php';
require_once MPRO_PORTFOLIO_DIR . 'includes/class-mpro-portfolio-content-types.php';
require_once MPRO_PORTFOLIO_DIR . 'includes/class-mpro-portfolio-admin-menu.php';
require_once MPRO_PORTFOLIO_DIR . 'includes/class-mpro-portfolio-settings.php';
require_once MPRO_PORTFOLIO_DIR . 'includes/class-mpro-portfolio-meta-boxes.php';
require_once MPRO_PORTFOLIO_DIR . 'includes/class-mpro-portfolio-admin-list.php';
require_once MPRO_PORTFOLIO_DIR . 'includes/class-mpro-portfolio-renderer.php';
require_once MPRO_PORTFOLIO_DIR . 'includes/class-mpro-portfolio-shortcodes.php';
require_once MPRO_PORTFOLIO_DIR . 'includes/class-mpro-portfolio-templates.php';
require_once MPRO_PORTFOLIO_DIR . 'includes/class-mpro-portfolio-elementor.php';
require_once MPRO_PORTFOLIO_DIR . 'includes/class-mpro-portfolio-rank-math.php';
require_once MPRO_PORTFOLIO_DIR . 'includes/functions.php';

register_activation_hook( __FILE__, array( 'MPRO_Portfolio_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'MPRO_Portfolio_Plugin', 'deactivate' ) );

MPRO_Portfolio_Plugin::instance()->boot();
