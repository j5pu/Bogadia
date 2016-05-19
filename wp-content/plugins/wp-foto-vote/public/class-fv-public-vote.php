<?php

/**
 * The public-facing Voting functionality of the plugin.
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
class FV_Public_Vote {

    public static $vote_debug_var;
    /**
     * Ajax :: Vote for photo
     *
     * @GET-param 'some_str'    WP nonce
     * @GET-param 'user_country' saved in User browser, for decrease queries count to indicate user country by IP
     * @GET-param 'post_id'     post Id
     * @GET-param 'contest_id'
     * @GET-param 'uid'         evercookie identification
     * @GET-param 'fv_name'     if uses contest security `default and Subscribe`
     * @GET-param 'fv_email'    if uses contest security `default and Subscribe`
     * @GET-param 'referer'     from what page user open contest page?
     *
     * @return void
     * @output json_array
     */
    public static function vote()
    {
        // not allow direct voting
        if ( !defined('WP_CACHE') || WP_CACHE == FALSE ) {
            // Invalid WP security token
            if ( false == check_ajax_referer('fv_vote', 'some_str', false) ) {
                self::echoVoteRes(98, '', false);     // Wrong WP security token
            }
        }
        //session_start();
        $my_db = new FV_DB;

        $ip = substr( fv_get_user_ip(), 0, 15 );
        $user_country = '';
        if (isset($_COOKIE['user_country'])) {
            $user_country = sanitize_text_field($_COOKIE['user_country']);
        }

        $post_id = (int)$_POST['post_id'];
        if ( $post_id > 99999999 ) {
            $post_id = 1;
        }
        $contest_id = (int)$_POST['contest_id'];
        $vote_id = (int)$_POST['vote_id'];
        $UID = sanitize_text_field($_POST['uid']);
        if ( strlen($UID) > 22 ) {
            $UID = substr($UID, 0 , 22);
        }

        $name = (isset($_POST['fv_name'])) ? sanitize_text_field($_POST['fv_name']) : '';
        $email = (isset($_POST['fv_email'])) ? sanitize_email($_POST['fv_email']) : '';


        /*
                $name = '';
                if (isset($_POST['fv_name'])) {
                    $name = sanitize_text_field($_POST['fv_name']);
                    $_SESSION['fv_name'] = $name;
                } elseif ( isset( $_SESSION['fv_name'] ) ) {
                    $name = $_SESSION['fv_name'];
                }

                $email = '';
                if (isset($_POST['fv_email'])) {
                    $email = sanitize_text_field($_POST['fv_email']);
                    $_SESSION['fv_email'] = $name;
                } elseif ( isset( $_SESSION['fv_email'] ) ) {
                    $email = $_SESSION['fv_email'];
                }
        */
        $CHECK = (isset($_POST['check'])) ? true : false;        // not vote, only check, can user vote?
        // откуда пришёл голосующий
        $referer = sanitize_text_field(stripcslashes($_POST['referer']));

        $contest = ModelContest::query()->findByPK($contest_id);

        $user_id = get_current_user_id();
        // allow addons change security_type
        $contest->security_type = apply_filters(FV::PREFIX . 'vote_contest_security_type', $contest->security_type, $user_id);

        // Detect, need Subscription or not
        if ( in_array($contest->security_type, array("cookieAsocial", "defaultAsocial", "defaultAsubscr") )  )
        {
            if (session_id() == '') {
                session_start();
            }
            $add_subscription = true;
        } else {
            $add_subscription = false;
        }

        //** Verify ReCaptcha Response
        /*if ( $contest->security_type == "defaultArecaptcha" || $contest->security_type == "cookieArecaptcha" ) {
            if ( isset($_POST['recaptcha_response']) ) {
                $recaptcha_verify = FvFunctions::recaptcha_verify_response($_POST['recaptcha_response'], $ip, FvFunctions::ss('recaptcha-secret-key') );
                if ( $recaptcha_verify == false ) {
                    self::echoVoteRes(6, $user_country, $add_subscription);  // wrong reCAPTCHA
                }
            } else {
                fv_log('Vote error - recaptcha_response is empty!', $_POST, __FILE__, __LINE__);
                self::echoVoteRes(99, $user_country, $add_subscription);     // error
            }
        }*/
        //** Verify ReCaptcha Response
        if ( $contest->security_type == "defaultArecaptcha" || $contest->security_type == "cookieArecaptcha" ) {
            $check_recaptcha_response = true;
            // if if enabled solve reCAPTCHA once in 30 minutes and have Session then Check it
            if ( FvFunctions::ss('recaptcha-session', false) ) :
                if ( isset($_SESSION['fv_recaptcha_session']) && (time() - $_SESSION['fv_recaptcha_session']) < 1800 ) {
                    $check_recaptcha_response = false;
                } elseif( !isset($_SESSION['fv_recaptcha_session']) || (time() - $_SESSION['fv_recaptcha_session']) >= 1800 ) {
                    unset($_SESSION['fv_recaptcha_session']);
                }
            ENDIF;

            if ( $check_recaptcha_response ) :
                if ( isset( $_POST['recaptcha_response']) ) {
                    $recaptcha_verify = FvFunctions::recaptcha_verify_response( $_POST['recaptcha_response'], $ip, FvFunctions::ss('recaptcha-secret-key') );

                    if ( $recaptcha_verify === 'error' ) {
                        self::echoVoteRes(99, $user_country, $add_subscription);     // error
                    } elseif ( $recaptcha_verify == false ) {
                        self::echoVoteRes(6, $user_country, $add_subscription);  // wrong reCAPTCHA
                    } elseif ( FvFunctions::ss('recaptcha-session', false) ) {
                        // Save session if enabled solve reCAPTCHA once in 30 minutes
                        $_SESSION['fv_recaptcha_session'] = time();
                    }
                } elseif ( FvFunctions::ss('recaptcha-session', false) ) {
                    // if if enabled solve reCAPTCHA, but no have session
                    self::echoVoteRes(66, $user_country, $add_subscription);  // need  reCAPTCHA
                } else {
                    fv_log('Vote error - recaptcha_response is empty!', $_POST, __FILE__, __LINE__);
                    self::echoVoteRes(99, $user_country, $add_subscription);     // error
                }
            ENDIF;
        }

        $add_subscription = apply_filters('fv_vote_contest_add_subscription', $add_subscription, $contest->security_type, $user_id);

        if ( $user_id == 0 && $contest->security_type == "cookieAregistered" ) {
            die(fv_json_encode(array('res' => '5', 'user_country' => $user_country, 'add_subscription' => $add_subscription)));
        }

        // Дата страта и окончания
        $time_now = current_time( 'timestamp', 0 );

        /* Защита */
        $cookies = false;
        $cookie_name = '';

        // имя куки, разное при разных типах голосования
        if ( strpos($contest->voting_frequency, 'once') !== false ) {
            $cookie_name = 'vote_post_' . $post_id;
        } else {
            $cookie_name = 'vote_post_' . $post_id . '_' . $vote_id;
        }

        // check dates
        if (!($time_now > strtotime($contest->date_start) && $time_now < strtotime($contest->date_finish))) {
            die(fv_json_encode(array('res' => '4', 'user_country' => $user_country, 'add_subscription' => $add_subscription))); // konkurs and or not start
        }
        // конец даты (end dates)
        $can_vote = false;

        $ip_data = array(
            'ip' => $ip,
            'uid' => $UID,
            'changed' => current_time( 'mysql', 0 ),
            'vote_id' => $vote_id,
            'contest_id' => $contest_id,
            'post_id' => $post_id,
            'browser' => substr($_SERVER['HTTP_USER_AGENT'], 0, 250),
            'display_size' => substr(sanitize_text_field($_POST['ds']), 0, 49),
            'referer' => substr($referer, 0, 250),
            'country' => substr($user_country, 0, 30),
            'name' => substr($name, 0, 49),
            'email' => substr($email, 0, 59),
            'user_id' => $user_id,
        );
        // Check plugins
        if ( !empty($_POST['pp']) ) {
            $ip_data['b_plugins'] = (int)$_POST['pp'];
        }

        $social_condition = false;
        if ( isset($_SESSION['fv_social']) && is_array($_SESSION['fv_social']) ) {
            $ip_data["email"] = substr($_SESSION['fv_social']["email"], 0, 49);
            $ip_data["name"] = substr($_SESSION['fv_social']["soc_name"], 0, 59);
            $ip_data["soc_profile"] = substr($_SESSION['fv_social']["soc_profile"], 0, 249);
            $ip_data["soc_network"] = substr($_SESSION['fv_social']["soc_network"], 0, 49);
            $ip_data["soc_uid"] = substr($_SESSION['fv_social']["soc_network"].$_SESSION['fv_social']["soc_uid"], 0, 49);
            $social_condition = array(
                "soc_uid" => $ip_data["soc_uid"],
            );
            //FvFunctions::dump($ip_data);
        }

        if ( $contest->security_type == "cookieAsocial" && !is_array($social_condition) ) {
            self::echoVoteRes(99, $user_country, $add_subscription); // Error
        }

        if ( $contest->security_type == "defaultAfb" && !$CHECK ) {
            if ( isset($_POST['fb_post_id']) ) {
                $fb_pid = explode("_", $_POST['fb_post_id']);
                if ( count($fb_pid) ) {
                    $ip_data["fb_pid"] = $fb_pid[0];
                } else {
                    self::echoVoteRes(99, $user_country, $add_subscription);  // incorrect facebook share post id
                }
            } else {
                self::echoVoteRes(99, $user_country, $add_subscription); // did't find facebook share post id
            }
        }

        $ip_data = apply_filters('fv/vote/ip_data', $ip_data, $contest);

        $NEED_check_ip_query = true;
        $check_ip_query = false;
        $check_ip_query_count = false;

        // ============= CHECK if not empty $UID ::START =============
        if ( empty($UID) || strpos($UID, '500 Internal') !== false ) {
            if ($contest->security_type == "cookieArecaptcha") {
                // Disable QUERY if not have $UID, else will have many records with empty $UID not related with this user
                $NEED_check_ip_query = false;
            } else {
                $UID = 'empty_UID';
            }
        }
        // ============= CHECK if not empty $UID ::END =============

        // ============= if need QUERY to check user :: START =============
        $NEED_check_ip_query = apply_filters('fv/vote/need_check_ip_query', $NEED_check_ip_query);
        IF ( $NEED_check_ip_query === TRUE ) :

            $check_ip_query = ModelVotes::query()->where( "contest_id", $contest->id );

            // Complete query according to Contest Security Type
            if ( $contest->security_type == "defaultAsocial" && is_array($social_condition) ) {
                $check_ip_query->where_any( array("ip"=>$ip,"uid" => $UID, $social_condition) );
            } elseif ( $contest->security_type == "cookieAsocial" && is_array($social_condition) ) {
                $check_ip_query->where_any( array("uid" => $UID, $social_condition ) );
            } else if ( $contest->security_type == "cookieAregistered" ) {
                $check_ip_query->where_any( array("uid" => $UID, "user_id" => $user_id) );
            } else if ( $contest->security_type == "defaultAfb" && !$CHECK ) {
                $check_ip_query->where_any( array("ip"=>$ip, "uid" => $UID, "fb_pid" => $ip_data["fb_pid"]) );
            } else if ( $contest->security_type == "cookieArecaptcha" ) {
                $check_ip_query->where("uid", $UID);
            } else {
                $check_ip_query->where_any( array("ip"=>$ip, "uid" => $UID) );
            }

            // Check votes count fot this photo
            $check_ip_query_count = '';
            // Complete query according to Contest Voting Frequency
            switch($contest->voting_frequency) {
                case ("once"):
                    $check_ip_query->where_later( "changed", strtotime($contest->date_start) );
                    break;
                case ("onceF2"):
                case ("onceF3"):
                case ("onceF10"):
                    $check_ip_query->where_later( "changed", strtotime($contest->date_start) );
                    $check_ip_query_count = clone $check_ip_query;
                    $check_ip_query_count->where( "vote_id", $vote_id );
                    break;
                case ("onceFall"):
                    $check_ip_query->where_later( "changed", strtotime($contest->date_start) )
                        ->where( "vote_id", $vote_id  );
                    break;
                case ("24hFonce"):
                    $check_ip_query->where_later( "changed", current_time('timestamp', 0) - 86400 );
                    break;
                case ("24hF2"):
                case ("24hF3"):
                    $check_ip_query->where_later( "changed", current_time('timestamp', 0) - 86400 );
                    $check_ip_query_count = clone $check_ip_query;
                    $check_ip_query_count->where( "vote_id", $vote_id );
                    break;
                case ("24hFall"):
                    $check_ip_query->where_later( "changed", current_time('timestamp', 0) - 86400 )
                        ->where( "vote_id", $vote_id  );
                    break;
                default:
                    break;
            }

            // Apply filter to query
            $check_ip_query = apply_filters('fv/vote/check_ip_query', $check_ip_query,
                $contest, $vote_id, $ip, $UID, $ip_data);        // Apply filter to query

            $check_ip_query_count = apply_filters('fv/vote/check_ip_query_count', $check_ip_query_count, $check_ip_query,
                $contest, $vote_id, $ip, $UID, $ip_data);

            $check_ip = $check_ip_query->find();
            // Apply filter to query results
            $check_ip = apply_filters(FV::PREFIX . 'vote_check_ip', $check_ip,
                $contest, $user_id, $ip, $UID, $ip_data);

            if ( is_object($check_ip_query_count) ) {
                $check_ip_count = $check_ip_query_count->find();
                //var_dump($check_ip_count);
            }

        ENDIF;  // ============= :: END =============

        // проверяем куку
        if (  !isset($_COOKIE[$cookie_name])  ) {
            if ( !$CHECK ) :
                // если частота голосования - 1 раз
                if ( strpos($contest->voting_frequency, 'once') !== false ){
                    // ставим куку по дате окончания голосования
                    setcookie($cookie_name, strtotime($contest->date_finish), strtotime($contest->date_finish));
                }
            endif;
        } else {
            // if vote frequency - once in 24 hours and exists cookie, and it is correct ( it value later then current WP site time )
            // uses for prevent problems with voting frequency
            if ( strpos($contest->voting_frequency, '24h') !== false && $_COOKIE[$cookie_name] > current_time('timestamp', 0) ) {
                // cookie exists, and is exists database records?
                if ( (is_array($check_ip) && count($check_ip) > 0) OR !$NEED_check_ip_query ) {
                    $cookies = true;
                }
            }
        }

        // Apply filter to query results
        $cookies = apply_filters(FV::PREFIX . 'vote_check_cookies', $cookies,
            $contest->security_type, $user_id, $ip, $UID, $ip_data);


        $hours_leave = false;  // set default value
        // if voting frequency - once in 24 hours, math in how many hours user can vote
        if ( strpos($contest->voting_frequency, '24h') !== false && is_array($check_ip) && count($check_ip) > 0 ) {
            $secs_leave = (strtotime($check_ip[count($check_ip)-1]->changed, current_time( 'timestamp', 0 )) + 86400) - current_time( 'timestamp', 0 );
            $hours_leave = intval( $secs_leave / 3600 );

            // if voting frequency - once in 24 hours, set correct cookie related to last vote date
            if ( !isset($_COOKIE[$cookie_name]) ){
                // set cookie as Last vote Timestamp + 24 hours
                $canVoteIn = strtotime($check_ip[count($check_ip)-1]->changed) + 86400;
                setcookie($cookie_name, $canVoteIn, $canVoteIn);
            }

        }
        /*
                    if ( (FV::$DEBUG_MODE & FvDebug::$LVL_ALL) || fv_is_lc()  ) {
                        //var_dump($check_ip_query);
                        var_dump($check_ip);

                        var_dump($cookies);

                        echo "current_time mysql = " . current_time( 'mysql', 0 ) . PHP_EOL;
                        echo "secs_passed = " . $secs_leave . PHP_EOL ;
                        echo "hours_passed = " .  $hours_leave . PHP_EOL;
                        var_dump( date( "d-m-Y H:i:s", time() ) );

                    }
        */

        if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_VOTE ) {
            // Save Voter data, and later may be log it in `self::echoVoteRes`
            self::$vote_debug_var = $ip_data;
            self::$vote_debug_var['has_cookie'] = $cookies;
            self::$vote_debug_var['cookie_name'] = $cookie_name;
            self::$vote_debug_var['check_ip_query'] = $check_ip;
        }

        // If not vote, or vote for other photo
        if (!$check_ip && !$cookies) {
            // User can vote
            $can_vote = true;
            // Add subscribed cookie, if need
            if (!isset($_COOKIE['fv_subscribed_' . $contest->id]) && $add_subscription) {
                setcookie('fv_subscribed_' . $contest->id, $post_id, strtotime($contest->date_finish));
            }
        } elseif (is_array($check_ip) && !$cookies) {
            if ( !isset($check_ip_count) ) {
                $check_ip_count = '';
            }

            if ( has_filter('fv/vote/process_custom_frequency') ) {
                $can_vote = apply_filters('fv/vote/process_custom_frequency', $can_vote, $contest, $check_ip, $check_ip_count, $user_country, $add_subscription, $hours_leave);
            } else {
                $code = self::_get_vote_resp_code($contest, $check_ip, $check_ip_count);
                if ( $code !== TRUE ) {
                    self::echoVoteRes($code, $user_country, $add_subscription, $hours_leave); // echo response
                }
                $can_vote = true;
            }

        } else {
            if ( strpos($contest->voting_frequency, 'once') !== false && strpos($contest->voting_frequency, '24h') === false ){
                self::echoVoteRes(2, $user_country, $add_subscription); // user was voted
            } else {
                self::echoVoteRes(3, $user_country, $add_subscription, $hours_leave); // 24 hour not passed
            }

        }
        if ( $can_vote && $CHECK ) {
            self::echoVoteRes("can_vote", $user_country, $add_subscription); // can_vote
        }

        if ($can_vote) {
            if ( FvFunctions::ss('anti-fraud', false) ) {
                $ip_data = FvFunctions::getSpamScore($ip_data, $contest);
            } else {
                $ip_data['score'] = '-1';
            }

            if ( empty($user_country) ) {
                $user_country = fv_get_user_country($ip);
            }

            // try insert record
            $insert_res = ModelVotes::query()->insert($ip_data);
            if ( $insert_res == 0 ) {
                fv_log('Voting :: can`t add new ip record to DB', $ip_data);
                self::echoVoteRes(99, $user_country, $add_subscription); // error
            }
            // Increase vots count
            $data2 = $my_db->getCompItem($vote_id, $contest_id);
            if ($data2) {
                $my_db->increaseItemCountVotes($vote_id);
            } else {
                fv_log('Voting :: can`t find photo by ID(', $vote_id);
                self::echoVoteRes(99, $user_country, $add_subscription); // error
            }
        }

        // Voted successful
        self::echoVoteRes(1, $user_country, $add_subscription);
    }

    /* --------------------------------------------------------------------------- */

    /**
     * Analyze SQL query result and return can user vote or need END
     *
     * @param $contest
     * @param $check_ip
     * @param $check_ip_count
     *
     * @return int
     */
    public static function _get_vote_resp_code($contest, $check_ip, $check_ip_count)
    {
        // Processing data depending on voting frequency
        switch($contest->voting_frequency) {
            case ("once"):
                if ( count($check_ip) > 0  ) {
                    return 2; // user was already voted
                }
                break;
            case ("onceF2"):
                if ( count($check_ip) < 2 && empty($check_ip_count) ) {
                    return TRUE;
                } else {
                    return 2; // user was already voted
                }
                break;
            case ("onceF3"):
                if ( count($check_ip) < 3 && empty($check_ip_count) ) {
                    return TRUE;
                } else {
                    return 2; // user was already voted
                }
                break;
            case ("onceF10"):
                if ( count($check_ip) < 10 && empty($check_ip_count) ) {
                    return TRUE;
                } else {
                    return 2; // user was already voted
                }
                break;
            case ("onceFall"):
                if ( count($check_ip) > 0  ) {
                    return 2; // user was already voted
                }
                break;
            case ("24hFonce"):
                if ( count($check_ip) > 0 ) {
                    return 3; // 24 hour not passed
                }
                break;
            case ("24hF2"):
                if ( count($check_ip) < 2 && empty($check_ip_count) ) {
                    return TRUE;
                } else {
                    return 3; // 24 hour not passed
                }
                break;
            case ("24hF3"):

                if ( count($check_ip) < 3 && empty($check_ip_count) ) {
                    return TRUE;
                } else {
                    return 3; // 24 hour not passed
                }
                break;
            case ("24hFall"):
                if ( count($check_ip) > 0  ) {
                    return 3; // 24 hour not passed
                }
                break;
            default:
                return 2; // user was already voted
                break;
        }
    }

    /**
     * AJAX :: check, is user already entered data (email+name OR social authorization)
     *
     * @return void
     * @output json_array
     */
    public static function is_subscribed()
    {
        //die ( fv_json_encode( array('res'=>'not_subscribed') ) ); // user was not subscribed
        //session_start();
        $contest_id = (int)$_GET['contest_id'];
        $UID = sanitize_text_field($_GET['uid']);
        $my_db = new FV_DB;
        $contest = ModelContest::query()->findByPK($contest_id);

        $not_subscribed = true;
        if (  is_object($contest)  ) {
            $cookis = false;
            // проверяем куку на подписку
            if (isset($_COOKIE['fv_subscribed_' . $contest_id])) {
                $cookis = true;
            }

            if ( !$cookis && $contest->security_type == "defaultAsubscr" ) {
                $ip = fv_get_user_ip();
                //global $wpdb;
                $check_ip = $my_db->getIpInfo($ip, $UID);
                // флаг - не вводил данные

                if (is_array($check_ip)) {
                    foreach ($check_ip as $k => $chk_ip) {
                        if ($chk_ip->contest_id == $contest_id && !empty($chk_ip->email)) {
                            $not_subscribed = false;
                            break;
                        }
                    }
                }
            } else if ( $contest->security_type == "defaultAsocial" || $contest->security_type == "cookieAsocial" ) {
                if (session_id() == '') {
                    session_start();
                }

                if ( isset($_SESSION['fv_social']) ) {
                    $not_subscribed = false;
                }
            }

            // Если не голосовал или данные не заполнены
            if ($not_subscribed) {
                die(fv_json_encode(array('res' => 'not_subscribed'))); // user was not subscribed
            }
        }

        die(fv_json_encode(array('res' => 'is_subscribed'))); // user was subscribed
    }


    /**
     * Ajax :: save into session social login data
     *
     * @return void
     * @output json_array
     */
    public static function soc_login()
    {
        if (session_id() == '') {
            session_start();
        }
        check_ajax_referer('fv_vote', 'some_str');
        //die ( fv_json_encode( array('res'=>'not_subscribed') ) ); // user was not subscribed
        $contest_id = (int)$_POST['contest_id'];
        if ( $contest_id > 0 && !isset($_SESSION['fv_social']) ) {
            $_SESSION['fv_social']["email"] = '';
            if ( isset($_POST['email']) ) {
                $_SESSION['fv_social']["email"] = sanitize_email($_POST['email']);
            }
            if ( isset($_POST['soc_name']) ) {
                $_SESSION['fv_social']["soc_name"] = $_POST['soc_name'];
            }
            $_SESSION['fv_social']["soc_profile"] = $_POST['soc_profile'];
            $_SESSION['fv_social']["soc_network"] = $_POST['soc_network'];
            $_SESSION['fv_social']["soc_uid"] = $_POST['soc_uid'];
            $_SESSION['fv_social']["soc_name"] = '';
        }

        //FvFunctions::dump($_SESSION['fv_social']);

        session_write_close();
        die(  fv_json_encode( array('res' => 'authorized') )  ); // user was subscribed
    }


    /**
     * Send mail to user
     *
     * @param int $code
     * @param string $user_country
     * @param bool $add_subscribsion
     * @param int|bool $hours_leave
     *
     * @return void
     * @output json_array
     */
    static function echoVoteRes ($code, $user_country, $add_subscribsion, $hours_leave = false) {
        // IF error & code < 100 (> 100 need as example for Payments)
        if ( is_int($code) && $code > 1 && $code < 100 && (FV::$DEBUG_MODE & FvDebug::LVL_CODE_VOTE) ) {
            $codes_description = array(
                2 => 'Already voted',
                3 => '24 hours not passed',
                4 => 'date end',
                5 => 'not authorized',
                6 => 'wrong reCAPTCHA',
                66 => 'need reCAPTCHA',
                98 => 'invalid security token',
                99 => 'error',
                101 => 'need payment',
            );

            $curr_code_description = ' - ';
            $curr_code_description .= isset($codes_description[$code]) ? $codes_description[$code] : 'no description';

            fv_log('Unsuccessful Voting attempt :: code ' . $code . $curr_code_description, self::$vote_debug_var);
            global $wpdb;
            fv_log('Unsuccessful Voting attempt :: sql ', $wpdb->last_query);
            fv_log( 'curr memory usage (mb):' . (memory_get_usage()/1024/1024) );
        }

        $response_arr = array('res' => $code, 'user_country' => $user_country, 'add_subscribsion' => $add_subscribsion);
        if ( $hours_leave !== false ) {
            $response_arr['hours_leave'] = $hours_leave;
        }
        $response_arr = apply_filters('fv/vote/echo_res', $response_arr, $code);

        die( fv_json_encode($response_arr) );
    }    
    
}