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
/**
 * Checking if the post type of this post is enabled in the account settings 
 * 
 * */
if (in_array($post_type, $account_details['post_types']) && $category_flag && $account_extra_details['authorize_status'] == 1) {
    /**
     * Account Details
     * [consumer_key] => eEawirIsupGTEfyk1X6W1MuAaGU2rkSqoVQhYxLrFsugElevu5
      [consumer_secret] => FvCVYCejVUoVlmxb5u4egTNGI8eY8KeuN3Je6HHBMrMFttM3bV
      [username] => devteam2070
      [message_format] => #post_title posted
      [tags] => tag1, tag2, tag3
      [post_types] => Array
      (
      [0] => post
      [1] => page
      [2] => book
      )

     */
    foreach ($account_details as $key => $val) {
        $$key = $val; // converting each key into variable with its own value
    }
    $post_title = $post->post_title;
    $post_content = $post->post_content;
    $post_excerpt = $post->post_excerpt;
    $post_link = get_the_permalink($id);
    $post_author_id = $post->post_author;
    $author_name = get_the_author_meta('user_nicename', $post_author_id);
    $message_format = str_replace('#post_title', $post_title, stripslashes($message_format));
    $message_format = str_replace('#post_content', $post_content, $message_format);
    $message_format = str_replace('#post_excerpt', $post_excerpt, $message_format);
    $message_format = str_replace('#post_link', $post_link, $message_format);
    $message_format = str_replace('#author_name', $author_name, $message_format);
    $tumblr_obj = new Tumblr_ASAP_API_Client($consumer_key, $consumer_secret);
    $tumblr_obj->client->access_token = $account_extra_details['access_token'];
    $tumblr_obj->client->access_token_secret = $account_extra_details['access_token_secret'];
    $blog_url = $username . '.tumblr.com';
    $params = array('type' => 'text',
        'state' => 'published',
        'tags' => $tags,
        'date' => $post->post_date,
        'format' => 'html',
        'slug' => $post->post_name,
        'source_url' => $post_link,
        'title' => $post_title,
        'body' => $message_format,
        'tweet' => 'off'
    );
    //$this->print_array($params);
    $data = $tumblr_obj->postToTumblrBlog($blog_url, $params);
    //$this->print_array($data);
    //die();
    //$this->print_array($reply);
    $post_id = $post->ID;
    $log_time = date('Y-m-d h:i:s A');
    $account_type = 'Tumblr';
    if ($data->meta->status == 201) {
        //tweeted succesfully
        $log_status = 1;
        $log_details = __('Blog Posted Successfully on ', ASAP_TD) . $username;
    } else {
        //there was error while tweeting
        //$error_message = $e->getMessage();
        if (isset($data->response->errors)) {
            $error_message = $data->response->errors[0];
        } else {
            $error_message = $data->meta->msg;
        }
        $log_status = 0;
        $log_details = $error_message;
    }
    /**
     * Inserting log to logs table
     * */
    global $wpdb;
    $log_table_name = $wpdb->prefix . 'asap_logs';
    $wpdb->insert(
            $log_table_name, array(
        'post_id' => $post_id,
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

//die();
}//post type check close