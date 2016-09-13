<form method="post" action="">
    <select name="network_name">
        <option value=""><?php _e('Choose Network', ASAP_TD); ?></option>
        <option value="facebook">Facebook</option>
        <option value="twitter">Twitter</option>
        <option value="tumblr">Tumblr</option>
        <option value="linkedin">LinkedIn</option>
    </select>
    <input type="submit" name="add_account_submit" value="<?php _e('Add New Account', ASAP_TD); ?>"/>
</form>
<ul class="asap-tabs-wrap asap-tab-nav">
    <li class="asap-tab" id="asap-tab-accounts" data-href="<?php echo admin_url('admin.php?page=asap'); ?>"><?php _e('Accounts', ASAP_TD); ?></li>
    <li class="asap-tab" id="asap-tab-logs" data-href="<?php echo admin_url('admin.php?page=asap&tab=logs'); ?>"><?php _e('Logs', ASAP_TD); ?></li>
    <li class="asap-tab" id="asap-tab-how" data-href="<?php echo admin_url('admin.php?page=asap&tab=how'); ?>"><?php _e('How To Use', ASAP_TD); ?></li>
    <li class="asap-tab" id="asap-tab-about" data-href="<?php echo admin_url('admin.php?page=asap&tab=about'); ?>"><?php _e('About', ASAP_TD); ?></li>
</ul>
<?php
$account_id = $_GET['account_id'];
$table_name = $wpdb->prefix . "asap_accounts";
$account_row = $wpdb->get_row("SELECT * FROM $table_name WHERE account_id = $account_id", 'ARRAY_A');
if ($account_row) {
    $account_details = unserialize($account_row['account_details']);
    $account_extra_details = unserialize($account_row['account_extra_details']);
    // $this->print_array($account_extra_details);
    include('edit-pages/' . $account_row['account_type'] . '-edit.php');
} else {
    wp_redirect(admin_url('admin.php?page=asap'));
}
//$this->print_array($account_row);
