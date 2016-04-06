<!-- Intersitial Modal -->
<style>
/* CSS used here will be applied after bootstrap.css */
body { font-family: 'Open Sans', sans-serif; }
	@media screen and (max-width: 700px){
		#interstitialModal{
			top: 0% !important;
		}
	}
	@media screen and (min-width: 701px){
		#interstitialModal{
			top: 3% !important;
		}
		.modal-dialog {
			width: 24% !important;
		}
	}
	#interstitialModal .modal-dialog
	{
        color: #ffffff;
    }
	#interstitialModal .modal-body
	{
        padding:0px;
	}
	#interstitialModal .modal-dialog a
	{
        color: #ffffff;
        text-decoration:underline;
	}
	#interstitialModal .modal-content
	{
        width: auto;
        border: 0px;
		background-color: transparent !important;
	}
	.modal-backdrop
	{
        opacity:0.8 !important;
	}
	button#close-buton.close{
		opacity: 1 !important;
		font-size: 21px;
		color: white;
		opacity: 0.8;
	}
	#trackinglink{
		opacity: 0;
	}
	.ad_header, .ad_discount{
    color: black !important;
		text-decoration: none !important;
		font-family: Oswald;
	}
	.ad_header{
    font-size: 20px;
		font-style: normal !important;
	}
	.ad_link{
    color: white !important;
		width: 90%;
		text-decoration: none !important;
		font-size: 20px;
		font-family: Oswald;
		background-color: #d17b83 !important;
		border-color: #d17b83 !important;
	}
	.old_price{
    color: grey !important;
	}

</style>
<link href='https://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
<div class="modal fade" id="interstitialModal" tabindex="-1" role="dialog" aria-labelledby="interstitialLabel" aria-hidden="true">
	<div class="modal-dialog">
		<p class="text-right">
			<a id="trackinglink" href="#">BOGA</a>
			<button id="close-buton" type="button" class="close" data-dismiss="modal">
				<span aria-hidden="true">x</span><span class="sr-only text-muted">Close</span>
			</button>
		</p>
		<a id="trackinglink_2" href="#">
			<div class="modal-content">
				<div class="modal-body text-center">
					<!-- /61601326/mayor -->
					<div id='div-gpt-ad-1457972362060-0' style='height:600px; width:300px; margin: 0 auto;'>
						<script type='text/javascript'>
							googletag.cmd.push(function() { googletag.display('div-gpt-ad-1457972362060-0'); });
						</script>
					</div>
				</div>
			</div>
		</a>
    </div>
</div>
<script>
    jQuery(document).ready(function(){
        var cookie_val = localStorage.getItem('bogatitial');
        if(cookie_val){
            if(cookie_val < 2){
                cookie_val++;
                localStorage.setItem('bogatitial', cookie_val);
            }else{
                localStorage.removeItem('bogatitial');
            }
        }else{
			var tracking_link = jQuery('.ad_link').attr('href');
			jQuery('#trackinglink').attr('href', tracking_link);
            jQuery('.shareaholic-share-buttons-container.floated').hide();
            jQuery('#interstitialModal').modal({show:true, backdrop: 'static'});
            localStorage.setItem('bogatitial', 1);
        }
        jQuery('#close-buton').on('click', function(){
            jQuery('.shareaholic-share-buttons-container.floated').show('slow');
        });
    });
</script>