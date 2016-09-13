<div class="wrap">
    <!--Plugin Header-->
    <?php include('sections/header.php'); ?>
    <!--Plugin Header-->

    <div class="asap-main-section">
        <?php if (isset($_SESSION['asap_message'])) { ?><p class="asap-message"><?php
            echo $_SESSION['asap_message'];
            unset($_SESSION['asap_message']);
            ?></p><?php } ?>
        <?php
        global $wpdb;
        //include('networks/facebook.php');
        if (isset($_POST['add_account_submit'], $_POST['network_name']) && $_POST['network_name'] != '') {
            ?>
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
            include('networks/' . $_POST['network_name'] . '.php');
        } else if (isset($_GET['account_id'])) {
            include('edit-page.php');
        } else {
            ?>
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
            <?php $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'accounts'; ?>
            <div class="asap-main-inner-wrap">
                <ul class="asap-tabs-wrap">
                    <li class="asap-tab <?php if ($active_tab == 'accounts') { ?>asap-active-tab<?php } ?>" id="asap-tab-accounts"><?php _e('Accounts', ASAP_TD); ?></li>
                    <li class="asap-tab <?php if ($active_tab == 'logs') { ?>asap-active-tab<?php } ?>" id="asap-tab-logs"><?php _e('Logs', ASAP_TD); ?></li>
                    <li class="asap-tab <?php if ($active_tab == 'how') { ?>asap-active-tab<?php } ?>" id="asap-tab-how"><?php _e('How To Use', ASAP_TD); ?></li>
                    <li class="asap-tab <?php if ($active_tab == 'about') { ?>asap-active-tab<?php } ?>" id="asap-tab-about"><?php _e('About', ASAP_TD); ?></li>
                </ul>
                <?php
                /**
                 * Accounts Section
                 */
                include_once('tabs/accounts.php');

                /**
                 * Logs Section
                 * */
                include('tabs/logs.php');

                /**
                 * How To Use Section
                 */
                include_once('tabs/how-to-use.php');

                /**
                 * About Section
                 */
                include_once('tabs/about.php');
                ?>


            </div>
            <?php
        }
        ?>
    </div>
</div>