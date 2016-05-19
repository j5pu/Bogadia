<?php

/**
 * return default messages for frontend translated with i18n
 *
 * @return array $key => $title
 */
function fv_get_default_public_translation_messages()
{
    return array(
        'upload_form_title' => __('Upload a photo to the contest', 'fv'),
        'upload_form_button_text' => __('Upload', 'fv'),
        'upload_form_need_login' => __('You must be logged to upload a photo to the contest. <a href="%1$s">Login</a> or <a href="%2$s">register</a>.', 'fv'),
        'leaders_title' => __('Leaders vote', 'fv'),
        'other_title' => __('Other <br/>photo', 'fv'),
        'vote_button_text' => __('Vote', 'fv'),
        'vote_count_text' => __('Vote count', 'fv'),
        'vote_lightbox_text' => __('Vote', 'fv'),
        'back_to_contest' => __('Back to contest', 'fv'),
        'pagination_summary' => __('Page %s from %s', 'fv'),
        'pagination_infinity' => __('Load more', 'fv'),
        'shares_count_text' => __('Shares count', 'fv'),

        'toolbar_title_gallery' => __('Gallery', 'fv'),
        'toolbar_title_upload' => __('Upload', 'fv'),
        'toolbar_title_sorting' => __('Sort by:', 'fv'),
        'toolbar_title_sorting_newest' => __('Newest first', 'fv'),
        'toolbar_title_sorting_oldest' => __('Oldest first', 'fv'),
        'toolbar_title_sorting_popular' => __('Popular first', 'fv'),
        'toolbar_title_sorting_unpopular' => __('Unpopular first', 'fv'),
        'toolbar_title_sorting_random' => __('Random', 'fv'),
        'toolbar_title_sorting_alphabetical-az' => __('Alphabetical A-Z (by name)', 'fv'),
        'toolbar_title_sorting_alphabetical-za' => __('Alphabetical Z-A (by name)', 'fv'),

        'timer_days' => __('days', 'fv'),
        'timer_hours' => __('hours', 'fv'),
        'timer_minutes' => __('minutes', 'fv'),
        'timer_secs' => __('seconds', 'fv'),

        'title_share' => __('Share with friends! ', 'fv'),
        'title_voting' => __('Voting!', 'fv'),
        'msg_voting' => __('Voting in process!', 'fv'),
        //TODO *Rename `title` into msg
        'title_voted' => __('Success! ', 'fv'),
        'msg_voted' => __('Your vote has been counted! ', 'fv'),
        'title_not_voted' => __('Warning! ', 'fv'),
        'msg_konkurs_end' => __('The contest has ended.', 'fv'),
        'msg_you_are_voted' => __('You have already voted!', 'fv'),
        'msg_24_hours_not_passed' => __('Since the last vote has not yet been 24 hours!', 'fv'),
        'msg_not_authorized' => __('Your are not authorized in site!', 'fv'),
        'subtitle_not_authorized' => __('Register on the site and vote', 'fv'),
        'fb_vote_msg' => __('For vote please share page on Facebook', 'fv'),
        'msg_err' => __('An error has occurred. Please contact the administrator!', 'fv'),
        'invalid_token' => __('Security token is invalid - please refresh page!', 'fv'),

        'lead_table_rank' => __('Rank', 'fv'),
        'lead_table_photo' => __('Competitor', 'fv'),
        'lead_table_votes' => __('Total Votes', 'fv'),

        'form_subsr_msg' => __('For voting, you need to enter data', 'fv'),
        'form_subsr_name' => __('Your name', 'fv'),
        'form_subsr_email' => __('Your email', 'fv'),

        'form_soc_msg' => __('To be able to vote, you\'ll need to authorise with a social network!', 'fv'),

        'title_recaptcha_vote' => __('Solve reCAPTCHA please!', 'fv'),
        'msg_recaptcha_wrong' => __('Looks like you\'ve got it wrong!', 'fv'),

        'invite_friends' => __('Invite friends to help you win!', 'fv'),

        'download_error' => __('An error occurred whilst downloading the picture(s). ', 'fv'),
        'download_no_image' => __('Please select image file!', 'fv'),
        'download_limit' => __('You have already downloaded the picture, it may still be pending review. ', 'fv'),
        'download_limit_size' => __('Your photo is bigger than %LIMIT_SIZE% megabytes.', 'fv'),
        'download_invaild_email' => __('The email provided is invalid, please check and try again.', 'fv'),
        'download_ok' => __('Thank you, your photo was successfully uploaded and will be reviewed shortly.', 'fv'),
        'download_admin' => __('Thank you, your photo was successfully uploaded and published (because you are an admin, show after page reload).', 'fv'),
        'upload_dimensions_err' => __('Sorry, but this image does not fit required size. %INFO%', 'fv'),
        'upload_dimensions_smaller' => __('It %PARAM% must be smaller than %SIZE%', 'fv'),
        'upload_dimensions_bigger' => __('It %PARAM% must be bigger than %SIZE%', 'fv'),
        'upload_dimensions_height' => __('height', 'fv'),
        'upload_dimensions_width' => __('width', 'fv'),

        'mail_upload_user_title' => __('Your photo submission was successful', 'fv'),
        'mail_upload_user_body' => __('Your photo was uploaded to contest %1$s.', 'fv'),
        'mail_approve_user_title' => __('Your photo has been approved', 'fv'),
        'mail_approve_user_body' => __('Your photo has been approved in contest %1$s.', 'fv'),
        'mail_delete_user_title' => __('Your photo has been deleted', 'fv'),
        'mail_delete_user_body' => __('Your photo has been deleted in contest %1$s.', 'fv'),
        'mail_share_user_title' => __('Help me win a photography competition!', 'fv'),
        'mail_share_user_body' => __('Hi, I\'ve entered the contest %1$s and my photo is called %2$s. I need your help to win so!', 'fv'),

        'mail_upload_admin_title' => __('New Photography Competition Submitted', 'fv'),
        'mail_upload_admin_body' => __('New photo uploaded to contest %1$s. Name: %2$s, user email: %3$s', 'fv'),

        'contest_list_active' => __('Voting active until %s', 'fv'),
        'contest_list_upload_opened_now' => __('Upload active until %2$s', 'fv'),
        'contest_list_upload_opened_future' => __('Upload active from %1$s to %2$s', 'fv'),
        'contest_list_finished' => __('Contest finished at %s', 'fv'),
    );

}

