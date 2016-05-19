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
class FV_Public_Ajax {

    /**
     * Ajax :: Upload photo to contest
     *
     * Also called non AJAX from class-fv-public.php
     * (need, if visitor browser not support javascript)
     *
     * @param object $contest
     *
     * @return void
     * @output json_array
     */
    public static function upload_photo($contest = NULL)
    {
        if ( empty($_POST) ) {
            return false;
        }
        global $post;
        $fields_structure = Fv_Form_Helper::get_form_structure();
        $my_db = new FV_DB;

        if (empty($contest)) {
            $post_id = (int)$_REQUEST['post_id'];
            $contest_id = (int)$_REQUEST['contest_id'];
            $contest = $my_db->getContest($contest_id);
        } else {
            $post_id = $post->ID;
        }

        try {

            if ($contest->max_uploads_per_user == NULL) {
                $contest->max_uploads_per_user = 5;
            }

            $limit_exceeded = false;
            $public_translated_messages = fv_get_public_translation_messages();
            $public_translated_messages_def = fv_get_default_public_translation_messages();
            // есои есть сессия, значит фото уже загружалось
            //if (isset($_SESSION['foto-upload-'.$post->ID]) && $_SESSION['foto-upload-'.$post->ID] > $contest->max_uploads_per_user) {
            //  $limit_exceeded = true;
            //}

            //var_dump( apply_filters('fv/public/can_upload', false) );
            //var_dump( $_FILES );

            //var_dump($_POST);

            if ( isset($_POST['go-upload']) && isset($_POST['form']) ) {

                $limit_exceeded = false;
                $err = array();

                // Check is at least first file is passed and any Filters not applied (like for Video contest)
                if ( $_FILES['foto-async-upload']['size'] == 0 && !apply_filters('fv/public/can_upload', false) ) {
                    $err['custom_upload_error'] = $public_translated_messages['download_no_image'];
                }

                // if first file not empty
                IF ( empty($err) ):
                    if (defined('DOING_AJAX') && DOING_AJAX && get_option('fotov-upload-limit-cookie', false)) {
                        if (session_id() == '') {
                            session_start();
                        }
                        if (!isset($_SESSION['foto-upload-' . $contest_id])) {
                            // статус - загрузка
                            $_SESSION['foto-upload-' . $contest_id] = 1;
                        } elseif ($_SESSION['foto-upload-' . $contest_id] >= 1 && $_SESSION['foto-upload-' . $contest_id] < $contest->max_uploads_per_user) {
                            // статус - загружена
                            //$_SESSION['foto-upload'] = 2;
                            $_SESSION['foto-upload-' . $contest_id] = (int)$_SESSION['foto-upload-' . $contest_id] + 1;
                        } elseif ($_SESSION['foto-upload-' . $contest_id] >= $contest->max_uploads_per_user) {
                            // есои есть сессия, значит фото уже загружалось
                            $limit_exceeded = true;
                            //$err['custom_upload_error'] = __('Cookie', 'fv') . ' / ' . $_SESSION['foto-upload-'.$contest_id];
                        }
                        //var_dump(isset($_SESSION['foto-upload-'.$contest_id]));
                        //var_dump($_SESSION['foto-upload-'.$contest_id]);
                    }

                    //      check photo size
                    if ( !empty($_FILES) && get_option('fotov-upload-photo-limit-size', 0) > 0 && $_FILES["foto-async-upload"]["size"] > (int) get_option('fotov-upload-photo-limit-size', 0) * 1024 ) {
                        if ( $public_translated_messages["download_limit_size"] ) {
                            $err['custom_upload_error'] = str_replace("%LIMIT_SIZE%", get_option('fotov-upload-photo-limit-size') / 1024, $public_translated_messages["download_limit_size"] );
                        }else {
                            $err['custom_upload_error'] = str_replace("%LIMIT_SIZE%", get_option('fotov-upload-photo-limit-size') / 1024, $public_translated_messages_def["download_limit_size"] );
                        }
                    }

                    // Checks user ip
                    if (get_option('fotov-upload-limit-ip', false)) {
                        $ip = fv_get_user_ip();
                        //var_dump($my_db->getCompItemsCountByIp($contest_id, $ip));
                        $uploadedByIp = ModelCompetitors::query()->where_all( array('contest_id' => $contest_id, 'user_ip' => $ip) )->find(true);
                        if ( $uploadedByIp >= $contest->max_uploads_per_user ) {
                            $limit_exceeded = true;
                            //$err['custom_upload_error'] = __('IP', 'fv') . ' / ' . $my_db->getCompItemsCountByIp($contest_id, $ip);
                        }
                    }

                    // Checks user id
                    if (get_option('fotov-upload-limit-userid', false) && get_current_user_id() > 0) {
                        $uploadedById = ModelCompetitors::query()->where_all( array('contest_id' => $contest_id, 'user_id' => get_current_user_id()) )->find(true);
                        if ( $uploadedById >= $contest->max_uploads_per_user ) {
                            $err['custom_upload_error'] = __('Upload limit exceeded', 'fv');
                            $limit_exceeded = true;
                        }
                    }

                    //==================================
                    // GET photo data from $_POST
                    $new_photo = Fv_Form_Helper::_get_photo_data_from_POST( $_POST['form'], Fv_Form_Helper::get_form_structure_obj() );
                    //==================================

                    // Checks entered email
                    if ( get_option('fotov-upload-limit-email', true) && !empty($new_photo['user_email']) ) {
                        //if email vaild find in bd
                        if ( is_email( $new_photo['user_email'] ) ) {
                            $uploadedByEmail = ModelCompetitors::query()->where_all( array('contest_id' => $contest_id, 'user_email' => $new_photo['user_email']) )->find(true);
                            if ( $uploadedByEmail >= $contest->max_uploads_per_user ) {
                                $limit_exceeded = true;
                            }
                            // else shows error
                        } else {
                            $err['custom_upload_error'] = __('Enter valid email!', 'fv');
                            $limit_exceeded = true;
                        }
                    }

                ENDIF;  // END Checking is empty $err

                $inserted_photo_id = false;

                if (!$limit_exceeded && !isset($err['custom_upload_error'])) {

                    // scale image size
                    if (  get_option('fotov-upload-photo-resize', false)  ) {
                        add_action('wp_handle_upload', 'fv_upload_resize'); // apply our modifications
                    }

                    $new_photo = array_merge($new_photo,
                        array(
                            //'additional' => ( isset($upload_info['comment']) ) ? $upload_info['comment'] : "",
                            'contest_id' => $contest->id,
                            'votes_count' => 0,
                            'user_id' => get_current_user_id(),
                            'user_ip' => fv_get_user_ip(),
                            'added_date' => current_time('timestamp', 0),
                        )
                    );

                    // статус - на модерации / опубликован
                    if ( FvFunctions::curr_user_can() || $contest->moderation_type == "after" ) {
                        $new_photo['status'] = ST_PUBLISHED;
                    } else {
                        $new_photo['status'] = ST_MODERAION;
                    }

                    // log
                    //FvLogger::addLog('$new_photo', $new_photo);

                    //FvFunctions::getPhotoThumbnail($unit, 'full');
                    //$image_min = wp_get_attachment_image_src($image_id, get_option('fotov-image-size', 'thumbnail'));

                    $notify_sent = false;
                    // save $_FILES, because function `media_handle_upload` reset array

                    //* Check, if exists custom upload functions, else run Default
                    if ( apply_filters( 'fv/public/custom_upload/uses', false, $contest ) === false ) {
                        require_once(ABSPATH . 'wp-admin/includes/admin.php');

                        if ( FvFunctions::ss('upload-custom-folder', false) ) {
                            // Change Upload dir
                            add_filter('upload_dir', array('FV_Public_Ajax', 'filter_upload_dir'));
                        }
                        // Get all File inputs (NEED for support Multiply File inputs)
                        FOREACH (Fv_Form_Helper::_get_file_inputs() as $INPUT_NAME => $INPUT_params) :

                            if ( !isset($_FILES[$INPUT_NAME]) || empty($_FILES[$INPUT_NAME]['name']) || $_FILES[$INPUT_NAME]['size'] == 0 ) {
                                continue;
                            }
                            do_action('fv/public/before_upload', $INPUT_NAME);

                            $new_photo_data = $new_photo;

                            $image_id = media_handle_upload(
                                $INPUT_NAME,
                                $post_id,
                                array(),
                                apply_filters( 'fv/public/upload/media_handle_upload_overrides', array('test_form'=>false), $new_photo_data)
                            ); //post id of Client Files page

                            do_action('fv/public/after_upload', $INPUT_NAME);
                            //var_dump(is_wp_error($image_id));
                            if (is_wp_error($image_id)) {
                                $err['upload_error'] = $image_id;
                                $image_id = false;
                                FvLogger::addLog('image upload error ', '', __FILE__, __LINE__);
                            } else {
                                $image = wp_get_attachment_image_src($image_id, 'full');
                                $new_photo_data['url'] = $image[0];
                                $new_photo_data['image_id'] = $image_id;

                                // if enables showing Photo name around each File input
                                if ( !empty($INPUT_params['photo_name_input']) && !empty($_POST[$INPUT_NAME.'-name']) )
                                {
                                    $new_photo_data['name'] = sanitize_text_field($_POST[$INPUT_NAME.'-name']);
                                }

                                $inserted_photo_id = self::_upload_add_photo_to_db($new_photo_data, $INPUT_NAME);
                            }
                        ENDFOREACH;

                        if ( FvFunctions::ss('upload-custom-folder', false) ) {
                            remove_filter('upload_dir', array('FV_Public_Ajax', 'filter_upload_dir'));
                        }

                    } else {
                        $custom_upload_result = apply_filters('fv/public/custom_upload/run', array(), $new_photo, $contest);
                        //FvLogger::addLog('$custom_upload_result', $custom_upload_result);
                        if ( isset($custom_upload_result['custom_upload_error']) ) {
                            $err['custom_upload_error'] = $custom_upload_result['custom_upload_error'];
                            FvLogger::addLog('custom_upload_error ', $custom_upload_result['custom_upload_error'], __FILE__, __LINE__);
                        } elseif( isset($custom_upload_result['new_photo']) ) {
                            $new_photo = $custom_upload_result['new_photo'];

                            $inserted_photo_id = self::_upload_add_photo_to_db($new_photo, 'video');
                        } else {
                            $err['custom_upload_error'] = 'Unknown upload error!';
                        }
                    }

                    //** IF there not problems
                    IF ( count($err) == 0 ):
                        $public_translated_messages = apply_filters('fv/public/upload_after_save', $public_translated_messages, $new_photo, $inserted_photo_id);

                        // Sent Notify Messages to Admin and User
                        if ( !$notify_sent ) {
                            self::_upload_sent_notify($contest, $new_photo, $inserted_photo_id, $post_id, $public_translated_messages);
                            $notify_sent = true;
                        }
                    ENDIF;
                    // reset
                    $_FILES = array();
                } else {
                    $err['uploaded'] = 1;
                }

                $status = "error";
                if (isset($err['custom_upload_error'])) {
                    $output = "<div class='fv-box fv_error'> " . $err['custom_upload_error'] . "</div>";
                } elseif (isset($err['upload_error'])) {
                    $output = "<div class='fv-box fv_error'> " . $public_translated_messages['download_error'] . $err['upload_error']->errors['upload_error'][0] . "</div>";
                } elseif (isset($err['uploaded'])) {
                    $output = "<div class='fv-box fv_warning'>" . $public_translated_messages['download_limit'] . " </div>";
                } else {
                    $status = "ok";
                    if ( !FvFunctions::curr_user_can() ) {
                        $output = "<div class='fv-box fv_success'>" . $public_translated_messages['download_ok'] . "</div>";
                    } else {
                        $output = "<div class='fv-box fv_success'>" . $public_translated_messages['download_admin'] . "</div>";
                    }
                }

                if (defined('DOING_AJAX') && DOING_AJAX) {
                    /* it's an AJAX call */
                    die(fv_json_encode(array('data' => $output, "status"=>$status, 'inserted_photo_id'=>$inserted_photo_id)));
                } else {
                    echo $output;
                }
            }
            // END UPLOAD
        } catch (Exception $e) {
            FvLogger::addLog('image upload error ', $e->getMessage());
        }
    }

