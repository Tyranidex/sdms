<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SMDS_Custom_Fields {
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_language_fields' ) );
        add_action( 'save_post', array( $this, 'save_language_files' ) );
    }

    /**
     * Add meta boxes for each language.
     */
    public function add_language_fields() {
        add_meta_box(
            'smds_language_files',
            __( 'Language Files', 'smds' ),
            array( $this, 'render_language_fields' ),
            'smds_document',
            'normal',
            'default'
        );
    }

    /**
     * Render the meta box content.
     */
    public function render_language_fields( $post ) {
        wp_nonce_field( 'smds_save_files', 'smds_files_nonce' );

        // Get available languages from settings
        $languages = get_option( 'smds_languages', array( 'en' => array( 'country' => 'English', 'flag' => '' ) ) );

        foreach ( $languages as $code => $language ) {
            $file_id = get_post_meta( $post->ID, 'smds_file_' . $code, true );
            $file_url = $file_id ? wp_get_attachment_url( $file_id ) : '';

            echo '<div class="smds-language-file">';
            echo '<label>' . esc_html( $language['country'] ) . ':</label><br>';
            echo '<input type="hidden" name="smds_file_' . esc_attr( $code ) . '" value="' . esc_attr( $file_id ) . '">';

            // Upload button
            $upload_style = $file_id ? 'display: none;' : '';
            echo '<input type="button" class="button smds-upload-button" data-language="' . esc_attr( $code ) . '" value="' . __( 'Upload File', 'smds' ) . '" style="' . esc_attr( $upload_style ) . '">';

            // View and Remove buttons
            if ( $file_url ) {
                $file_name = basename( get_attached_file( $file_id ) );
                echo '<span class="smds-file-info">';
                echo ' <a href="' . esc_url( $file_url ) . '" target="_blank" class="button">' . __( 'View File', 'smds' ) . '</a>';
                echo ' <button type="button" class="button smds-remove-file-button" data-language="' . esc_attr( $code ) . '">' . __( 'Remove File', 'smds' ) . '</button>';
                echo ' <span class="smds-file-name">' . esc_html( $file_name ) . '</span>';
                echo '</span>';
            } else {
                echo '<span class="smds-file-info" style="display: none;"></span>';
            }

            echo '</div>';
        }

        // Include JavaScript for media uploader
        $this->enqueue_media_script();
    }


    /**
     * Enqueue script for media uploader.
     */
    private function enqueue_media_script() {
        wp_enqueue_media();
        wp_enqueue_script( 'smds-media-uploader', SMDS_PLUGIN_URL . 'assets/js/media-uploader.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'smds-media-uploader', 'smdsUploader', array(
            'title'     => __( 'Choose File', 'smds' ),
            'button'    => __( 'Use this file', 'smds' ),
            'viewFile'  => __( 'View File', 'smds' ),
            'removeFile'=> __( 'Remove File', 'smds' ),
        ) );
    }

    /**
     * Save the uploaded files.
     */
    public function save_language_files( $post_id ) {
        if ( ! isset( $_POST['smds_files_nonce'] ) || ! wp_verify_nonce( $_POST['smds_files_nonce'], 'smds_save_files' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( 'smds_document' !== $_POST['post_type'] || ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $languages = get_option( 'smds_languages', array( 'en' => 'English' ) );

        foreach ( $languages as $code => $language ) {
            $file_id = isset( $_POST[ 'smds_file_' . $code ] ) ? intval( $_POST[ 'smds_file_' . $code ] ) : '';
            update_post_meta( $post_id, 'smds_file_' . $code, $file_id );
        }
    }
}