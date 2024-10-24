<?php
/*
Plugin Name: Simple Document Management System (SDMS)
Description: A plugin to manage documents with multilingual support.
Version: 1.0.0
Author: Dorian Renon
Text Domain: smds
Domain Path: /languages
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'SMDS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SMDS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include necessary files
require_once SMDS_PLUGIN_DIR . 'includes/class-smds-cpt.php';
require_once SMDS_PLUGIN_DIR . 'includes/class-smds-admin.php';
require_once SMDS_PLUGIN_DIR . 'includes/class-smds-frontend.php';
require_once SMDS_PLUGIN_DIR . 'includes/class-smds-custom-fields.php';
require_once SMDS_PLUGIN_DIR . 'includes/class-smds-settings.php';
require_once SMDS_PLUGIN_DIR . 'includes/functions.php';

// Initialize classes
function smds_init() {
    new SMDS_CPT();
    new SMDS_Admin();
    new SMDS_Custom_Fields();
    new SMDS_Settings();
    new SMDS_Frontend();
}
add_action( 'plugins_loaded', 'smds_init' );
