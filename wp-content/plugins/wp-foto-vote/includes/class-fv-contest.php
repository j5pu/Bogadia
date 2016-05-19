<?php

defined('ABSPATH') or die("No script kiddies please!");

/**
 * The contest class.
 *
 * Used from doing most operations with contest and photos - add/edit/deleted
 *
 * @since      ?
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <wp-vote@hotmail.com>
 */
class FV_Contest
{

        /**
         * Delete contest and all photos from it
         *
         */
        public function delete($contest_id)
        {
            $my_db = new FV_DB;
            if ($contest_id >= 0) {
                if ($my_db->deleteContest($contest_id)) {
                    $my_db->deleteCompItems($contest_id);
                    $my_db->clearVoteStats($contest_id);
                }
            }
        }

        /**
         * Save the contest
         *
         * @return int $contest_id
         */
        public function save()
        {
            $my_db = new FV_DB;
            //$contestt_id = -1
            /*
             * We need to verify this came from the our screen and with proper authorization,
             * because save_post can be triggered at other times.
             */

            if (!isset($_POST['fv_edit_contest_nonce']) || !wp_verify_nonce($_POST['fv_edit_contest_nonce'], 'fv_edit_contest_action')) {
                die('nonce');
                return;
            }

            if ( !FvFunctions::curr_user_can() ) {
                return;
            }

            if (!isset($_POST['contest_title'])) {
                return;
            }

            $contest_options = array(
                'name' => sanitize_text_field($_POST['contest_title']),
                'date_start' => sanitize_text_field($_POST['date_start']),
                'date_finish' => sanitize_text_field($_POST['date_finish']),
                'upload_date_start' => sanitize_text_field($_POST['upload_date_start']),
                'upload_date_finish' => sanitize_text_field($_POST['upload_date_finish']),
                'soc_title' => sanitize_text_field($_POST['fv_social_title']),
                'soc_description' => sanitize_text_field($_POST['fv_social_descr']),
                'soc_picture' => sanitize_text_field($_POST['fv_social_photo']),
                'user_id' => get_current_user_id(),
                'upload_enable' => isset($_POST['fv_upload_enable']) ? 1 : 0,
                'security_type' => sanitize_text_field($_POST['fv_security_type']),
                'voting_frequency' => sanitize_text_field($_POST['fv_voting_frequency']),
                'max_uploads_per_user' => (int)$_POST['max_uploads_per_user'],
                'show_leaders' => isset($_POST['show_leaders']) ? 1 : 0,
                'lightbox_theme' => sanitize_text_field($_POST['lightbox_theme']),
                'upload_theme' => sanitize_text_field($_POST['upload_theme']),
                'timer' => sanitize_text_field($_POST['fv_timer']),
                'sorting' => (array_key_exists($_POST['sorting'], fv_get_sotring_types_arr())) ? $_POST['sorting'] : 'sorting',
                'moderation_type' => (in_array($_POST['moderation_type'], array('pre', 'after'))) ? $_POST['moderation_type'] : 'pre',
                'page_id' => !isset($_POST['fv_page_id']) ? 0 : (int)$_POST['fv_page_id'],
                'cover_image' => !isset($_POST['fv_cover_image']) ? '' : (int)$_POST['fv_cover_image'],
                'type' => !isset($_POST['type']) ? 0 : (int)$_POST['type'],
            );

            $contest_options = apply_filters('fv/before_save_contest', $contest_options);

            if ((int)$_POST['contest_id'] > 0) {
                $contest_id = (int)$_POST['contest_id'];
                ModelContest::query()->update($contest_options, array('id' => $contest_id));
            } else {
                $contest_id = ModelContest::query()->insert($contest_options);
            }

            $contest_options = apply_filters('fv/after_save_contest', $contest_options, $contest_id);

            wp_add_notice(__('Contest saved.', 'fv'), 'success');

            return $contest_id;
        }

