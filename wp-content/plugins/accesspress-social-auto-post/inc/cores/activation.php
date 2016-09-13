<?php

global $wpdb;

$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix . "asap_accounts";
$log_table_name = $wpdb->prefix . "asap_logs";
$sql = "CREATE TABLE $table_name (
  account_id INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY(account_id),
  account_title VARCHAR(255),
  account_status INT NOT NULL,
  account_type VARCHAR(255),
  account_details TEXT,
  account_extra_details TEXT NOT NULL
) $charset_collate;";

$log_tbl_query = "CREATE TABLE $log_table_name (
  log_id INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY(log_id),
  post_id INT NOT NULL,
  log_status INT NOT NULL,
  log_time VARCHAR(255),
  log_details TEXT,
  account_type VARCHAR(30)
) $charset_collate;";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta($sql);
dbDelta($log_tbl_query);
