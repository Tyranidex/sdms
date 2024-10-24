<?php get_header(); ?>
<h1>TEMPLATE ALTERNATIF</h1>
<?php if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        ?>
        <div class="smds-document">
            <h1><?php the_title(); ?></h1>
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
                            echo '<li><a href="' . esc_url( $download_url ) . '">' . esc_html( $language['country'] ) . '</a></li>';
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