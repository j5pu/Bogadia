<div class="asap-section" id="asap-section-accounts" <?php if ($active_tab != 'accounts') { ?>style="display: none;"<?php } ?>>
    <?php
    $table_name = $wpdb->prefix . "asap_accounts";
    $accounts = $wpdb->get_results("select * from $table_name", 'ARRAY_A');
    //$this->print_array($accounts);
    ?>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e('Account Title', ASAP_TD); ?></th>
                <th><?php _e('Account Type', ASAP_TD); ?></th>       
                <th><?php _e('Account Status', ASAP_TD); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th><?php _e('Account Title', ASAP_TD); ?></th>
                <th><?php _e('Account Type', ASAP_TD); ?></th>       
                <th><?php _e('Account Status', ASAP_TD); ?></th>
            </tr>
        </tfoot>
        <tbody>
            <?php
            if (count($accounts) > 0) {
                $account_counter = 1;
                foreach ($accounts as $account) {
                    $account_id = $account['account_id'];
                    $delete_nonce = wp_create_nonce('asap_delete_nonce');
                    $activate_nonce = wp_create_nonce('asap_activate_nonce');
                    $activate = ($account['account_status'] == 1) ? 0 : 1;
                    $row_class = ($account_counter % 2 == 0) ? 'asap-even-row' : 'asap-odd-row';
                    ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td class="title column-title">
                            <?php echo $account['account_title']; ?>
                            <div class="row-actions">
                                <span class="edit"><a href="<?php echo admin_url("admin.php?page=asap&account_id=$account_id"); ?>">Edit</a> | </span>
                                <span class="delete"><a href="<?php echo admin_url("admin-post.php?action=asap_delete_account&account_id=$account_id&_wpnonce=$delete_nonce"); ?>" onclick="return confirm('<?php _e('Are you sure you want to delete this account?', ASAP_TD); ?>');">Delete</a></span>
                            </div>
                        </td>
                        <td><?php echo ucfirst($account['account_type']); ?></td>
                        <td>
                            <a href="<?php echo admin_url("admin-post.php?action=asap_activate&account_id=$account_id&_wpnonce=$activate_nonce&activate=$activate"); ?>" class="<?php echo ($account['account_status'] == 1) ? 'asap-active' : 'asap-inactive'; ?>"><?php echo ($account['account_status'] == 1) ? __('Deactivate', ASAP_TD) : __('Activate', ASAP_TD); ?></a> 
                        </td>
                    </tr>
                    <?php
                    $account_counter++;
                }
            } else {
                ?>
                <tr colspan="3"><td><?php _e('No Accounts Added Yet', ASAP_TD); ?></td></tr>
                <?php
            }
            ?>

        </tbody>
    </table>
</div>