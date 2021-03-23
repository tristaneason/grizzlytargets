<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<style type="text/css">
    <?php
    $cfw = \Objectiv\Plugins\Checkout\Main::instance();
    $active_template = $cfw->get_templates_manager()->getActiveTemplate()->get_slug();
    $header_background_color = $cfw->get_settings_manager()->get_setting('header_background_color', array( $active_template ) );

    if ( $header_background_color == "#ffffff" ) {
        $header_background_color = "#333";
    }
    ?>
    /**
    Special Futurist breadcrumb styles
     */
    #cfw-breadcrumb:after {
        background: <?php echo $header_background_color; ?>;
    }

    #cfw-breadcrumb li > a {
        color: <?php echo $header_background_color; ?>;
    }

    #cfw-breadcrumb .filled-circle:before {
        background: <?php echo $header_background_color; ?>;
    }

    #cfw-breadcrumb li:before {
        border: 2px solid <?php echo $header_background_color; ?>;
    }
</style>
<?php do_action( 'cfw_before_header' ) ;?>
<header id="cfw-header">
    <div class="wrap">
        <div class="cfw-container cfw-column-12">
            <div id="cfw-logo-container">
                <!-- TODO: Find a way to inject certain backend settings as global params without having to put logic in the templates -->
                <div class="cfw-logo">
                    <a title="<?php echo get_bloginfo( 'name' ); ?>" href="<?php echo apply_filters( 'cfw_header_home_url', get_home_url() ); ?>" class="logo"></a>
                </div>
            </div>
        </div>
    </div>
</header>
<?php do_action( 'cfw_after_header' ) ;?>