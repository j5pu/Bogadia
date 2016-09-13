<?php

$id = $post->ID;
$account_details = unserialize($account->account_details);
$account_extra_details = unserialize($account->account_extra_details);
//$this->print_array($account_details);
//$this->print_array($account_extra_details);
$post_type = get_post_type($id);
$taxonomies = get_object_taxonomies($post_type);
$terms = wp_get_post_terms($id, $taxonomies);
$categories = isset($account_details['category']) ? $account_details['category'] : array();
//$this->print_array($categories);
//$this->print_array($terms);
$category_flag = false;
if (count($categories) == 0) {
    $category_flag = true;
} else if (in_array('all', $categories)) {
    $category_flag = true;
} else {
    foreach ($terms as $term) {
        if (in_array($term->term_id, $categories)) {
            $category_flag = true;
        }
    }
}
//var_dump($category_flag);
//die();
/**
 * Checking if the post type of this post is enabled in the account settings and the account is already authorized
 *  in facebook
 * */
if (in_array($post_type, $account_details['post_types']) && $account_extra_details['authorize_status'] == 1 && $category_flag) {
    /**
     * Account Details
     * [application_id] => 1651381158424608
      [application_secret] => a697762125e735da3bea0309ee36c0d0
      [facebook_user_id] => devteam2070
      [message_format] => Test Post
      [include_image] => 1
      [post_image] => featured_image
      [custom_image_url] => adsfasdfasdf
      [auto_publish_pages] => Array
      (
      [0] => 1
      [1] => 881870878545563
      [2] => 1505719796374561
      )

      [post_types] => Array
      (
      [0] => post
      [1] => page
      )
     * */
    foreach ($account_details as $key => $val) {
        $$key = $val;
    }

    /*
      #post_title,#post_content,#post_excerpt,#post_link,#author_name
     * */
    foreach ($account_extra_details as $key => $val) {
        $$key = $val;
    }
    $post_title = $post->post_title;
    $post_content = strip_tags($post->post_content);
    $post_content = str_replace('&nbsp;', '', $post_content);
    $post_excerpt = $post->post_excerpt;
    $post_link = get_the_permalink($id);
    $post_author_id = $post->post_author;
    $caption = get_bloginfo('description');
    $author_name = get_the_author_meta('user_nicename', $post_author_id);
    $message_format = str_replace('#post_title', $post_title, $message_format);
    $message_format = str_replace('#post_content', strip_tags($post_content), $message_format);
    $message_format = str_replace('#post_excerpt', $post_excerpt, $message_format);
    $message_format = str_replace('#post_link', $post_link, $message_format);
    $message_format = str_replace('#author_name', $author_name, $message_format);

    //echo $message_format;die();



    $post_id = $id;
    $log_time = date('Y-m-d h:i:s A');
    $account_type = 'LinkedIn';
    //$this->print_array($attachment);
    $linkedin = new nsx_LinkedIn($account_details['client_id'], $account_details['client_secret']);
    $linkedin->oauth_verifier = $account_extra_details['liOAuthVerifier'];
    $linkedin->request_token = new nsx_trOAuthConsumer($account_extra_details['liOAuthToken'], $account_extra_details['liOAuthTokenSecret'], 1);
    $linkedin->access_token = new nsx_trOAuthConsumer($account_extra_details['liAccessToken'], $account_extra_details['liAccessTokenSecret'], 1);

    if (post_image == 'featured_image') {
        if (has_post_thumbnail($id)) {
            $image_id = get_post_thumbnail_id($id);
            $image_url = wp_get_attachment_image_src($image_id, 'large', true);
            $picture = $image_url[0];
        } else {
            $picture = trim($custom_image_url);
        }
    } else {
        $picture = trim($custom_image_url);
    }

    try {

        if ($post_format == 'link') {
            $ret = $linkedin->postShare($message_format, $message_format, str_replace('&', '&amp;', $post_link), $picture, $message_format);
        } else {
            $ret = $linkedin->postShare($message_format);
        }
        //echo $message_format;
        $xml = simplexml_load_string($ret);
        //$this->print_array($xml);die();
        if (is_object($xml)) {
            $xml = (array)$xml;
            
            if (isset($xml['update-key'])) {
                $log_details = __('Posted Successfully in LinkedIn Account.', ASAP_TD);
                $log_status = 1;
            } else {
                $log_details = 'Status:' . $xml['status'] . '-' . $xml['message'];
                $log_status = 0;
            }
        } else {
            $log_details = __('Please enable SimpleXML extension in your server to record the log.', ASAP_TD);
            $log_status = 0;
        }
    } catch (Exception $o) {
        $ret = "ERROR:" . print_r($o, true);
        $log_details = $ret;
        $log_status = 0;
    }
    //var_dump($ret);


    /**
     * Inserting log to logs table
     * */
    global $wpdb;
    $log_table_name = $wpdb->prefix . 'asap_logs';
    $wpdb->insert(
            $log_table_name, array(
        'post_id' => $id,
        'log_status' => $log_status,
        'log_time' => $log_time,
        'log_details' => $log_details,
        'account_type' => $account_type
            ), array(
        '%d',
        '%d',
        '%s',
        '%s',
        '%s'
            )
    );
}
//die('hell0');
