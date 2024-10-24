<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SMDS_CPT {
    public function __construct() {
        add_action( 'init', array( $this, 'register_cpt' ) );
        add_action( 'init', array( $this, 'register_taxonomy' ) );
    }

    /**
     * Register the 'Document' custom post type.
     */
    public function register_cpt() {
        $labels = array(
            'name'               => __( 'Documents', 'smds' ),
            'singular_name'      => __( 'Document', 'smds' ),
            'add_new'            => __( 'Add New Document', 'smds' ),
            'add_new_item'       => __( 'Add New Document', 'smds' ),
            'edit_item'          => __( 'Edit Document', 'smds' ),
            'new_item'           => __( 'New Document', 'smds' ),
            'view_item'          => __( 'View Document', 'smds' ),
            'search_items'       => __( 'Search Documents', 'smds' ),
            'not_found'          => __( 'No documents found', 'smds' ),
            'not_found_in_trash' => __( 'No documents found in Trash', 'smds' ),
            'menu_name'          => __( 'Documents', 'smds' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => false,
            'rewrite'            => array( 'slug' => 'docs' ),
            'supports'           => array( 'title', 'editor' ),
            'show_in_rest'       => true,
        );

        register_post_type( 'smds_document', $args );
    }

    /**
     * Register custom taxonomy for documents.
     */
    public function register_taxonomy() {
        $labels = array(
            'name'              => __( 'Document Categories', 'smds' ),
            'singular_name'     => __( 'Document Category', 'smds' ),
            'search_items'      => __( 'Search Categories', 'smds' ),
            'all_items'         => __( 'All Categories', 'smds' ),
            'parent_item'       => __( 'Parent Category', 'smds' ),
            'parent_item_colon' => __( 'Parent Category:', 'smds' ),
            'edit_item'         => __( 'Edit Category', 'smds' ),
            'update_item'       => __( 'Update Category', 'smds' ),
            'add_new_item'      => __( 'Add New Category', 'smds' ),
            'new_item_name'     => __( 'New Category Name', 'smds' ),
            'menu_name'         => __( 'Categories', 'smds' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'rewrite'           => array( 'slug' => 'docs-category' ),
            'show_in_rest'      => true,
        );

        register_taxonomy( 'smds_category', array( 'smds_document' ), $args );
    }
}