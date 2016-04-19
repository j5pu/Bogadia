<div id="compartir_opinion" class="text-center" data-postid="<?php echo get_the_ID(); ?>" data-appid="<?php echo sq_option('fb_app_id') ?>" data-logged="<?php echo is_user_logged_in();?>">
    <h3 id="compartir_opinion_header"></h3>
    <img id="bogashare_spinner" src="/wp-content/plugins/boga-share/assets/img/spinner2.gif" style="display: none; width: 80px">
    <div id="compartir_opinion_desplegar" class="row">
        <div class="col-md-6">
            <a target="_blank" href="https://www.bogadia.com/sorteos/gana-kit-productos-crea-m-solo-darnos-opinion/"><img id="bogashare_img" class="img-responsive" src="/wp-content/plugins/boga-share/assets/img/concursocream.jpg"></a>
        </div>
        <div class="col-md-6">
<!--            <p id="bogashare_description"><strong><a target="_blank" href="https://www.bogadia.com/sorteos/gana-kit-productos-crea-m-solo-darnos-opinion/">Más info</a></strong></p>-->
            <form id="compartir_opinion_form">
<!--                <input id="share_msg" placeholder="¡Me parece increible!" style="margin: 0 auto;margin-bottom: 15px;border: 1px solid lightgray;width: 90%;height: 40px;">
-->                <input id="share_submit" type="submit" value="Compartir en Facebook y participar" onclick=<?php if(!is_user_logged_in()){ ?>"myFacebookLogin()"<?php }else{  ?>"myFacebookShare()"<?php } ?> ></input>
            </form>
        </div>
    </div>
    <button id="close_compartir_opinion"><em class="icon-angle-down" style="font-size: 25px;"></em></button>
<!--    <button id="open_compartir_opinion" style="display: none;"><em class="icon-angle-up" style="font-size: 25px;"></em></button>
-->    <div id="success" class="alert alert-success">
        <strong>¡Genial!</strong> Se ha compartido con tus amigos.
        <div class="hide-alert"><p><strong>Quitar este aviso</strong></p></div>
    </div>
    <div id="danger" class="alert alert-danger">
        <strong>¡Vaya!</strong> Se ha producido un error. Inténtalo de nuevo o escribe a valle@bogadia.com.
        <div class="hide-alert"><p><strong>Quitar este aviso</strong></p></div>
    </div>
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
