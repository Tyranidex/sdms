<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SMDS_Settings {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Add plugin settings page.
     */
    public function add_settings_page() {
        add_options_page(
            __( 'SDMS Settings', 'smds' ),
            __( 'SDMS Settings', 'smds' ),
            'manage_options',
            'smds-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Register settings.
     */
    public function register_settings() {
        register_setting( 'smds_settings_group', 'smds_languages', array( $this, 'sanitize_languages' ) );
        register_setting( 'smds_settings_group', 'smds_template' );
        register_setting( 'smds_settings_group', 'smds_file_type_icons', array( $this, 'sanitize_file_type_icons' ) );
    }

    /**
     * Sanitize languages input.
     */
    public function sanitize_languages( $input ) {
        $sanitized = array();
        if ( is_array( $input ) ) {
            foreach ( $input as $code => $language ) {
                $sanitized[ sanitize_text_field( $code ) ] = array(
                    'country' => sanitize_text_field( $language['country'] ),
                    'flag'    => esc_url_raw( $language['flag'] ),
                );
            }
        }
        return $sanitized;
    }

    /**
     * Sanitize icons input.
     */
    public function sanitize_file_type_icons( $input ) {
        $sanitized = array();
        if ( is_array( $input ) ) {
            foreach ( $input as $type => $url ) {
                $sanitized[ sanitize_text_field( $type ) ] = esc_url_raw( $url );
            }
        }
        return $sanitized;
    }

    /**
     * Sanitize icons javascript.
     */
    private function enqueue_icon_uploader_script() {
        wp_enqueue_media();
        wp_enqueue_script( 'smds-icon-uploader', SMDS_PLUGIN_URL . 'assets/js/icon-uploader.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'smds-icon-uploader', 'smdsIconUploader', array(
            'title'  => __( 'Choose Icon', 'smds' ),
            'button' => __( 'Use this icon', 'smds' ),
        ) );
    }

    /**
     * Render the settings page content.
     */
    public function render_settings_page() {
        // Get the selected template
        $selected_template = get_option( 'smds_template', 'template-default.php' );

        // Scan the templates directory for available templates
        $templates_dir = SMDS_PLUGIN_DIR . 'templates/';
        $templates = glob( $templates_dir . 'template-*.php' );

        // Prepare templates array
        $template_options = array();
        foreach ( $templates as $template_path ) {
            $template_file = basename( $template_path );
            $template_name = ucwords( str_replace( array( 'template-', '.php', '-' ), array( '', '', ' ' ), $template_file ) );
            $template_options[ $template_file ] = $template_name;
        }

        // Get existing file type icons
        $file_types = array(
            'pdf'   => __( 'PDF', 'smds' ),
            'word'  => __( 'Word', 'smds' ),
            'excel' => __( 'Excel', 'smds' ),
            'image' => __( 'Image', 'smds' ),
            'video' => __( 'Video', 'smds' ),
            'psd'   => __( 'Photoshop', 'smds' ),
            'ai'    => __( 'Illustrator', 'smds' ),
        );

        $file_type_icons = get_option( 'smds_file_type_icons', array() );

        ?>

        <div class="wrap">
            <h1><?php _e( 'SDMS Settings', 'smds' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'smds_settings_group' );
                $languages = get_option( 'smds_languages', array( 'en' => 'English' ) );

                // Load languages from JSON file
                $json_file = SMDS_PLUGIN_DIR . 'languages.json';
                $available_languages = array();
                if ( file_exists( $json_file ) ) {
                    $json_data = file_get_contents( $json_file );
                    $available_languages = json_decode( $json_data, true );
                }

                ?>
                <h2><?php _e( 'Add Languages', 'smds' ); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Available Languages', 'smds' ); ?></th>
                        <td>
                            <select id="smds_language_selector">
                                <?php
                                foreach ( $available_languages as $lang ) {
                                    echo '<option value="' . esc_attr( $lang['code'] ) . '">' . esc_html( $lang['country'] ) . '</option>';
                                }
                                ?>
                            </select>
                            <button type="button" class="button" id="smds_add_language"><?php _e( 'Add', 'smds' ); ?></button>
                        </td>
                    </tr>
                </table>

                <h2><?php _e( 'Template Settings', 'smds' ); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Select Template', 'smds' ); ?></th>
                        <td>
                            <select name="smds_template">
                                <?php foreach ( $template_options as $file => $name ): ?>
                                    <option value="<?php echo esc_attr( $file ); ?>" <?php selected( $selected_template, $file ); ?>>
                                        <?php echo esc_html( $name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <h2><?php _e( 'Added Languages', 'smds' ); ?></h2>
                <table class="form-table" id="smds_languages_table">
                    <?php
                    foreach ( $languages as $code => $language ) {
                        echo '<tr>';
                        echo '<td>';
                        echo '<img src="' . esc_url( $language['flag'] ) . '" alt="' . esc_attr( $language['country'] ) . '" style="vertical-align: middle; margin-right: 5px;">';
                        echo esc_html( $language['country'] ) . ' (' . esc_html( $code ) . ')';
                        echo '</td>';
                        echo '<td><button type="button" class="button smds-remove-language" data-code="' . esc_attr( $code ) . '">' . __( 'Remove', 'smds' ) . '</button></td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
                <!-- Output the existing languages as hidden inputs -->
                <div id="smds_languages_hidden_inputs">
                    <?php foreach ( $languages as $code => $language ): ?>
                        <input type="hidden" name="smds_languages[<?php echo esc_attr( $code ); ?>][country]" value="<?php echo esc_attr( $language['country'] ); ?>">
                        <input type="hidden" name="smds_languages[<?php echo esc_attr( $code ); ?>][flag]" value="<?php echo esc_attr( $language['flag'] ); ?>">
                    <?php endforeach; ?>
                </div>

                <h2><?php _e( 'File Type Icons', 'smds' ); ?></h2>
                <table class="form-table">
                    <?php foreach ( $file_types as $key => $label ): ?>
                        <tr valign="top">
                            <th scope="row"><?php echo esc_html( $label ); ?></th>
                            <td>
                                <?php
                                $icon_url = isset( $file_type_icons[ $key ] ) ? $file_type_icons[ $key ] : SMDS_PLUGIN_URL . 'assets/images/icons/' . $key . '.png';
                                ?>
                                <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $label ); ?>" style="max-width: 50px; max-height: 50px;">
                                <input type="hidden" name="smds_file_type_icons[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_url( $icon_url ); ?>">
                                <input type="button" class="button smds-upload-icon-button" data-file-type="<?php echo esc_attr( $key ); ?>" value="<?php _e( 'Change Icon', 'smds' ); ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <?php
                // Include JavaScript for media uploader
                $this->enqueue_icon_uploader_script();
                ?>
                <?php submit_button(); ?>
            </form>
        </div>

        <script>
            (function($){
                var availableLanguages = <?php echo json_encode( $available_languages ); ?>;

                function updateLanguageOptions() {
                    var addedCodes = [];
                    $('#smds_languages_hidden_inputs input[name^="smds_languages"]').each(function() {
                        var code = $(this).attr('name').match(/\[(.*?)\]/)[1];
                        if ($.inArray(code, addedCodes) === -1) {
                            addedCodes.push(code);
                        }
                    });
                    $('#smds_language_selector option').each(function() {
                        var option = $(this);
                        if ($.inArray(option.val(), addedCodes) !== -1) {
                            option.remove();
                        }
                    });
                }

                $('#smds_add_language').on('click', function(){
                    var selectedCode = $('#smds_language_selector').val();
                    var selectedLanguage = availableLanguages.find(lang => lang.code === selectedCode);

                    if (selectedLanguage) {
                        // Append to the table
                        $('#smds_languages_table').append('<tr><td><img src="' + selectedLanguage.flag + '" alt="' + selectedLanguage.country + '"> ' + selectedLanguage.country + ' (' + selectedCode + ')</td><td><button type="button" class="button smds-remove-language" data-code="' + selectedCode + '">' + '<?php _e( 'Remove', 'smds' ); ?>' + '</button></td></tr>');
                        // Append hidden inputs
                        $('#smds_languages_hidden_inputs').append(
                            '<input type="hidden" name="smds_languages[' + selectedCode + '][country]" value="' + selectedLanguage.country + '">' +
                            '<input type="hidden" name="smds_languages[' + selectedCode + '][flag]" value="' + selectedLanguage.flag + '">'
                        );
                        // Remove from dropdown
                        $('#smds_language_selector option[value="' + selectedCode + '"]').remove();
                    }
                });

                $(document).on('click', '.smds-remove-language', function(){
                    var code = $(this).data('code');
                    $(this).closest('tr').remove();
                    $('#smds_languages_hidden_inputs input[name^="smds_languages[' + code + ']"]').remove();
                    // Add back to dropdown
                    var language = availableLanguages.find(lang => lang.code === code);
                    if (language) {
                        $('#smds_language_selector').append('<option value="' + code + '">' + language.country + '</option>');
                    }
                });

                // Remove already added languages on page load
                $(document).ready(function() {
                    updateLanguageOptions();
                });
            })(jQuery);
            </script>
        <?php
    }
}