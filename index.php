<?php

/*
Plugin Name: ReClean
Description: Revision cleaner
Plugin URI: http://#
Author: Web Cartel
Author URI: http://web-cartel.ru
Version: 1.0
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'WCST_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'WCST_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCST_WP_UPLOADS_DIR_PATH', wp_upload_dir()['basedir'] );
define( 'WCST_PLUGIN_UPLOADS_DIR_URL', wp_upload_dir()['baseurl'] . '/reclean-backup-files' );
define( 'WCST_PLUGIN_UPLOADS_DIR_PATH', WCST_WP_UPLOADS_DIR_PATH . '/reclean-backup-files' );



function wcst_reclean_activate() {
	if ( !file_exists( WCST_PLUGIN_UPLOADS_DIR_PATH ) ) {
		mkdir( WCST_PLUGIN_UPLOADS_DIR_PATH );
	}
}
register_activation_hook( __FILE__, 'wcst_reclean_activate' );




include('inc/functions.php');
include('inc/db_info.php');
include('inc/post_clean.php');
include('inc/all_clean.php');
include('inc/db_backup.php');