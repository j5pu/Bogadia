<h4 class="asap-network-title"><?php _e('Post Settings', ASAP_TD); ?></h4>
<div class="asap-network-inner-wrap">
    <div class="asap-network-field-wrap">
        <label><?php _e('Enable Auto Publish For:', ASAP_TD); ?></label>
        <div class="asap-network-field">
            <?php
            $post_types = $this->get_registered_post_types();
            foreach ($post_types as $post_type) {
                $post_type_obj = get_post_type_object($post_type);
                ?>
                <label class="asap-full-width"><input type="checkbox" name="account_details[post_types][]" value="<?php echo $post_type; ?>"/><?php echo $post_type_obj->labels->name; ?></label>
            <?php }
            ?>
        </div>
    </div>

    <div class="asap-network-field-wrap">
        <label><?php _e('Categories for Auto Post', ASAP_TD); ?></label>
        <div class="asap-network-field">
            <select name="account_details[category][]" multiple="multiple">
                <option value="all"><?php _e('All', ASAP_TD); ?></option>
                <?php
                $taxonomies = get_taxonomies();
                unset($taxonomies['nav_menu']);
                unset($taxonomies['post_format']);
                //$this->print_array($taxonomies);
                foreach ($taxonomies as $taxonomy) {
                    $taxonomy_obj = get_taxonomy($taxonomy);

                    $terms = get_terms($taxonomy, array('hide_empty' => 0));
                    if (count($terms) > 0) {
                        ?>
                        <optgroup label="<?php echo $taxonomy_obj->label; ?>">
                            <?php
                            foreach ($terms as $term) {
                                ?>
                                <option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
                                <?php
                            }
                            ?>
                        </optgroup>
                        <?php
                    }
                }
                ?>
            </select>
            <div class="asap-field-note">
                <?php _e('Note:Please use command or control key to select multiple options.Not selecting any of the option will be considered as <strong>All</strong> selected.', ASAP_TD); ?>
            </div>
        </div>
    </div>
</div>
<div class="asap-network-field-wrap">
    <div class="asap-network-field">
        <input type="submit" name="add_submit" value="<?php _e('Add Account', ASAP_TD); ?>"/>
        <a href="<?php echo admin_url('admin.php?page=asap'); ?>"><input type="button" value="<?php _e('Back', ASAP_TD); ?>"/></a>
    </div>
</div>