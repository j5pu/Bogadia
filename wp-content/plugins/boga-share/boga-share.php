<?php
/*
Plugin Name: BogaShare
Description: Muestra el cajon de compartir a traves de api para el concurso de share
*/

function show_bogashare_dialog() {
    if(is_single(11826)){
        include 'share.php';
    }
}
add_action('wp_footer', 'show_bogashare_dialog');
/*
 *
CREATE TABLE `bogadia_produccion`.`wp_bogashare` (
  `post_id` INT NULL,
  `user_id` INT NULL,
  `comment` VARCHAR(1000) NULL,
  `date` DATETIME NULL,
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC));
 *
 *
 */

?>