    /**
     * Helper function, that adds new record to Database
     *
     * @param array
     * @param string
     *
     * @return int
     */
    private static function _upload_add_photo_to_db($photo_data_array, $INPUT_NAME)
    {
        $photo_data_array = apply_filters('fv/public/upload_before_save', $photo_data_array, $INPUT_NAME);

        if ( isset($photo_data_array['options']) && is_array($photo_data_array['options']) ) {
            $photo_data_array['options'] = maybe_serialize($photo_data_array['options']);
        }

        $insert_res = ModelCompetitors::query()->insert($photo_data_array);

        if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_UPLOAD ) {
            fv_log('_upload_add_photo_to_db', $photo_data_array, __FILE__, __LINE__);
            fv_log('_upload_add_photo_to_db INSERT result', $insert_res);
        }

        if ( $insert_res == 0) {
            fv_log('_upload_add_photo_to_db :: something wrong, result is 0!', $photo_data_array, __FILE__, __LINE__);
        }

        return $insert_res;
    }

    /**
     * @param object $contest
     * @param array $new_photo_data
     * @param int $inserted_photo_id
     * @param int $post_id
     * @param array $public_translated_messages
     *
     * @return void
     */
    private static function _upload_sent_notify($contest, $new_photo_data, $inserted_photo_id, $post_id, $public_translated_messages)
    {
        // Admin upload Notify
        if (get_option('fotov-upload-notify', false)) {
            if (get_option('fotov-upload-notify-email', false)) {
                $notify_email = get_option('fotov-upload-notify-email');
            } else {
                $notify_email = get_option('admin_email');
            }
            if ( is_email($notify_email) ) {
                $photo_url = fv_generate_contestant_link( $contest->id, get_permalink( $post_id ) ,$inserted_photo_id );
                $mail_body = sprintf($public_translated_messages['mail_upload_admin_body'], $contest->name, $new_photo_data["name"], $new_photo_data["user_email"], $photo_url);
                FvFunctions::notifyMailToUser($notify_email, $public_translated_messages['mail_upload_admin_title'], $mail_body);
            }
        }

        // User upload Notify
        if ( get_option('fotov-users-notify-upload', false) ) {
            if ( !empty($new_photo_data['user_email']) && is_email($new_photo_data['user_email']) ) {
                $photo_url = urldecode( fv_generate_contestant_link( get_permalink( $post_id ), $contest->id, $inserted_photo_id ) );
                $mail_body = sprintf($public_translated_messages['mail_upload_user_body'], $contest->name, $new_photo_data["name"], $new_photo_data["user_email"], $photo_url);
                FvFunctions::notifyMailToUser($new_photo_data['user_email'], $public_translated_messages['mail_upload_user_title'], $mail_body);
            }
        }
    }


    /**
     * Change WP upload dir to custom
     * @param array $path_data
     */
    public static function filter_upload_dir($path_data)
    {
        if (!empty($path_data['error'])) {
            return $path_data; //error or uploading
        }

        //remove default subdir (year/month)
        $path_data['path'] = str_replace($path_data['subdir'], '', $path_data['path']);
        $path_data['url'] = str_replace($path_data['subdir'], '', $path_data['url']);

        $path_data['subdir'] = '/fv-contest';
        $path_data['path'] .= '/fv-contest';
        $path_data['url'] .= '/fv-contest';

        return $path_data;
    }

