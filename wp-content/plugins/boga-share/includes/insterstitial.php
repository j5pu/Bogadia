<div class="modal fade" id="bogashareModal" tabindex="-1" role="dialog" aria-labelledby="interstitialLabel" aria-hidden="true">
	<div class="modal-dialog">
		<button id="close-buton" type="button" class="close" data-dismiss="modal">
			<span aria-hidden="true">x</span><span class="sr-only text-muted">Close</span>
		</button>
		<img id="flores_img" src="/wp-content/plugins/boga-share/assets/img/flores.png">
		<div id="compartir_opinion" data-postid="<?php echo get_the_ID(); ?>" data-appid="<?php echo sq_option('fb_app_id') ?>">
			<button id="share_submit_insterstitial" class="share_submit" onclick="myFacebookLogin()"><i class="icon-facebook"></i> | Comparte este post para participar</button>
			<a target="_blank" href="https://www.bogadia.com/sorteos/ganar-50-euros-compartiendo-post-facebook"><p id="mas_info">Más info</p></a>
		</div>
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