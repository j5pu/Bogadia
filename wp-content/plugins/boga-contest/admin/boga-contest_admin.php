<?php
/**
 * Created by PhpStorm.
 * User: yera
 * Date: 14/9/16
 * Time: 18:26
 */

function sincronize_buddypress(){
    global $wpdb;
    $results = $wpdb->get_results("SELECT wp_bogacontest_contestant.id, user_id, path FROM wp_bogacontest_contestant INNER JOIN wp_bogacontest_img ON wp_bogacontest_contestant.ID=wp_bogacontest_img.contestant_id WHERE wp_bogacontest_img.main='1';", OBJECT);
    $counter = 0;
    echo '<h3>Sincronización Concurso de modelos y Buddypress</h3>';
    echo '<p>Cada concursante sincronizado se representa mediante un guión</p>';
    echo '<ul class="list-group">';
    echo '<li class="list-group-item">';
    foreach ($results as $contestant){
        $filename = ABSPATH . $contestant->path;
        $filename = str_replace('//', '/', $filename);
        $avatar_folder = WP_CONTENT_DIR .'/uploads/avatars/'. $contestant->user_id;

        if (!file_exists($avatar_folder)) {
            mkdir($avatar_folder, 0777, true);
        }

        copy($filename, $avatar_folder .'/main-bpfull.jpg');
        copy($filename, $avatar_folder .'/main-bpthumb.jpg');

        if ( function_exists( 'bp_update_user_last_activity' ) ) {
            bp_update_user_last_activity($contestant->id);
        }
        echo'-';
        $counter++;
        if ($counter == 10){
            echo '</li><li class="list-group-item">';
            $counter = 0;
        }
    }
    echo '</li></div>';
}

sincronize_buddypress();