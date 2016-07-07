<?php
function contestantElement($contest, $contestant) {
    echo '<url>'.PHP_EOL;
    echo '<loc>https://www.bogadia.com/concursos/'.$contest.'/'.$contestant.'/</loc>'. PHP_EOL;
    echo '<changefreq>weekly</changefreq>'.PHP_EOL;
    echo '</url>'.PHP_EOL;
}
function contestElement($contest) {
    echo '<url>'.PHP_EOL;
    echo '<loc>https://www.bogadia.com/concursos/'.$contest.'/</loc>'. PHP_EOL;
    echo '<changefreq>weekly</changefreq>'.PHP_EOL;
    echo '</url>'.PHP_EOL;
}

header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' .PHP_EOL;

require_once('../../../wp-load.php');
global $wpdb;
$contestants = $wpdb->get_results("SELECT wp_users.user_nicename, wp_bogacontest.slug FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID INNER JOIN wp_bogacontest ON wp_bogacontest.ID=wp_bogacontest_contestant.contest_id;", OBJECT);
$contests = $wpdb->get_results("SELECT wp_bogacontest.slug FROM wp_bogacontest;", OBJECT);

foreach($contests as $contest){
    contestElement($contest->slug);
}foreach($contestants as $contestant){
    contestantElement($contestant->slug, $contestant->user_nicename);
}

echo '</urlset>';
?>