<?php
/*
Plugin Name: Bogacontest
Description: Concurso de modelos

/*CREATE TABLE `bogadia`.`wp_bogacontest_contestant` (
`ID` INT NOT NULL AUTO_INCREMENT,
  `user_id` VARCHAR(45) NULL,
  `Date` DATETIME NULL,
  PRIMARY KEY (`ID`),
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC));*/
/*CREATE TABLE `bogadia`.`wp_bogacontest_img` (
`ID` INT NOT NULL AUTO_INCREMENT,
  `contestant_id` INT UNSIGNED NULL,
  `main` INT NULL DEFAULT 0,
  PRIMARY KEY (`ID`));*/
/*CREATE TABLE `bogadia`.`new_table` (
`ID` INT NOT NULL AUTO_INCREMENT,
  `contestant_id` BIGINT(20) NULL,
  `voter_id` BIGINT(20) NULL,
  `date` DATETIME NULL,
  PRIMARY KEY (`ID`));*/
/*CREATE TABLE `bogadia`.`wp_bogacontest` (
`ID` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(100) NULL,
  PRIMARY KEY (`ID`));*/
function bogacontest_install() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE wp_bogacontest_contestant (
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id varchar(45) NULL,
        contest_id varchar(45) NULL,
        date datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;

	CREATE TABLE wp_bogacontest_img (
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        contestant_id int UNSIGNED NULL,
        contest_id varchar(45) NULL,
        date datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;

	CREATE TABLE wp_bogacontest_votes(
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        contestant_id bigint(20) UNSIGNED NULL,
        votes_id bigint(20) NULL,
        date datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;

	CREATE TABLE wp_bogacontest(
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        slug varchar(100) NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;
	";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'bogashare_install' );

add_filter('rewrite_rules_array','wp_insertMyRewriteRules');
add_filter('query_vars','wp_insertMyRewriteQueryVars');
add_filter('init','flushRules');

// Remember to flush_rules() when adding rules
function flushRules(){
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

// Adding a new rule
function wp_insertMyRewriteRules($rules)
{
    $newrules = array();
    $newrules['concurso/([^/]+)'] = 'index.php?pagename=bogacontest&contest=$matches[1]';
    $newrules['concursante/([^/]+)/(.+)'] = 'index.php?pagename=bogacontestant&contest=$matches[1]&contestant=$matches[2]';
    $finalrules = $newrules + $rules;
    return $finalrules;
}

// Adding the var so that WP recognizes it
function wp_insertMyRewriteQueryVars($vars)
{
    array_push($vars, 'contest');
    array_push($vars, 'contestant');
    return $vars;
}

//Stop wordpress from redirecting
remove_filter('template_redirect', 'redirect_canonical');

include 'class/contestant.php';
$bogacontestant = new contestant();
$bogacontest = new contest();

