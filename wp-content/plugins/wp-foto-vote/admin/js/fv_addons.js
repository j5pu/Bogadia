//** Show ads in Addons page
jQuery(document).ready(function() {

	// if mobile - end
	if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
		return;
	}
	// Show ads
	setTimeout(
		function() {
			var image = new Image();
			image.onload = function () {
				//console.info("Image loaded !");
				//do something...

				jQuery( '#redux-header' ).prepend( '<div class="fv_topa"><a href="http://wp-vote.net/ad_addons-top"></a></div>' );
				var el = jQuery( '#redux-header' );
				el.css( 'position', 'relative' );

				el.find( '.fv_topa' ).attr(
					  'style',
					  'float:right; display:block !important; overflow:hidden;'
				)
			  	.find('a')
			 	.append(image);

			}
			image.onerror = function () {
				//console.error("Cannot load image");
				//do something else...
			}
			image.src = "http://wp-vote.net/show/ad_addons-top.png";


		},  600
	);

});