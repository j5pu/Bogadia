<div class="modal fade" id="interstitialModal" tabindex="-1" role="dialog" aria-labelledby="interstitialLabel" aria-hidden="true">
	<div class="modal-dialog">
		<button id="close-buton" type="button" class="close" data-dismiss="modal">
			<span aria-hidden="true">x</span><span class="sr-only text-muted">Close</span>
		</button>
		<img id="flores_img" src="/wp-content/plugins/boga-share/assets/img/flores.png">
		<div>
			<button id="share_submit_insterstitial" class="share_submit" onclick="myFacebookLogin()"><i class="icon-facebook"></i> | Comparte este post para participar</button>
			<img id="bogashare_spinner" src="/wp-content/plugins/boga-share/assets/img/spinner2.gif" style="display: none; width: 50px">
			<div id="success" class="alert alert-success">
				<strong>¡Genial!</strong> Se ha compartido con tus amigos.
				<div class="hide-alert"><p><strong>Quitar este aviso</strong></p></div>
			</div>
			<div id="danger" class="alert alert-danger">
				<strong>¡Vaya!</strong> Se ha producido un error. Inténtalo de nuevo o escribe a valle@bogadia.com.
				<div class="hide-alert"><p><strong>Quitar este aviso</strong></p></div>
			</div>
			<a target="_blank" href="https://www.bogadia.com/sorteos/gana-kit-productos-crea-m-solo-darnos-opinion/"><p id="mas_info">Más info</p></a>
		</div>
	</div>
</div>