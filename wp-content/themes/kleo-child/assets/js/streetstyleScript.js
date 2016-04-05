if ('undefined' != typeof jQuery)
{
(function($){


// Funcionalidades

/*Scrool Move*/

/*
window.onload = function() {
	
*/
	if (window.addEventListener) {

		// IE9, Chrome, Safari, Opera
		window.addEventListener("mousewheel", MouseWheelHandler, false);
		// Firefox
		window.addEventListener("DOMMouseScroll", MouseWheelHandler, false);
	}
	// IE 6/7/8
	else window.attachEvent("onmousewheel", MouseWheelHandler);

	function MouseWheelHandler(e) {


		if($('.bigSection').css('display')=='block'){

			// cross-browser wheel delta
			var e = window.event || e; // old IE support
			var delta = Math.max(-1, Math.min(1, (e.wheelDelta || -e.detail)));

			var navImagenesHeight = $('.navImagenes').height();
			var elemNavImagenesHeight = $('.elemNavImagenes').height()+10;
			var maxScroll = $('.elemNavImagenes').length * elemNavImagenesHeight - navImagenesHeight - 5;

			if (delta==-1){
				var _goToScrollPos = $('.navImagenes').scrollTop()+50;
				$('.navImagenes').scrollTo( _goToScrollPos, {duration:0});
				displayArrowsScrolled( _goToScrollPos, 0, maxScroll);
			}else if(delta==1){
				var _goToScrollPos = $('.navImagenes').scrollTop()-50;
				$('.navImagenes').scrollTo( _goToScrollPos, {duration:0});
				displayArrowsScrolled( _goToScrollPos, 0, maxScroll);
			}

		}

		return false;
	}

//Scroll Limit
$('body').on({
    'mousewheel': function(e) {
		if($('.bigSection').css('display')=='block' && $(window).width()>767){
			e.preventDefault();
		}
		if($('.newStreetStyle').css('display')=='block'){
			e.preventDefault();
		}
	}
})
window.onscroll = function(e) {
	if($('.bigSection').css('display')=='block'){
		var scrollPosition =  self.pageYOffset || document.documentElement.scrollTop  || document.body.scrollTop;
		if ($(window).width()>767){
			e.preventDefault();
			$(window).scrollTop(0);
		}
		if($('.newStreetStyle').css('display')=='block'){
			e.preventDefault();
			$(window).scrollTop(0);
		}
	}
};

$.fn.scrollTo = function( target, options, callback ){
  if(typeof options == 'function' && arguments.length == 2){ callback = options; options = target; }
  var settings = $.extend({
    scrollTarget  : target,
    offsetTop     : 50,
    duration      : 500,
    easing        : 'swing'
  }, options);
  return this.each(function(){
    var scrollPane = $(this);
    var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
    var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
    scrollPane.animate({scrollTop : scrollY }, parseInt(settings.duration), settings.easing, function(){
      if (typeof callback == 'function') { callback.call(this); }
    });
  });
}

var waitForFinalEvent = (function () {
  var timers = {};
  return function (callback, ms, uniqueId) {
    if (!uniqueId) {
      uniqueId = "Don't call this twice without a uniqueId";
    }
    if (timers[uniqueId]) {
      clearTimeout (timers[uniqueId]);
    }
    timers[uniqueId] = setTimeout(callback, ms);
  };
})();

function ownResize(){
	if (document.createEvent) { // W3C
	    var ev = document.createEvent('Event');
	    ev.initEvent('resize', true, true);
	    window.dispatchEvent(ev);
	} else { // IE
	    document.fireEvent('onresize');
	}
}

$( document ).ready(
	function createStreetGallery(){
//Crear contenedor Galeria
		$('body').append($('<div>', {class: 'bigSection'}));
		$('body').append($('<div>', {class: 'bigCover'}));

//Crear boton cerrar		
		$('.bigSection').append($('<div>', {class: 'quitBigSection'}).on('click',function(){
			$('.medSection').css('display','none');
			$('.bigSection').css({'height':'0%','display':'none'});
			$('.bigCover').css('display','none');
			$('.bigSection').remove('.arrowNav');
			$('.pagination-sticky').css('visibility','visible');
			$('.kleo-go-top').css('visibility','visible');
		}));

//Ocultar las secciones dentro de contenedores(medSection)
		$sections = $('section').has('p').has('img').not(':has(section)').has('.publiGaleria'); //.has('.publiGaleria');
		for ($i=0; $i<$sections.length; $i++){
			$('.bigSection').append($('<div>', {class: 'medSection', id: 'medSection'+$i}).css({'display':'none'}));
			$($sections[$i]).addClass('sec'+$i);	
		}
		$medSection = $('.medSection');
		for($i=0; $i<$medSection.length; $i++){
			$($medSection[$i]).append($('.sec'+$i));
		}

//Funcionalidad al .menuGaleria
		$imagenes = $('.menuGaleria').find('li').addClass('elemGaleria').on('click',function(){
			$l=$($imagenes).length;
			$i=$(this).parent().children().index(this);
			$('.medSection').css('display','none');
			$('.medSection').css('opacity','0');
			$('.bigSection').css('display','inline-block');
			$('.bigSection').css('height','100%');
			$('.bigCover').css('display','block');
			openMedsection($i);
		});
	});
function openMedsection(num){

		$i=num;
		$('.pagination-sticky').css('visibility','hidden');
		$('.kleo-go-top').css('visibility','hidden');
		$(window).scrollTop(20);
		$('#medSection'+num).css('display','block');
		setTimeout(function(){
			$('#medSection'+num).css('opacity','1');
			$('#medSection'+num).find('.wpb_wrapper').eq(0).find('img').css({'opacity':'1'});
		},200);

	//Crear shadow
		var shadowHeight = $('.medSection').eq(num).find('.kleo_text_column').height();
		$('.medSection').eq(num).find('.wpb_single_image').css('box-shadow','0px 0px 20px #000, 0px ' + shadowHeight + 'px 20px #000');
		$('.medSection').eq(num).find('.wpb_raw_html').css('box-shadow','0px 0px 20px #000, 0px ' + shadowHeight + 'px 20px #000');

		ownResize();
	
		sideArrows(num);

	//Crear icono debajo de la publi
		if( !$('.medSection .publiGaleria').eq(num).has('.descriptionStreetStyle').length ){

			if(!$('.medSection .publiGaleria').eq(num).has('.logoGaleria').length){
				$publiSections = $('.medSection .publiGaleria');
				$diamond = $('.medSection .publiGaleria').eq(num).prev().find('.kleo_text_column').height();
				$($publiSections[num]).children().append($('<div>', {class: 'logoGaleria'}).css('height',$diamond+'px').append($('<img>').attr({'src':'https://www.bogadia.com/wp-content/themes/kleo-child/assets/img/diamante.png'}).css('max-height', $('ins').eq(num).height()*0.15+'px')));
				
			}else{
				$('.medSection .publiGaleria').eq(num).find('.logoGaleria img').css('max-height', $('ins').eq(num).height()*0.15+'px');
			}

		}else{
			//crea y da tamaño al cajon inferior de la segunda foto(descripcion+publi)
			if(!$('.medSection .publiGaleria').eq(num).has('.logoGaleria').length){
				$publiSections = $('.medSection .publiGaleria');
				$diamond = $('.medSection .publiGaleria').eq(num).prev().find('.kleo_text_column').height();
				$($publiSections[num]).children().append($('<div>', {class: 'logoGaleria'}).css('height',$diamond+'px').append($('<img>').attr({'src':'https://www.bogadia.com/wp-content/themes/kleo-child/assets/img/clip.png'}).css({'width':'22%', 'margin-left':'-83%','margin-top':'-27%'})));

			}
			//tamaño del cajon de descripcion-publi
			var desc_MaxHe = $('.medSection .col-sm-6.wpb_column:not(.publiGaleria)').eq(num).height();
			$('.descriptionStreetStyle').eq(num).css('max-height', desc_MaxHe);
			//separacion entre descripciones
			var desc_ins_He = $('.descriptionStreetStyle ins').eq(num).outerHeight();
			var desc_p_Le = $('.descriptionStreetStyle').eq(num).find('p').length;
			var desc_p_He = $('.descriptionStreetStyle').eq(num).find('p').outerHeight();
			var mult_mgnBtn = 1/(desc_p_Le-2)+1;
			var desc_p_mgnBtn = ( desc_MaxHe*1.1 - desc_ins_He - ( desc_p_Le * desc_p_He ) ) / desc_p_Le;
			$('.descriptionStreetStyle').eq(num).find('p:odd').css('margin-bottom', desc_p_mgnBtn*mult_mgnBtn );
			$('.descriptionStreetStyle').eq(num).find('p').last().css('margin-bottom', desc_p_mgnBtn );
		}
		$diamond = $('.medSection .publiGaleria').eq(num).prev().find('.kleo_text_column').height();
		$('.medSection .publiGaleria').find('.logoGaleria').css('height',$diamond+'px');
		ownResize();

		$(window).resize(function () {
		    waitForFinalEvent(function(){
				$diamond = $('.medSection .publiGaleria').eq(num).prev().find('.kleo_text_column').height();
				$($publiSections[num]).find('.logoGaleria').css('height',$diamond+'px');

				if( !$('.medSection .publiGaleria').eq(num).has('.descriptionStreetStyle').length ){
					$($publiSections[num]).find('.logoGaleria').find('img').css('max-height',$('ins').eq(num).height()*0.15+'px');
				}else{
					$('.descriptionStreetStyle').eq(num).css('max-height', $('.medSection .col-sm-6.wpb_column:not(.publiGaleria)').eq(num).height());
				}
				
				var shadowHeight = $('.medSection').eq(num).find('.kleo_text_column').height();
				$('.medSection').eq(num).find('.wpb_single_image').css('box-shadow','0px 0px 20px #000, 0px ' + shadowHeight + 'px 20px #000');
				$('.medSection').eq(num).find('.wpb_raw_html').css('box-shadow','0px 0px 20px #000, 0px ' + shadowHeight + 'px 20px #000');

			    //Determinar altura
			    if($(window).width()>767){
					var alturaMedSection = ($(window).height()-$('.medSection').eq(num).height())/2.5;
					$('.medSection').eq(num).css('top', alturaMedSection+'px');

					if($('.arrowTop').length){
						$('.arrowTop').css('height',$('.arrowTop').width());
						$('.arrowBot').css('height',$('.arrowBot').width());
					}
				}

				sideArrows(num);
		    }, 200, "some unique string");
		});

	//Crear botones de compartir
		if(!$('.medSection .publiGaleria').eq(num).has('.share-links').length){
			$publiSections = $('.medSection .publiGaleria');
			var img_src_to_share = $('.elemGaleria img').eq(num).attr('src');
			img_src_to_share = img_src_to_share.split("uploads/").pop();
			//img_src_to_share = img_src_to_share.replace('/','%2F');
			var name_st = $('.medSection .col-sm-6.wpb_column.column_container:not(.publiGaleria) p').eq(num).html();
			name_st = name_st.replace(' ', '_');

			var location_url = window.location.href;
			if (location_url.search("ph=")<0){
				location_url = location_url + '?ph=';
			}else{
				location_url = location_url.substring(0, location_url.search('=')+1);
			}
			location_url = location_url.replace('https','http');
			location_url += img_src_to_share + "&nm_st=" + name_st;
			$($publiSections[num]).append($('<div>').attr({class: 'share-links'})
				.append($('<span>').attr({class: 'kleo-facebook'})
					.append($('<a>').attr({class: 'post_share_facebook'}).on('click', function(){ javascript:window.open(this.href, //http://www.facebook.com/sharer.php?u=https://www.facebook.com/photo.php?fbid=481019152029911
					'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=220,width=600');return false;}).attr('href', "http://www.facebook.com/sharer.php?u=" + encodeURIComponent(location_url) )
						))
				.append($('<span>').attr({class: 'kleo-twitter'})
					.append($('<a>').attr({class: 'post_share_twitter'}).on('click', function(){ javascript:window.open(this.href,
					'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=260,width=600');return false;}).attr('href', "https://twitter.com/share?url=" + window.location.href)
						))
				.append($('<span>').attr({class: 'kleo-googleplus'})
					.append($('<a>').on('click', function(){ javascript:window.open(this.href,
					'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;}).attr('href', "https://plus.google.com/share?url=" + encodeURIComponent(location_url) )
						)));

			if($(window).width()>767){
				$('.medSection .share-links').css('margin-left',-$('.publiGaleria').eq(num).outerWidth());
			}
		}

	//Crear menu galeria
		if($('.elemGaleria img').length>1){
			if(!$('.elemNavImagenes').length){
				$('.bigSection').prepend($('<div>', {class: 'navImagenes'}));
				for(var i=0;i<$('.elemGaleria img').length;i++){
					$('.elemGaleria img').eq(i).clone().addClass('elemNavImagenes').appendTo('.navImagenes').on('click', function(){
						$('.medSection').css('display','none');
						$('.medSection').css('opacity','0');
						$i=$(this).parent().children().index(this);
						openMedsection($i);
					});
				}
			}	
			$('.elemNavImagenes').css('border-color','white');
			$('.elemNavImagenes').eq($i).css('border-color','#902828');

			if($(window).height()*0.7<$('.elemNavImagenes').length*$('.elemNavImagenes').height()){
				if(!$('.arrowTop').length){
					$('.bigSection').prepend($('<div>', {class: 'arrowTop arrowNav'}));
					$('.arrowTop').css('height',$('.arrowTop').width());
					$('.bigSection').prepend($('<div>', {class: 'arrowBot arrowNav'}));
					$('.arrowBot').css('height',$('.arrowBot').width());

					var navImagenesHeight = $('.navImagenes').height();
					var elemNavImagenesHeight = $('.elemNavImagenes').height()+10;
					var maxScroll = $('.elemNavImagenes').length * elemNavImagenesHeight - navImagenesHeight - 5;

					$('.arrowTop').on('mousedown',function(){
						var _goToScrollPos = $('.navImagenes').scrollTop()-300;
						$('.navImagenes').scrollTo(_goToScrollPos);
						displayArrowsScrolled( _goToScrollPos, 0, maxScroll);
					});

					$('.arrowBot').on('mousedown',function(){
						var _goToScrollPos = $('.navImagenes').scrollTop()+300;
						$('.navImagenes').scrollTo(_goToScrollPos);
						displayArrowsScrolled( _goToScrollPos, 0, maxScroll);
					});
				}	
			}
		    //Determinar altura
			if($(window).width()>767){
			var alturaMedSection = ($(window).height()-$('.medSection').eq(num).height())/2.5;
			$('.medSection').eq(num).css('top', alturaMedSection+'px');
			}
		}
		//Scroll del menu navegador entre imagenes para centrar la elegida
		var navImagenesHeight = $('.navImagenes').height();
		var elemNavImagenesHeight = $('.elemNavImagenes').height()+10;
		var maxScroll = $('.elemNavImagenes').length * elemNavImagenesHeight - navImagenesHeight - 5;
		var _goToScrollPos = elemNavImagenesHeight * $i - (navImagenesHeight/2 - elemNavImagenesHeight/2);
		$('.navImagenes').scrollTo(_goToScrollPos);
		displayArrowsScrolled( _goToScrollPos, 0, maxScroll);
	}

/*Nueva Galeria*/

	//Crear flechas window.width<768
	function sideArrows(num){
		if($(window).width()<768){
			if($('.elemGaleria img').length>1){
				if(!$('.arrowLeft').length){
					$('.bigSection').append($('<div>', {class: 'arrowLeft arrowHor'})
						.on('click',function(){
							sideArrowTo(num, -1);
						}));
					$('.bigSection').append($('<div>', {class: 'arrowRight arrowHor'})
						.on('click',function(){
							sideArrowTo(num, 1);
						}));	
				}
			}
		}
	}

	function sideArrowTo(num, directionN){
		if (!$('.newStreetStyle').length>0) $('.medSection').css('display','none');
		if (!$('.newStreetStyle').length>0) $('.medSection').css('opacity','0');
		$('.arrowHor').remove();
		$numArrowLeft=(num-directionN+$l)%$l;
		($('.newStreetStyle').length>0) ? openNewMedsection($numArrowLeft) : openMedsection($numArrowLeft);
		if (!$('.newStreetStyle').length>0) ownResize();
	}
	function displayArrowsScrolled (scrollPosition, limitDown, limitUp){
		if ( scrollPosition < limitDown ){
			$('.arrowTop').css('display','none');
		}else{
			$('.arrowTop').css('display','inline-block');
		}
		if ( scrollPosition > limitUp ){
			$('.arrowBot').css('display','none');
		}else{
			$('.arrowBot').css('display','inline-block');
		}
	}

/*Final Vieja Galeria*/
	
$( document ).ready(
	function createNewStreetGallery(){
	//Crear contenedor Galeria
		$('body').append($('<div>', {class: 'bigCover'}).on('click',function(){ closeStreetGallery(); }));
		$('body').append($('<div>', {class: 'bigSection'}));

	//Crear boton cerrar		
		$('.bigSection').append($('<div>', {class: 'quitBigSection'}).on('click',function(){ closeStreetGallery(); }));

	//Funcionalidad al .menuGaleria
		$imagenes = $('.streetGaleria').find('li').addClass('elemGaleria').on('click',function(){
			$l=$($imagenes).length;
			$i=$(this).parent().children().index(this);
			$('.medSection').css('display','none');
			$('.medSection').css('opacity','0');
			$('.bigSection').css('display','inline-block');
			$('.bigCover').css('display','block');
			openNewMedsection($i);
		});

		$('.bigSection').addClass('newStreetStyle').append($('<div>', {class: 'medSection', id: 'medSection'})
			.append($('<section>', {class: 'container-wrap main-color'})
				.append($('<div>', {class: 'section-container container'})
					.append($('<div>', {class: 'row'})
						.append($('<div>', {class: 'col-sm-6 publiGaleria wpb_column'})
							.append($('ins.adsbygoogle.streetStyleAdv').clone().addClass('insideGallery')))
						.append($('<div>', {class: 'col-sm-6 wpb_column photoCol'})
							.append($('<div>', {class: 'wpb_wrapper'})
								.append($('<div>', { class: 'wpb_single_image'})
									.append($('<div>', {class: 'wpb_wrapper'})
										.append($('.elemGaleria img').eq(0).clone())))
								.append($('<div>', { class: 'kleo_text_column wpb_content_element'})
									.append($('<div>', {class: 'wpb_wrapper'})
										.append('<p class="nameStreetStyled">'+ $('.elemGaleria img').eq(0).attr('alt') +'</p>' )))))
							))));

		$('ins.adsbygoogle.streetStyleAdv:not(.insideGallery)').remove();

	//ShareButtons
		createShareButtons(0);

		var srcForReplace = $('.bigSection #medSection img').attr('src');
		var replaced = srcForReplace.replace('-150x150','');
		$('.bigSection #medSection img').attr('src', replaced);
		$('.bigSection #medSection img').attr('width', '600');
		$('.bigSection #medSection img').attr('height', '600');
		$('.bigSection #medSection img').removeClass('attachment-thumbnail');

	});
	function closeStreetGallery(){
		$('.medSection').css('display','none');
		$('.bigSection').css({'display':'none'});
		$('.bigCover').css('display','none');
		$('.bigSection').remove('.arrowNav');
		$('.pagination-sticky').css('visibility','visible');
		$('.kleo-go-top').css('visibility','visible');
	}
	function openNewMedsection(num){
		$i=num;
		$('.pagination-sticky').css('visibility','hidden');
		$('.kleo-go-top').css('visibility','hidden');
		$(window).scrollTop(0);
		$('#medSection').css('display','block');
		changeImage($i);
		changeName($i);
		changeShareButtons($i);
		$('#medSection').css('opacity','1')
		$('#medSection').find('.wpb_wrapper').eq(0).find('img').css({'opacity':'1'});
		if($('.elemGaleria img').length>1){
			createMenuGaleria($i);
		}
		sideArrows(num);

		galleryStylesResponsive();
	}
	function changeImage(num){
		var srcForReplace = $('.elemGaleria img').eq(num).attr('src');
		var replaced = srcForReplace.replace('-150x150','');
		$('.bigSection #medSection img').attr('src', replaced);
	}
	function changeName(num){
		$('.nameStreetStyled').text($('.elemGaleria img').eq(num).attr('alt'));
	}
	function changeShareButtons(num){
		location_url = getLocationToShare(num);
		$('.post_share_facebook').attr('href', "http://www.facebook.com/sharer.php?u=" + encodeURIComponent(location_url) );
		$('.kleo-googleplus a').attr('href', "https://plus.google.com/share?url=" + encodeURIComponent(location_url) );
	}
	function galleryStylesResponsive(){
		//Shadow Effect
		var shadowHeight = $('.medSection').find('.kleo_text_column').height();
		$('.medSection').find('.wpb_single_image').css('box-shadow','0px 0px 20px #000, 0px ' + shadowHeight + 'px 20px #000');
		$('.medSection').find('.wpb_raw_html').css('box-shadow','0px 0px 20px #000, 0px ' + shadowHeight + 'px 20px #000');

		//Determinar altura
		if($(window).width()>767){
			var alturaMedSection = ($(window).height()-$('.medSection').height())/2.5;
			$('.medSection').css('top', alturaMedSection+'px');
			$('.navImagenes').css({'height': $(window).height()-150+'px', 'top':'80px'});
			$('.arrowBot').css('top', $(window).height()-70+'px');
			$('.arrowBot').css('height',$('.arrowBot').width());
			$('.arrowTop').css('top', '10px');
			$('.arrowTop').css('height',$('.arrowTop').width());
		}
	}
	//Crear menu galeria
	function createMenuGaleria(num){
		if(!$('.elemNavImagenes').length){
			$('.bigSection').prepend($('<div>', {class: 'navImagenes'}));
			for(var i=0;i<$('.elemGaleria img').length;i++){
				$('.elemGaleria img').eq(i).clone().addClass('elemNavImagenes').appendTo('.navImagenes').on('click', function(){
					$('.medSection').css('display','none');
					$('.medSection').css('opacity','0');
					$i=$(this).parent().children().index(this);
					openNewMedsection($i);
				});
			}
		}	
		$('.elemNavImagenes').css('border-color','white');
		$('.elemNavImagenes').eq($i).css('border-color','#902828');

		if($(window).height()*0.7<$('.elemNavImagenes').length*$('.elemNavImagenes').height()){
			if(!$('.arrowTop').length){
				$('.bigSection').prepend($('<div>', {class: 'arrowTop arrowNav'}));
				$('.arrowTop').css('height',$('.arrowTop').width());
				$('.bigSection').prepend($('<div>', {class: 'arrowBot arrowNav'}));
				$('.arrowBot').css('height',$('.arrowBot').width());

				var navImagenesHeight = $('.navImagenes').height();
				var elemNavImagenesHeight = $('.elemNavImagenes').height()+10;
				var maxScroll = $('.elemNavImagenes').length * elemNavImagenesHeight - navImagenesHeight - 5;

				$('.arrowTop').on('mousedown',function(){
					var _goToScrollPos = $('.navImagenes').scrollTop()-300;
					$('.navImagenes').scrollTo(_goToScrollPos);
					displayArrowsScrolled( _goToScrollPos, 0, maxScroll);
				});

				$('.arrowBot').on('mousedown',function(){
					var _goToScrollPos = $('.navImagenes').scrollTop()+300;
					$('.navImagenes').scrollTo(_goToScrollPos);
					displayArrowsScrolled( _goToScrollPos, 0, maxScroll);
				});
			}	
		}

		//Scroll del menu navegador entre imagenes para centrar la elegida
		var navImagenesHeight = $('.navImagenes').height();
		var elemNavImagenesHeight = $('.elemNavImagenes').height()+10;
		var maxScroll = $('.elemNavImagenes').length * elemNavImagenesHeight - navImagenesHeight - 5;
		var _goToScrollPos = elemNavImagenesHeight * $i - (navImagenesHeight/2 - elemNavImagenesHeight/2);
		$('.navImagenes').scrollTo(_goToScrollPos);
		displayArrowsScrolled( _goToScrollPos, 0, maxScroll);
	}

	//Crear botones de compartir
	function createShareButtons(num){
			location_url = getLocationToShare(num);

			$('.nameStreetStyled').parent().append($('<p>').attr({class: 'share-links'})
				.append($('<span>').attr({class: 'kleo-facebook'})
					.append($('<a>').attr({class: 'post_share_facebook'}).on('click', function(){ javascript:window.open(this.href, //http://www.facebook.com/sharer.php?u=https://www.facebook.com/photo.php?fbid=481019152029911
					'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=220,width=600');return false;}).attr('href', "http://www.facebook.com/sharer.php?u=" + encodeURIComponent(location_url) )
						.append($('<i class="fa fa-facebook"></i>'))
						))
				.append($('<span>').attr({class: 'kleo-twitter'})
					.append($('<a>').attr({class: 'post_share_twitter'}).on('click', function(){ javascript:window.open(this.href,
					'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=260,width=600');return false;}).attr('href', "https://twitter.com/share?url=" + window.location.href)
						.append($('<i class="fa fa-twitter"></i>'))
						))
				.append($('<span>').attr({class: 'kleo-googleplus'})
					.append($('<a>').on('click', function(){ javascript:window.open(this.href,
					'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;}).attr('href', "https://plus.google.com/share?url=" + encodeURIComponent(location_url) )
						.append($('<i class="fa fa-google-plus"></i>'))
						)));
	}
	function getLocationToShare(num){
		var img_src_to_share = $('.elemGaleria img').eq(num).attr('src');
		img_src_to_share = img_src_to_share.split("uploads/").pop();
		//img_src_to_share = img_src_to_share.replace('/','%2F');
		var name_st = $('.nameStreetStyled').html();
		name_st = name_st.replace(' ', '_');

		var location_url = window.location.href;
		if (location_url.search("ph=")<0){
			location_url = location_url + '?ph=';
		}else{
			location_url = location_url.substring(0, location_url.search('=')+1);
		}
		location_url = location_url.replace('https','http');
		location_url += img_src_to_share + "&nm_st=" + name_st;
		return location_url;
	}
	
	if($('.streetGaleria').length){
		createNewStreetGallery();
		$(window).resize(function () {
			sideArrows(0);
			galleryStylesResponsive();
		})
	}
	/*Nueva Galeria*/
	if($('.menuGaleria').length){
		createStreetGallery();
	}
/*Final Nueva Galeria*/
})(jQuery);
}
