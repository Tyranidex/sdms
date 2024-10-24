<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SMDS_Frontend {
    public function __construct() {
        add_filter( 'post_type_link', array( $this, 'custom_post_link' ), 10, 2 );
        add_action( 'init', array( $this, 'add_rewrite_rules' ) );
        add_action( 'template_include', array( $this, 'load_custom_template' ) );
        add_action( 'wp', array( $this, 'handle_download' ) );
    }

    /**
     * Modify the permalink structure.
     */
    public function custom_post_link( $post_link, $post ) {
        if ( $post->post_type !== 'smds_document' ) {
            return $post_link;
        }

        $terms = wp_get_post_terms( $post->ID, 'smds_category' );
        if ( $terms ) {
            usort( $terms, function ( $a, $b ) {
                return $a->parent - $b->parent;
            } );

            $term_slugs = array();
            foreach ( $terms as $term ) {
                $term_slugs[] = $term->slug;
            }

            $taxonomy_slug = implode( '/', $term_slugs );
            return trailingslashit( home_url( 'docs/' . $taxonomy_slug . '/' . $post->post_name ) );
        } else {
            return trailingslashit( home_url( 'docs/' . $post->post_name ) );
        }
    }

    /**
     * Add custom rewrite rules.
     */
    public function add_rewrite_rules() {
        add_rewrite_rule(
            '^docs/(.+)/download/([a-z]{2})/?',
            'index.php?smds_download=1&language=$matches[2]&smds_path=$matches[1]',
            'top'
        );

        add_rewrite_rule(
            '^docs/(.+)/download/?',
            'index.php?smds_download=1&language=en&smds_path=$matches[1]',
            'top'
        );

        add_rewrite_rule(
            '^docs/(.+)/?$',
            'index.php?smds_document=$matches[1]',
            'top'
        );

        // Add query vars
        add_filter( 'query_vars', function ( $vars ) {
            $vars[] = 'smds_download';
            $vars[] = 'language';
            $vars[] = 'smds_path';
            $vars[] = 'smds_document';
            return $vars;
        } );
    }

    /**
     * Load custom template for the document post type.
     */
    public function load_custom_template( $template ) {
        if ( get_query_var( 'smds_document' ) ) {
            $selected_template = get_option( 'smds_template', 'template-default.php' );
            $custom_template = SMDS_PLUGIN_DIR . 'templates/' . $selected_template;
            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }
        return $template;
    }

    /**
     * Handle file downloads.
     */
    public function handle_download() {
        if ( get_query_var( 'smds_download' ) ) {
            $path = get_query_var( 'smds_path' );
            $language = get_query_var( 'language', 'en' );

            // Find the post based on the path
            $post = $this->get_post_by_path( $path );
            if ( $post ) {
                $file_id = get_post_meta( $post->ID, 'smds_file_' . $language, true );
                if ( $file_id ) {
                    $file_path = get_attached_file( $file_id );
                    if ( $file_path && file_exists( $file_path ) ) {
                        // Serve the file content directly
                        $file_type = wp_check_filetype( $file_path );
                        $mime_type = $file_type['type'];
                        $file_name = basename( $file_path );

                        // Set headers
                        header( 'Content-Description: File Transfer' );
                        header( 'Content-Type: ' . $mime_type );
                        header( 'Content-Disposition: inline; filename="' . $file_name . '"' );
                        header( 'Content-Transfer-Encoding: binary' );
                        header( 'Expires: 0' );
                        header( 'Cache-Control: must-revalidate' );
                        header( 'Pragma: public' );
                        header( 'Content-Length: ' . filesize( $file_path ) );

                        // Clean output buffer
                        ob_clean();
                        flush();

                        // Read the file
                        readfile( $file_path );
                        exit;
                    }
                }
            }
            // If no file found, display 404
            wp_die( __( 'File not found.', 'smds' ), 404 );
        }
    }

    /**
     * Helper function to get post by path.
     */
    private function get_post_by_path( $path ) {
        global $wpdb;

        $path_parts = explode( '/', $path );
        $post_name = array_pop( $path_parts );

        $post = get_page_by_path( $post_name, OBJECT, 'smds_document' );
        return $post;
    }
}