// END FUNCTION @upload_photo@

    public static function ajax_get_votes_counts() {
        //var_dump($_POST['ids']);
        if ( isset($_POST['ids']) && is_array($_POST['ids']) ) {
            $ids = array();
            foreach($_POST['ids'] as $id) {
                $ids[] = (int)$id;
            }
            $photos = ModelCompetitors::query()->what_field(" `t`.`id`, `t`.`votes_count` ")->where_in('id',$_POST['ids'])->find();
            $photosVotes = array();
            foreach($photos as $photo) {
                $photosVotes[$photo->id] = (int)$photo->votes_count;
            }
            die( fv_json_encode(array('res'=>'ok', 'votes'=>$photosVotes)) );
        }
        die( fv_json_encode(array('res'=>'error')) );
    }

    public static function ajax_go_to_page () {
        if ( !defined('WP_CACHE') || WP_CACHE == FALSE ) {
            // not allow direct actions
            check_ajax_referer('fv-ajax', 'some_str');
        }

        if ( FvFunctions::ss('pagination-type', 'default') == 'default' ) {
            die(fv_json_encode( array('result'=>'fail', 'mgs'=>'ajax pagination disabled!') ));
        }

        if ( isset($_GET['contest_id']) ) {
            $contest_id = (int)$_GET['contest_id'];
        } else {
            die(fv_json_encode( array('result'=>'fail', 'mgs'=>'wrong contest_id!') ));
        }
        $post_id= (int)$_GET['post_id'];
        $paged = ( isset($_GET['fv-page']) ) ? (int)$_GET['fv-page'] : 1;

        $plugin_public = new FV_Public(FV::NAME, FV::VERSION);
        global $photos;
        add_filter( 'fv_shows_get_comp_items', array('FV_Public_Ajax','hook_fv_shows_get_comp_items'), 100, 1 );
        ob_start();
            $plugin_public->show_contest(array('id'=>$contest_id), true);
        $photos_list_html = ob_get_clean();
        //if ( is_array($photos) ) {
            die(fv_json_encode(
                array(
                    'result'        =>  'ok',
                    'html'          =>  $photos_list_html,
                    'photos_data'   =>  $photos,
                    'share_page_url'=>  fv_generate_contestant_link( $contest_id, get_permalink($post_id) ),
                )
            ));
        /*} else {
            die(fv_json_encode( array('result'=>'fail', 'mgs'=>'wrong $photos!') ));
        }*/
    }

    public static function hook_fv_shows_get_comp_items ($photos_arr) {
        global $photos;
        $photos = $photos_arr;
        return $photos_arr;
    }
}
