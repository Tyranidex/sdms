<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SMDS_Admin {

    public function __construct() {
        // Enqueue admin scripts and styles
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // Modify the CPT document edit screen
        add_action( 'add_meta_boxes_smds_document', array( $this, 'replace_featured_image_metabox' ), 20 );

        // Save the custom featured image
        add_action( 'save_post', array( $this, 'save_custom_featured_image' ) );
    }

    /**
     * Enqueue admin assets.
     */
    public function enqueue_admin_assets( $hook ) {
        // Enqueue only on the post edit screen
        if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
            wp_enqueue_style( 'smds-admin-styles', SMDS_PLUGIN_URL . 'assets/css/admin-styles.css' );
            // wp_enqueue_script( 'smds-admin-scripts', SMDS_PLUGIN_URL . 'assets/js/admin-scripts.js', array( 'jquery' ), '1.0.0', true );
        }
    }

    /**
     * Replace the featured image metabox with custom file type image selection.
     */
    public function replace_featured_image_metabox() {
        remove_meta_box( 'postimagediv', 'smds_document', 'side' );
        add_meta_box(
            'smds_file_type_image',
            __( 'File Type Image', 'smds' ),
            array( $this, 'render_file_type_image_metabox' ),
            'smds_document',
            'side',
            'default'
        );
    }

    /**
     * Render the file type image metabox.
     */
    public function render_file_type_image_metabox( $post ) {
        wp_nonce_field( 'smds_save_file_type_image', 'smds_file_type_image_nonce' );

        $selected_image = get_post_meta( $post->ID, '_smds_file_type_image', true );
        $file_types = array(
            'pdf'   => __( 'PDF', 'smds' ),
            'word'  => __( 'Word Document', 'smds' ),
            'excel' => __( 'Excel Spreadsheet', 'smds' ),
            'image' => __( 'Image', 'smds' ),
            'video' => __( 'Video', 'smds' ),
            'psd'   => __( 'Photoshop File', 'smds' ),
            'ai'    => __( 'Illustrator File', 'smds' ),
        );

        $file_type_icons = get_option( 'smds_file_type_icons', array() );

        echo '<div class="smds-file-type-image-options">';
        foreach ( $file_types as $key => $label ) {
            $icon_url = isset( $file_type_icons[ $key ] ) ? $file_type_icons[ $key ] : SMDS_PLUGIN_URL . 'assets/images/icons/' . $key . '.png';

            echo '<label style="display: inline-block; margin-right: 10px; text-align: center;">';
            echo '<input type="radio" name="smds_file_type_image" value="' . esc_attr( $key ) . '" ' . checked( $selected_image, $key, false ) . '>';
            echo '<img src="' . esc_url( $icon_url ) . '" alt="' . esc_attr( $label ) . '" style="display: block; max-width: 50px; max-height: 50px;">';
            echo '<span>' . esc_html( $label ) . '</span>';
            echo '</label>';
        }
        echo '</div>';
    }

    /**
     * Save the custom featured image.
     */
    public function save_custom_featured_image( $post_id ) {
        if ( ! isset( $_POST['smds_file_type_image_nonce'] ) || ! wp_verify_nonce( $_POST['smds_file_type_image_nonce'], 'smds_save_file_type_image' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( 'smds_document' !== $_POST['post_type'] || ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $file_type_image = sanitize_text_field( $_POST['smds_file_type_image'] );
        update_post_meta( $post_id, '_smds_file_type_image', $file_type_image );
    }
}