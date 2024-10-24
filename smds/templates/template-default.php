<?php get_header(); ?>

<?php
// Step 1: Retrieve the selected file type
$file_type = get_post_meta( get_the_ID(), '_smds_file_type_image', true );

// Step 2: Get custom icons from plugin options
$file_type_icons = get_option( 'smds_file_type_icons', array() );

// Step 3: Determine the icon URL
$default_icons_url = SMDS_PLUGIN_URL . 'assets/images/icons/';
if ( isset( $file_type_icons[ $file_type ] ) && ! empty( $file_type_icons[ $file_type ] ) ) {
    $icon_url = $file_type_icons[ $file_type ];
} else {
    $icon_url = $default_icons_url . $file_type . '.png';
}

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        ?>
        <div class="smds-document">
            <h1>
                <?php
                if ( ! empty( $icon_url ) ) {
                    echo '<img src="' . esc_url( $icon_url ) . '" alt="' . esc_attr( $file_type ) . ' icon" class="smds-file-type-icon" style="vertical-align: middle; margin-right: 10px;">';
                }
                the_title();
                ?>
            </h1>

            <div class="smds-content">
                <?php the_content(); ?>
            </div>

            <div class="smds-download-links">
                <h2><?php _e( 'Download Files:', 'smds' ); ?></h2>
                <ul>
                    <?php
                    $languages = get_option( 'smds_languages', array( 'en' => array( 'country' => 'English', 'flag' => '' ) ) );
                    foreach ( $languages as $code => $language ) {
                        $file_id = get_post_meta( get_the_ID(), 'smds_file_' . $code, true );
                        if ( $file_id ) {
                            $download_url = trailingslashit( get_permalink() ) . 'download/' . $code;
                            $flag_url = isset( $language['flag'] ) ? $language['flag'] : '';

                            echo '<li>';
                            // Display the flag image if available
                            if ( ! empty( $flag_url ) ) {
                                echo '<img src="' . esc_url( $flag_url ) . '" alt="' . esc_attr( $language['country'] ) . ' flag" style="vertical-align: middle; margin-right: 5px;">';
                            }
                            // Display the download link
                            echo '<a href="' . esc_url( $download_url ) . '">' . esc_html( $language['country'] ) . '</a>';
                            echo '</li>';
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
        <?php
    endwhile;
endif;

get_footer();