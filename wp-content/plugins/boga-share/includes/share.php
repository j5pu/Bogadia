<div id="compartir_opinion" class="text-center" data-postid="<?php echo get_the_ID(); ?>" data-appid="<?php echo sq_option('fb_app_id') ?>" data-logged="<?php echo is_user_logged_in();?>">
    <button id="share_submit_post_mobile" class="share_submit" onclick="myFacebookLogin()"><i class="icon-facebook"></i> | Comparte este post para participar</button>
</div>
<?php if(is_user_logged_in()){ ?>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '<?php echo sq_option('fb_app_id'); ?>', // App ID
            version    : 'v2.1',
            status     : true, // check login status
            cookie     : true, // enable cookies to allow the server to access the session
            xfbml      : true,  // parse XFBML
            oauth      : true
        });
        jQuery('#fb-root').trigger('facebook:init');
    };
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/<?php echo apply_filters('kleo_facebook_js_locale', 'en_US'); ?>/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<?php } ?>