        /**
         * AJAX :: Save the contestant
         *
         * @return void
         * @output json_array with form
         */
        public function save_contestant()
        {
            if ( !FvFunctions::curr_user_can() ) {
                return;
            }

            if (!isset($_POST['fv_nonce']) || !wp_verify_nonce($_POST['fv_nonce'], 'save_contestant')) {
                print 'Sorry, your nonce did not verify.';
                exit;
            }

            $contest_id = (int)$_POST['contest_id'];
            $FORM = $_POST['form'];

            if (isset($FORM['name'])) :

                $contest = ModelContest::query()->findByPK($contest_id);

                $data = array(
                    'id' => (is_numeric($FORM['id'])) ? intval($FORM['id']) : -1,
                    'name' => sanitize_text_field($FORM['name']),
                    'description' => !empty($FORM['description']) ? wp_kses_post($FORM['description']) : '',
                    'full_description' => !empty($FORM['full_description']) ? wp_kses_post($FORM['full_description']) : '',
                    'social_description' => !empty($FORM['social_description']) ? sanitize_text_field($FORM['social_description']) : '',
                    'additional' => ( !empty($FORM['additional']) )? sanitize_text_field($FORM['additional']) : '',
                    'url' => sanitize_text_field($FORM['image']),
                    'image_id' => (int)$FORM['image_id'],
                    'contest_id' => $contest->id,
                    'votes_count' => isset($FORM['description']) ? sanitize_text_field($FORM['votes']) : 0,
                    'status' => sanitize_text_field($FORM['status']),
                );
                /*, array(
                            'a' => array(
                                'href' => array(),
                                'title' => array()
                            ),
                            'br' => array(),
                            'em' => array(),
                            'strong' => array(),
                        )
                    )
                */
                //var_dump($data);

                if ($data['id'] > 0) {
                    $prev = ModelCompetitors::query()->findByPK($data['id']);
                }


                if ( !empty($prev) && $prev->options ) {
                    if ( is_array($prev->options) && isset($FORM['options']) && is_array($FORM['options']) ) {
                        $data['options'] = array_merge($prev->options, $FORM['options']);
                    } else {
                        $data['options'] = $prev->options;
                    }
                } elseif ( isset($FORM['options']) && is_array($FORM['options']) ) {
                    $data['options'] = $FORM['options'];
                }

                // Email оповещение
                if (!empty($prev) && $prev->status == ST_MODERAION && $data['status'] == ST_PUBLISHED && get_option('fotov-users-notify', false)) {
                    if (is_email($prev->user_email)) {
                        $public_translated_messages = fv_get_public_translation_messages();
                        $mail_body = sprintf($public_translated_messages['mail_approve_user_body'], $contest->name, $data["name"], $prev->user_email);
                        FvFunctions::notifyMailToUser($prev->user_email, $public_translated_messages['mail_approve_user_title'], $mail_body, $prev);
                    }
                }

                $response = array();

                $filters = apply_filters('fv/admin/save_photo', array(), $data, $contest);

                if ( isset($filters['photo']) ) {
                    $data = $filters['photo'];
                }
                if ( isset($filters['notify_message']) ) {
                    $response['notify'] = $filters['notify_message'];
                }

                if ( is_array($data['options']) ) {
                    $data['options'] = maybe_serialize( $data['options'] );
                }

                if ($data['id'] > 0) {
                    // Изменяем элемент
                    $response['add'] = false;
                    $r = ModelCompetitors::query()->update($data);
                } else {
                    // Создаем новый элемент
                    $response['add'] = true;
                    unset($data['id']);
                    $data['added_date'] = current_time('timestamp', 0);
                    $data['user_id'] = get_current_user_id();
                    $data['id'] = ModelCompetitors::query()->insert($data);
                }

                $unit = ModelCompetitors::query()->findByPK( (int)$data['id'] );
                $edit = true;
                ob_start();
                include FV::$ADMIN_PARTIALS_ROOT . '/_table_units_tr.php';
                $response['html'] = ob_get_clean();
                $response['id'] = $unit->id;
                die( fv_json_encode($response) );

            endif;
        }

        /**
         * Ajax :: FORM for Add or Edit photo information
         *
         * @POST-param int $constest_id
         * @POST-param int $constestant_id
         *
         * @return void
         * @output json_array with form
         */
        function form_contestant()
        {
            if ( !FvFunctions::curr_user_can() || !check_admin_referer('fv_nonce', 'fv_nonce') ) {
                return;
            }

            if (isset($_GET['contestant_id']) && intval($_GET['contestant_id']) > 0 && isset($_GET['contest_id']) && intval($_GET['contest_id']) > 0) {
                $unit = ModelCompetitors::query()->findByPK((int)$_GET['contestant_id']);
            } else {
                $unit = new Fv_Empty_Unit();
                $unit->contest_id = (int)$_GET['contest_id'];
            }

            ob_start();
            include FV::$ADMIN_PARTIALS_ROOT . '/_unit.php';
            $html = ob_get_clean();
            wp_die( fv_json_encode(array('html' => $html)) );
        }

