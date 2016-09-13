<div class="asap-section" id="asap-section-how" <?php if ($active_tab != 'how') { ?>style="display: none;"<?php } ?>>
    <p>There are 4 networks available for auto post.All the networks have different configurations.</p>
    <div class="more-title">Facebook Auto Post</div>
    <p>For Facebook Auto Post, you will need to get <strong>Facebook Application ID</strong>,<strong>Facebook Application Secret</strong> and <strong>Facebook User ID</strong>.</p>
    <p>To get all the required details you will need to create a Facebook APP <a href="https://developers.facebook.com/apps" target="_blank">here</a>.</p>
    <p>Also please make sure you follow below steps after creating app.<br>Navigate to Apps > Settings > Edit settings > Website > Site URL.<br/> Set the site url as : <?php echo site_url(); ?></p>
    <p>Once you add the account, then you will need to authorize the account.You can authorize the account by clicking "<strong>Authorize</strong>" button at top left of the account settings section.Once you click on <strong>Authorize</strong> button you will be redirected to the facebook which will show a dailog box asking the permission to post on your wall and get all the details regarding your facebook pages related with that account.You will need to allow all the permission to enable the auto post to your profile and fan pages. </p>
    <div class="more-title">Twitter Auto Post</div>
    <p>For Twitter Auto Post, you will need <strong>API Key</strong>,<strong>API Secret</strong>,<strong>Access Token</strong>,<strong>Access Token Secret</strong>.</p>
    <p>You can get all these details after creating a Twitter Application <a href="https://apps.twitter.com/" target="_blank">here</a>.And please make sure you keep <?php echo site_url(); ?> as Website URL while creating the app.</p>
    <div class="more-title">LinkedIn Auto Post</div>
    <p>For LinkedIn Auto Post, you will need to get <strong>Client ID</strong> and <strong>Client Secret</strong>.You can get both the details by creating a LinkedIn App <a href="https://www.linkedin.com/developer/apps" target="_blank">here</a>.</p><p>Please make sure you keep the <?php echo admin_url('admin-post.php?action=asap_linkedin_callback_authorize&account_id=account_id_in_url_bar_while_editing_account'); ?> in all the url fields while creating the App else Auto Post may not work.</p>
    <div class="more-title">Tumblr Auto Post</div>
    <p>For Tumblr Auto Post, you will need <strong>Tumblr API Consumer Key</strong>,<strong>OAuth Consumer Secret</strong> and <strong>Tumblr Username</strong>.</p>
    <p>You can get all the credentials after creating a Tumblr App <a href="<?php echo $this->get_tumblr_reg_url();?>" target="_blank">here.</a>Once you fill all the credentials then you will need to authorize Tumblr Account to allow auto post to your Tumblr Profile.Please make sure you don't authorize multiple times within few minutes.</p>
</div>