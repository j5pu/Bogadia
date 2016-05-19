<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    FV
 * @subpackage FV/admin
 * @author     Maxim K <wp-vote@hotmail.com>
 */
class FV_Admin_Pages
{

        /**
         * The ID of this plugin.
         *
         * @since    2.2.073
         * @access   private
         * @var      string $name The ID of this plugin.
         */
        private $name;

        public function __construct($name)
        {
                $this->name = $name;
        }

        /**
         * Home
         * @return void
         */
        public function page_home()
        {
                $contestClass = new FV_Contest();

                // Если пришёл параметр - очищаем результаты голосования
                if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add') {
                        $action = 'add';
                        wp_enqueue_style('dashicons');
                        FV_Admin::assets_page_edit_contest();

                        include FV::$ADMIN_PARTIALS_ROOT . 'contest_single.php';
                } elseif (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit') && isset($_REQUEST['contest'])) {

                        if ( isset($_POST['contest_title']) ) {
                                $contest_id = $contestClass->save();
                                $action = 'save';
                        } else {
                                $contest_id = (int)$_REQUEST['contest'];
                                $action = 'edit';
                        }

                        wp_enqueue_style('dashicons');
                        FV_Admin::assets_page_edit_contest();

                        $countdowns = apply_filters('fv/countdown/list', array() );

                        $contest = $contestClass->get_contest($contest_id);

                        // Reset security_type if $recaptcha_key is not set
                        if (
                                $contest->security_type == "defaultArecaptcha" &&
                                ( FvFunctions::ss('recaptcha-key', false, 5) == false || FvFunctions::ss('recaptcha-secret-key', false, 5) == false )
                           )
                        {
                            wp_add_notice(
                                sprintf("Please set <a href='%s'>reCAPTCHA API key</a> for use Recaptcha security!", admin_url("admin.php?page=fv-settings#additional")),
                                'warning'
                            );
                        }
                        // Reset security_type if $recaptcha_key is not set
                        if ( $contest->security_type == "defaultAfb" && get_option('fotov-fb-apikey', '') == '' ) {
                            //$contest->security_type = 'default';
                            wp_add_notice(
                                sprintf("Please set <a href='%s'>Facebook API key</a> for use Facebook Share security!", admin_url("admin.php?page=fv-settings#additional")),
                                'warning'
                            );
                        }

                        include FV::$ADMIN_PARTIALS_ROOT . 'contest_single.php';
                } else {
                        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete' && isset($_REQUEST['contest'])) {
                                $contestClass->delete((int)$_REQUEST['contest']);
                                $action = 'delete';
                        }
                        include FV::$ADMIN_PARTIALS_ROOT . 'admin-contests_page.php';
                }
        }

    /**
     * Subscribers list page
     * @return void
     */
    public function page_subscribers_list()
    {
        if (isset($_GET["fv-page"])) {
            $page = $_GET["fv-page"];
        } else {
            $page = 0;
        };
        $my_db = new FV_DB;

        $stats = $my_db->getSubsrStats($page);
        //var_dump($stats);

        include FV::$ADMIN_PARTIALS_ROOT . 'admin-subscribers-list.php';
    }


    /**
     * Form
     * @return void
     */
    public function page_form_builder()
    {
        FV_Admin::assets_page_form_builder();

        include FV::$ADMIN_PARTIALS_ROOT . 'form-builder.php';
    }

    /**
         * Votes log page
         * @return void
         */
        public function page_votes_log()
        {
                //Create an instance of our package class...
                $Table = new FV_List_Votes_Log();
                //Fetch, prepare, sort, and filter our data...
                $Table->prepare_items();

                include FV::$ADMIN_PARTIALS_ROOT . 'admin-log-list.php';
        }

    /**
         * Analytic: map
         * @return void
         */
        public function page_analytic()
        {
            FV_Admin::assets_lib_jvectormap();
            FV_Admin::assets_lib_amstockchart();

            $my_db = new FV_DB;
            $contests = $my_db->getContests(array());
            $votes_arr = array();

            foreach ($contests as $item) {
                    $contests[$item->id] = $item->id . ' / ' . $item->name;
            }

            $selected_id = false;
            $selected_photo_id = 0;

            if (isset($_GET['contest_id']) && $_GET['contest_id'] > 0) {
                $selected_id = (int)$_GET['contest_id'];

                $photos = ModelCompetitors::query()->where_all(array('contest_id' => $selected_id))->find();
                if (isset($_GET['photo_id']) && $_GET['photo_id'] > 0) {
                        $selected_photo_id = (int)$_GET['photo_id'];

                        $votes = ModelVotes::query()->
                            where_all(array("contest_id" => $selected_id, "vote_id" => $selected_photo_id))
                            ->limit(3000)
                            ->find();
                } else {
                        $votes = ModelVotes::query()->where_all(array("contest_id" => $selected_id))->limit(3000)->find();
                }
            } else {
                $votes = ModelVotes::query()->find();
            }

            foreach ($votes as $vote) {
                $cc = fv_2letter_country($vote->country);
                if (isset($votes_arr[$cc])) {
                        $votes_arr[$cc]++;
                } else {
                        $votes_arr[$cc] = 1;
                }
            }

            $chart_votes_arr = array();
            foreach ($votes as $vote) {
                $date = date( 'Y-m-d 00:00:00', strtotime($vote->changed) );
                //var_dump($date);

                if ( !isset($chart_votes_arr[$date]) ) {
                    $chart_votes_arr[ $date ] = 1;
                } else {
                    $chart_votes_arr[ $date ]++;
                }
            }
            $chart_votes_arr_res = array();
            foreach ($chart_votes_arr as $date => $votes) {
                $tmp['date']=$date;
                $tmp['votes']=$votes;
                $chart_votes_arr_res[] = $tmp;
            }

            //var_dump($chart_votes_arr);

            include FV::$ADMIN_PARTIALS_ROOT . 'admin-votes-analytic.php';
        }

        /**
         * Error log page
         * @return void
         */
        public function page_debug()
        {
            FV_Admin::assets_lib_codemirror(true);
            FV_Admin::assets_lib_tooltip();
            include FV::$ADMIN_PARTIALS_ROOT . 'page-debug.php';
        }

        /**
         * Translation page
         * @return void
         */
        public function page_translation()
        {
                $key_groups = fv_get_public_translation_key_titles();
                $messages = fv_get_public_translation_messages();
                $saved = false;

                if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'save') {
                    foreach ($key_groups as $group_name => $group_fields) :
                        foreach ($group_fields as $key => $value) {
                                // если пришла эта опция
                                if ( isset($_POST[$key]) && !in_array($key, fv_get_public_translation_textareas()) ) {
                                    $messages[$key] = sanitize_text_field($_POST[$key]);
                                } elseif ( isset($_POST[$key]) ) {
                                    $messages[$key] = $_POST[$key];
                                }
                        }
                        fv_update_public_translation_messages($messages);
                        $saved = true;
                    endforeach;
                }

                include FV::$ADMIN_PARTIALS_ROOT . 'admin-translations.php';
        }

        /**
         * Moderation photos page
         * @return void
         */
        public function page_moderation()
        {
                $my_db = new FV_DB;
                $items = $my_db->getCompItemsOnModeration();
                //var_dump(items);
                include FV::$ADMIN_PARTIALS_ROOT . 'admin-moderation-list.php';
        }

        /**
         * Settings page
         * @return void
         */
        public function page_settings()
        {
                $settings = get_option('fv', array() );
                include FV::$ADMIN_PARTIALS_ROOT . 'admin-settings_page.php';
        }

}