        /**
         * Ajax :: approve photo
         *
         * @POST-param int $constestant_id
         *
         * @return void
         * @output json_array
         */
        function approve_constestant()
        {
            if ( !FvFunctions::curr_user_can() || !check_admin_referer('fv_nonce', 'fv_nonce') ) {
                return;
            }
            //$my_db = new FV_DB;

            // Check required param
            if (!isset($_REQUEST['constestant_id'])) {
                return;
            }
            $contestant_id = (int)$_REQUEST['constestant_id'];

            ModelCompetitors::query()->updateByPK(array('status'=>ST_PUBLISHED), $contestant_id);

            do_action('fv/approve_photo', $contestant_id);

            if (get_option('fotov-users-notify', false)) {
                $contestant = ModelCompetitors::query()->findByPK($contestant_id);
                if ( is_object($contestant) && is_email($contestant->user_email) ) {
                    $contest = ModelContest::query()->findByPK($contestant->contest_id);

                    $public_translated_messages = fv_get_public_translation_messages();
                    $photo_url = fv_generate_contestant_link($contest->id, get_permalink($contest->page_id), $contestant_id);

                    $mail_body = sprintf($public_translated_messages['mail_approve_user_body'], $contest->name, $contestant->name, $contestant->user_email, $photo_url);
                    FvFunctions::notifyMailToUser($contestant->user_email, $public_translated_messages['mail_approve_user_title'], $mail_body, $contestant);
                }
            }

            die( fv_json_encode(array('res' => 'approved')) ); // output
        }

        /**
         * Ajax :: delete photo and image from hosting
         *
         * @POST-param int $constest_id
         * @POST-param int $constestant_id
         *
         * @return void
         * @output json_array
         */
        function delete_constestant()
        {
            if ( !FvFunctions::curr_user_can() || !check_admin_referer('fv_nonce', 'fv_nonce') ) {
                return;
            }

            // Если пришёл параметр - очищаем результаты голосования
            if (!isset($_REQUEST['constestant_id']) || !isset($_REQUEST['constest_id'])) {
                return;
            }
            $id = (int)$_REQUEST['constestant_id'];
            $contest_id = (int)$_REQUEST['constest_id'];
            $contestant = ModelCompetitors::query()->findByPK($id);

            do_action('fv/delete_photo', $contestant);

            // To leave Contestant
            //ModelCompetitors::query()->update( array( 'status'=> ST_DRAFT ), $contestant->id );

            // delete Contestant + may be Image from hosting
            if ( $contestant && ModelCompetitors::query()->delete($id) && get_option('fv-image-delete-from-hosting', false) ) {
                // in not registered some hooks
                if ( has_action( 'fv/admin/delete_photo_attachment' ) === false ) {
                    wp_delete_attachment($contestant->image_id, true);
                } else {
                    do_action( 'fv/admin/delete_photo_attachment', $contestant );
                }
            }

            if (get_option('fotov-users-notify', false)) {
                if (is_object($contestant) && is_email($contestant->user_email)) {
                    $contest = ModelContest::query()->findByPK($contestant->contest_id);
                    $public_translated_messages = fv_get_public_translation_messages();
                    $mail_body = sprintf($public_translated_messages['mail_delete_user_body'], $contest->name, $contestant->name, $contestant->user_email);
                    FvFunctions::notifyMailToUser($contestant->user_email, $public_translated_messages['mail_delete_user_title'], $mail_body, $contestant);
                }
            }

            die( fv_json_encode(array('res' => 'deleted')) ); // output
        }

        /**
         * Return complete contest data
         *
         * @param int $contest_id
         * @return object Contest and all photos from it
         */
        public function get_contest($contest_id)
        {
            $my_db = new FV_DB;
            $contest_options = ModelContest::query()->findByPK($contest_id);

            $contest_options->items = ModelCompetitors::query()->where('contest_id', $contest_id)->find();

            return $contest_options;
        }

        /**
         * Clear all `Vote records` from table by contest_id
         *
         * @param int $contest_id
         * @return void
         */
        public function clear_contest_stats($contest_id)
        {
            if ( !FvFunctions::curr_user_can() ) {
                return;
            }
            $my_db = new FV_DB;

            // Если пришёл параметр - очищаем результаты голосования
            if (!isset($_REQUEST['contest_id'])) {
                FvLogger::addLog("clear_contest_stats error - no contest_id");
                return;
            }

            $my_db->clearVoteStats((int)$_REQUEST['contest_id']);
            die(fv_json_encode(array('res' => 'cleared'))); // output
        }

        /**
         * reset all votes to 0
         *
         * @param int $contest_id
         * @return void
         */
        public function reset_contest_votes($contest_id)
        {
            if ( !FvFunctions::curr_user_can() ) {
                return;
            }

            // Если пришёл параметр - очищаем результаты голосования
            if (!isset($_REQUEST['contest_id'])) {
                FvLogger::addLog("reset_contest_votes error - no contest_id");
                return;
            }

            ModelCompetitors::query()->update(
                array( 'votes_count'=>0 ),
                array( 'contest_id'=>(int)$_REQUEST['contest_id'] )
            );

            die(fv_json_encode(array('res' => 'ok'))); // output
        }

}