/**
 * return messages for frontend from wordpress option
 * use this for hook it => https://codex.wordpress.org/Plugin_API/Filter_Reference/pre_option_(option_name)
 *
 * @return array get_option result
 */
function fv_get_public_translation_messages()
{
    //* TODO - need remove filter and write function to return just one value from array
    // like fv_get_transl_string('key')
    return apply_filters('fv/translation/get_public_messages', get_option('fotov-translation', fv_get_default_public_translation_messages()));
}

/**
 * return array key for frontend from wordress option
 * Used `wp_kses_data` for secure output and `stripcslashes`
 *
 * @param $key      string
 * @param $default  string
 *
 * @return string
 */
function fv_get_transl_msg($key, $default = '')
{
    if (!empty($key)) {
        $translation = get_option('fotov-translation');
        if (empty($translation)) {
            $translation = fv_get_default_public_translation_messages();
        }
        //$translation = fv_get_public_translation_messages();
        if (isset($translation[$key])) {
            return wp_kses_data(stripcslashes($translation[$key]));
            // doing some
        }
    }
    return $default;
}

/**
 * remove messages that not need in JS
 *
 * @param array $messages
 *
 * @return array get_option result
 */
function fv_prepare_public_translation_to_js($messages)
{
    unset($messages['toolbar_title_gallery']);
    unset($messages['toolbar_title_upload']);
    unset($messages['toolbar_title_sorting_newest']);
    unset($messages['toolbar_title_sorting_popular']);
    unset($messages['toolbar_title_sorting_unpopular']);
    unset($messages['toolbar_title_sorting_random']);
    unset($messages['toolbar_title_sorting_oldest']);


    unset($messages['upload_form_title']);
    unset($messages['upload_form_button_text']);
    unset($messages['upload_form_need_login']);
    unset($messages['leaders_title']);
    unset($messages['other_title']);

    unset($messages['shares_count_text']);

    unset($messages['mail_upload_user_title']);
    unset($messages['mail_upload_user_body']);
    unset($messages['mail_approve_user_title']);
    unset($messages['mail_approve_user_title']);
    unset($messages['mail_approve_user_body']);
    unset($messages['mail_delete_user_title']);
    unset($messages['mail_delete_user_title']);
    unset($messages['mail_delete_user_body']);

    unset($messages['mail_share_user_body']);
    unset($messages['mail_share_user_title']);
    unset($messages['upload_form_title']);

    unset($messages['mail_upload_admin_title']);
    unset($messages['mail_upload_admin_body']);

    unset($messages['contest_list_active']);
    unset($messages['contest_list_upload_opened_now']);

    unset($messages['contest_list_upload_opened_future']);
    unset($messages['contest_list_finished']);

    unset($messages['pagination_summary']);
    unset($messages['back_to_contest']);
    unset($messages['toolbar_title_sorting']);
    unset($messages['timer_days']);
    unset($messages['timer_hours']);
    unset($messages['timer_minutes']);
    unset($messages['timer_secs']);

    foreach ($messages as $k => $msg) {
        $messages[$k] = stripslashes($msg);
    }

    $messages['img_load_fail'] = '-';
    $messages['ajax_fail'] = '-';
    $messages['inactive_contest'] = '-';
    $messages['empty_contest'] = '-';
    if ( FvFunctions::curr_user_can() ) {
        // Add some Public texts for Admin
        $messages['img_load_fail'] = __('Some errors with loading thumbnails.
            If you don\'t know why this happens - please go to Photo contest => Settings and
            change "Thumbnail retrieving type:", if this not helps, contact with support.', 'fv');

        $messages['ajax_fail'] = __('Some errors with voting.
            If you don\'t know why this happens - please go to *Photo contest* => *Settings* => *Vote* tab and
            disable "Use fast voting option? :", if this not helps, contact with plugin support or Hosting.', 'fv');

        $messages['inactive_contest'] = __('This contest not currently active (not started or already finished)!' .
        'If you want enable voting for users - click *Edit contest* in *Admin bar* and check Vote start & end date.', 'fv');

        $messages['empty_contest'] = __('This contest don\'t not have photos! Please click *Edit contest* in *Admin bar* and add first photo or enable countdown/upload!', 'fv');
    }

    return $messages;
}


/**
 * Get public translation key titles
 *
 * return array of option labels for translated messages
 *
 * @return array $key => $title
 */
function fv_get_public_translation_key_titles()
{

    $r = array(
        'general' => array(
            'tab_title' => __('General', 'fv'),
            'leaders_title' => __('Leaders title', 'fv'),
            'vote_button_text' => __('Vote text in button', 'fv'),
            'vote_count_text' => __('Vote count', 'fv'),
            'vote_lightbox_text' => __('Vote text in image preview', 'fv'),
            'other_title' => __('Other photo title in image preview (in some themes)', 'fv'),
            'back_to_contest' => __('Back to contest(uses in single photo page)', 'fv'),
            'pagination_summary' => __('Pagination summary (please not remove %s)', 'fv'),
            'pagination_infinity' => __('Infinity scroll: Button text', 'fv'),
            'shares_count_text' => __('Shares count text', 'fv'),
        ),
        'toolbar' => array(
            'tab_title' => __('Toolbar', 'fv'),
            'toolbar_title_gallery' => __('Gallery tab title', 'fv'),
            'toolbar_title_upload' => __('Upload tab title', 'fv'),
            'toolbar_title_sorting' => __('Sorting title', 'fv'),
            'toolbar_title_sorting_newest' => __('Sorting > newest', 'fv'),
            'toolbar_title_sorting_oldest' => __('Sorting > oldest', 'fv'),
            'toolbar_title_sorting_popular' => __('Sorting > popular', 'fv'),
            'toolbar_title_sorting_unpopular' => __('Sorting > unpopular', 'fv'),
            'toolbar_title_sorting_random' => __('Sorting > random', 'fv'),
            'toolbar_title_sorting_alphabetical-az' => __('Sorting > Alphabetical A-Z (by name)', 'fv'),
            'toolbar_title_sorting_alphabetical-za' => __('Sorting > Alphabetical Z-A (by name)', 'fv'),
        ),

        'timer' => array(
            'tab_title' => __('Countdown', 'fv'),
            'timer_days' => __('Countdown > Days leave', 'fv'),
            'timer_hours' => __('Countdown > Hours leave', 'fv'),
            'timer_minutes' => __('Countdown > Minutes leave', 'fv'),
            'timer_secs' => __('Countdown > Seconds leave', 'fv'),
        ),
        'dialog_messages' => array(
            'tab_title' => __('Dialog messages', 'fv'),
            'title_share' => __('Title > Go share', 'fv'),
            'title_voting' => __('Title > Voting in process', 'fv'),
            'msg_voting' => __('Msg with preloader > Voting in process', 'fv'),
            'title_voted' => __('Title > vote counted (1-2 words)', 'fv'),
            'title_not_voted' => __('Title > vote not counted (1-2 words)', 'fv'),
            'msg_voted' => __('Msg > vote counted', 'fv'),
            'msg_konkurs_end' => __('Msg > the contest has ended', 'fv'),
            'msg_you_are_voted' => __('Msg > user already voted <br/><small>Use *hours_leave* for show when user can vote, if voting frequency Once for 24 hrs</small>', 'fv'),
            'msg_24_hours_not_passed' => __('Msg > 24 hours not passed <br/><small>Use *hours_leave* for show when user can vote</small>', 'fv'),
            'msg_not_authorized' => __('Msg > Your are not authorized', 'fv'),
            'subtitle_not_authorized' => __('SubTitle > Register in site and vote', 'fv'),
            'msg_err' => __('Msg > error', 'fv'),
            'invite_friends' => __('Message under title with call to share', 'fv'),
            'invalid_token' => __('Message if WP security token is invalid and need refresh page', 'fv'),
        ),
        'subscription_form' => array(
            'tab_title' => __('Subscription form', 'fv'),
            'form_subsr_msg' => __('Msg > your need enter data', 'fv'),
            'form_subsr_name' => sprintf(__('Field `%s` caption', 'fv'), __('name', 'fv')),
            'form_subsr_email' => sprintf(__('Field `%s` caption', 'fv'), __('email', 'fv')),
        ),
        'leaders' => array(
            'tab_title' => __('Leaders table', 'fv'),
            'lead_table_rank' => __('Leaders table > Rank', 'fv'),
            'lead_table_photo' => __('Leaders table > Competitor', 'fv'),
            'lead_table_votes' => __('Leaders table > Total Votes', 'fv'),
        ),
        'soc_authorization' => array(
            'tab_title' => __('Social authorization', 'fv'),
            'form_soc_msg' => __('Soc. authorization title', 'fv'),
        ),
        'vote_with_facebook_share' => array(
            'tab_title' => __('Vote with Facebook Share', 'fv'),
            'fb_vote_msg' => __('Msg > for vote please share in FB', 'fv'),
        ),
        'vote_with_recaptcha' => array(
            'tab_title' => __('Vote with reCAPTCHA', 'fv'),
            'title_recaptcha_vote' => __('Title > Solve reCAPTCHA please!', 'fv'),
            'msg_recaptcha_wrong' => __('Msg > Seems you do it wrong!', 'fv'),
        ),
        'upload_form' => array(
            'tab_title' => __('Upload form', 'fv'),
            'upload_form_title' => __('Upload form title', 'fv'),
            'upload_form_button_text' => __('Upload form button text', 'fv'),
            'upload_form_need_login' => __('User must be logged for upload <small>(%1$s will be replaced into Login link from wp_login_url(), %2$s will be replaced into Register link from wp_registration_url())</small>', 'fv'),
        ),
        'upload_messages' => array(
            //    $r = array_merge($r, array(
            'tab_title' => __('Upload messages', 'fv'),
            'download_error' => __('Photo upload error', 'fv'),
            'download_no_image' => __('Photo upload waring, if not file passed', 'fv'),
            'download_limit' => __('User already downloaded photo', 'fv'),
            'download_limit_size' => __('User photo is bigger than limit <small>(%LIMIT_SIZE% will be replaced into `limit value` in megabytes)</small>', 'fv'),
            'download_invaild_email' => __('User email is invalid.', 'fv'),
            'download_ok' => __('Photo is uploaded', 'fv'),
            'download_admin' => __('Photo is uploaded by admin', 'fv'),

            'upload_dimensions_err' => __('Message, that image doe\'s not fit required size (<small>%INFO% will be replaced into text below (smaller or bigger)</small>)', 'fv'),
            'upload_dimensions_smaller' => __('Image must be smaller than size (<small>%SIZE% will be replaced into size from settings, as example 500px., %PARAM% into "height" or "width"</small>).', 'fv'),
            'upload_dimensions_bigger' => __('Image must be bigger than size (<small>%SIZE% will be replaced into size from settings, as example 500px., %PARAM% into "height" or "width"</small>).', 'fv'),
            'upload_dimensions_height' => __('Height %PARAM%', 'fv'),
            'upload_dimensions_width' => __('Width %PARAM%', 'fv'),
        ),
        'mail_messages' => array(
            'tab_title' => __('Notify mail messages', 'fv'),
            'mail_upload_user_title' => __('User email title - photo uploaded', 'fv'),
            'mail_upload_user_body' => __('Upload user notify email <br/> <small>(%1$s will be replaced into contest name, %2$s will be replaced into photo name, %3$s will be replaced into user email, %4$s will be replaced into photo url for share; <i>!Important - if used multi upload, than photo name and other data will be for first photo in form)</i></small>', 'fv'),
            'mail_approve_user_title' => __('User email title - photo approved', 'fv'),
            'mail_approve_user_body' => __('Approved user notify email body', 'fv') .'<br/><small>(%1$s will be replaced into contest name, %2$s will be replaced into photo name, %3$s will be replaced into user email, %4$s will be replaced into photo url for share <u>[requires this - <a href="https://yadi.sk/i/mRcMnhgnmbW9D" target="_blank">https://yadi.sk/i/mRcMnhgnmbW9D</a>, else will not correct works]</u>;)</small>',
            'mail_delete_user_title' => __('User email title - photo deleted', 'fv'),
            'mail_delete_user_body' => __('Delete user notify email body<br/> <small>(%1$s will be replaced into contest name, %2$s will be replaced into photo name, %3$s will be replaced into user email)</small>', 'fv'),

            'mail_share_user_title' => __('User email title - email share', 'fv'),
            'mail_share_user_body' => __('Email share body<br/><small>(%1$s will be replaced into user name, %2$s will be replaced into user email, %3$s will be replaced into receiver email, %4$s will be replaced into photo name, %5$s will be replaced into photo link[don`t remember set up Page Id in contest settings], %6$s will be replaced into contest name)</small>', 'fv'),

            'mail_upload_admin_title' => __('Admin email title - new photo uploaded', 'fv'),
            'mail_upload_admin_body' => __('Upload admin notify email body <small>(%1$s will be replaced into contest name, %2$s will be replaced into photo name, %3$s will be replaced into user email; <i>!Important - if used multi upload, than photo name and other data will be for first photo in form</i>)</small>', 'fv'),
        ),

        'contest_list' => array(
            'tab_title' => __('Contest list', 'fv'),
            'contest_list_active' => __('Contest block text - Voting active until <small>(%s will be replaced into Date)</small>', 'fv'),
            'contest_list_upload_opened_now' => __('Contest block text - Upload active from <small>(%1$s will be replaced into Upload start date, %2$s into Upload end date)</small>', 'fv'),
            'contest_list_upload_opened_future' => __('Contest block text - Upload active until <small>(%1$s will be replaced into Upload start date, %2$s into Upload end date)</small>', 'fv'),
            'contest_list_finished' => __('Contest block text - Contest finished <small>(%s will be replaced into Date)</small>', 'fv'),
        ),
    );

    return $r;
}

/**
 * return Fields, than need to show as textarea
 *
 * @return array
 */
function fv_get_public_translation_textareas()
{
    return apply_filters('fv/translation/get_public_textareas', array('mail_upload_user_body', 'mail_approve_user_body', 'mail_delete_user_body', 'mail_upload_admin_body', 'mail_share_user_body', 'upload_form_need_login'));
}

/**
 * save messages for frontend edited with user
 *
 * @param array $messages
 *
 * @return bool update_option result
 */
function fv_update_public_translation_messages($messages)
{
    if (!FvFunctions::curr_user_can()) {
        return;
    }

    return update_option('fotov-translation', $messages);

}

/**
 * add default messages for frontend translated with i18n into wordpress database
 *
 * @return bool add_option result
 */
function fv_add_public_translation_messages()
{
    if (!FvFunctions::curr_user_can()) {
        return;
    }

    return add_option('fotov-translation', fv_get_default_public_translation_messages(), '', 'no');
}

/**
 * used on plugin Install / Update
 *
 * @return bool add_option result
 */
function fv_update_exists_public_translation_messages()
{
    if (!FvFunctions::curr_user_can()) {
        return;
    }
    $current = fv_get_public_translation_messages();
    $default = fv_get_default_public_translation_messages();
    $have_diff = false;

    if (empty($current)) {
        fv_add_public_translation_messages();
    }
    foreach ($default as $KEY => $TEXT) {
        if (empty($current[$KEY])) {
            $current[$KEY] = $TEXT;
            $have_diff = true;
        }
    }
    if ($have_diff) {
        return fv_update_public_translation_messages($current);
    }

    return true;
}

/**
 * Reset to default messages for frontend translated with i18n into wordpress database
 *
 * @return void
 */
function fv_reset_public_translation()
{
    if (!FvFunctions::curr_user_can()) {
        return;
    }

    delete_option('fotov-translation');
    fv_add_public_translation_messages();
    wp_add_notice("WP Foto Vote:: translation has been reset.", 'warning');
}