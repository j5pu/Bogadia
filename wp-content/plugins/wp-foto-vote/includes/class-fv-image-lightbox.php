<?php
/**
 * ImageLightbox library wrapper
 *
 * ================================
 * Usage this structure allows simply add new lightbox to contest list
 *
 * Need only add filter into FV::PREFIX . 'lightbox_list_array'
 * (append you lightbox name and theme, like 'imageLightbox_default')
 *
 * And add action for
 * FV::PREFIX . 'load_lightbox_imageLightbox'
 * ================================
 *
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <wp-vote@hotmail.com>
 */
class Fv_Image_Lightbox {

    const NAME = 'imageLightbox';

    /**
     * Enqueue assets
     *
     * @since    2.2.082
     *
     * @param string $theme     Key, like `default`
     * @return void
     */
    public static function assets ( $theme = '' ) {

        wp_enqueue_script( 'fv-lightbox-imageLightbox-js',  fv_min_url(FV::$ASSETS_URL . 'imageLightbox/jQuery.imageLightbox.js'), array('jquery'), FV::VERSION, true );
        wp_enqueue_style( 'fv-lightbox-imageLightbox-css', fv_min_url(FV::$ASSETS_URL . 'imageLightbox/jQuery.imageLightbox.css'), array(), FV::VERSION );

    }

    /**
     * Return name of action, that must be called for load this lightbox assets
     *
     * @since    2.2.082
     *
     * @return string
     */
    public static function getActionName () {
        return FV::PREFIX . 'load_lightbox_' . self::NAME;
    }

    /**
     * Add supported themes list to settings
     *
     * @since    2.2.082
     *
     * @param array $lightbox_list
     * @return array
     */
    public static function initListThemes ( $lightbox_list ) {
        //FV::PREFIX . 'lightbox_list_array'
        return array_merge(
            array(
                'imageLightbox_default' => 'imageLightbox :: default',
            ),
            $lightbox_list
        );
    }
}