<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * http://codyhouse.co/gem/responsive-tabbed-navigation/
*/
?>

<div class="wrap" id="fv_translation">
    <?php
    if ($saved) {
		echo '<div id="setting-error-settings_updated" class="updated settings-error">
                <p>
                    <strong>' . __('Translation saved.', 'fv') . '</strong>
                </p>
             </div>';
	}
	?>    

	<h2>
        <?php _e('Translating messages', 'fv') ?>
    </h2>

        <?php _e('Please don\'t use double quotes (") !', 'fv') ?><br/>
        <strong><?php printf( __('To translate form fields go to <a href="%s" target="_blank">"Form Builder"</a> page!', 'fv'), admin_url('admin.php?page=fv-formbuilder') )?></strong>
        <strong><?php printf( __('To translate more Countdown fields go to <a href="%s" target="_blank">"Addons"</a> page!', 'fv'), admin_url('admin.php?page=fv-addons') )?></strong>


	<form name="fv-translation" method="POST"><div class="fv-tabs">
        <!-- Tabs -->
        <nav>
            <ul class="fv-tabs-navigation">
                <?php foreach ($key_groups as $group_name => $group_fields) : ?>
                    <li><a href="#0" data-content="<?php echo $group_name; ?>" class="<?php echo ($group_name == 'general') ? 'selected' : ''; ?>">
                            <?php echo $group_fields['tab_title']; ?>
                    </a></li>
                <?php endforeach; ?>
            </ul> <!-- fv-tabs-navigation -->
        </nav>

        <!-- Tabs content / Generate ul list with tables for tabbed navigation -->
        <ul class="fv-tabs-content">
            <?php foreach ($key_groups as $group_name => $group_fields) : ?>
                <li data-content="<?php echo $group_name; ?>" class="<?php echo ($group_name == 'general') ? 'selected' : ''; ?>">
                    <table class="form-table">
                        <?php foreach ($group_fields as $key => $title) : ?>
                            <tr valign="top">
                                <?php if ($key == 'tab_title'): ?>
                                    <!-- Tab title -->
                                    <td><h3 class="no_margin"><?php echo $title ?></h3></td>
                                    <td><hr></td>
                                <?php else: ?>
                                    <!-- Tab fields -->
                                    <th scope="row"><?php echo $title ?>: </th>
                                    <td>
                                        <?php if ( !in_array($key, fv_get_public_translation_textareas()) ): ?>
                                            <input name="<?php echo $key ?>" value="<?php echo ( isset($messages[$key]) ) ? esc_attr(stripcslashes($messages[$key])) : ''; ?>" class="large-text"/>
                                        <?php else: ?>
                                            <textarea name="<?php echo $key ?>" class="large-text" rows="3"/><?php echo ( isset($messages[$key]) ) ?  wp_kses_data(stripcslashes($messages[$key])) : ''; ?></textarea>
                                            <small><?php _e('Did\'t use html tags.', 'fv') ?></small>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </li>
            <?php endforeach; ?>
        </ul>

		<p class="submit">
			<input type="hidden" name="action" value="save" />
            <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'fv') ?>" />

            <?php if (current_user_can('install_plugins')): ?>
                &nbsp;&nbsp;&nbsp;<a onclick="return confirm('<?php _e('Are you sure to reset translation?', 'fv') ?>');" href="<?php echo admin_url('admin.php?page=fv-translation&action=clear'); ?>"><?php _e('Reset translation.', 'fv') ?></a>
            <?php endif; ?>
		</p>
    </div></form>

	<style>
		table td input {
			min-width: 60%;
		}

		h3.no_margin {
			margin: 0;
		}

	</style>    
</div>