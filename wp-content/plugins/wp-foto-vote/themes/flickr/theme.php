<?php

if ( defined('DOING_AJAX') && DOING_AJAX == TRUE && FvFunctions::ss('pagination-type', 'default') == 'infinite' ) {
    return;
}

add_action('fv_before_shows_loop',  'fv_flickr_before_show');
add_action('fv_after_shows_loop',  'fv_flickr_after_show');
//add_action('fv_after_contest_list',  'fv_pinterest_contest_list');

function fv_flickr_before_show() {
        echo '<div class="column-grid photo-display-container ju flex-images justified-gallery" id="grid">';
}

function fv_flickr_after_show() {
        echo '</div>
                    <div id="progress" class="waiting"><dt></dt><dd></dd></div>
                ';
}

class FvTheme extends FvThemeBase {
    /**
     * Theme name
     *
     * @since    2.2.081
     * @access   public
     */
    const THEME = "flickr";

    public static function init($customLeaders, $newPage) {
        parent::init($customLeaders, $newPage);
        add_filter('fv_contest_item_template_data', array('FvTheme', 'single_item_template_data_filter'), 1 );
    }

    public static function assets_item() {
        parent::assets_item();
        wp_enqueue_script('fv_exif', FV::$ASSETS_URL . 'js/exif.js', array() , FV::VERSION, true);
        wp_enqueue_script('fv_theme_flickr', FvFunctions::get_theme_url ( self::THEME, 'assets/fv_theme_flickr.js' ), array( 'jquery' ) , FV::VERSION);
    }

    public static function assets_list() {
        parent::assets_list();
        //wp_enqueue_style('fv_theme_flickr_flex', FvFunctions::get_theme_url ( self::THEME, 'assets/jquery.flex-images.css' ) );
        wp_enqueue_script('fv_theme_flickr', FvFunctions::get_theme_url ( self::THEME, 'assets/fv_theme_flickr.js' ), array( 'jquery' ) , FV::VERSION);
    }

    public static function single_item_template_data_filter($template_data) {
        $template_data['most_voted'] = ModelCompetitors::query()
                                        ->limit(8)
                                        ->where_not( 'id', $template_data["contestant"]->id )
                                        ->where( 'contest_id', $template_data["contest_id"] )
                                        ->sort_by( 'RAND()' )
                                        ->find();
        // Is this item in leaders and contest are ended ?
        $template_data['is_most_voted'] = false;
        // if contests ends
        if ( !$template_data['konurs_enabled'] && current_time('timestamp', 0) > strtotime($template_data['contest']->date_finish) ) {
            $most_voted_arr = ModelCompetitors::query()
                ->limit(3)
                ->where( 'contest_id', $template_data["contest_id"] )
                ->sort_by( 'votes_count' )
                ->order( 'DESC' )
                ->find();
            $most_voted_places_names = array(
                '0'=> __('first', 'fv'),
                '1'=> __('second', 'fv'),
                '2'=> __('third', 'fv'),
                '3'=> __('fourth', 'fv'),
            );
            foreach($most_voted_arr as $key => $most_voted_item) {
                if ( $most_voted_item->id == $template_data["contestant"]->id ) {
                    $template_data['is_most_voted'] = true;
                    $template_data['most_voted_place'] = $most_voted_places_names[$key];
                    break;
                }
            }
        }
        return $template_data;
    }

}

// Init settings
FvTheme::init($customLeaders = true, $newPage = true);