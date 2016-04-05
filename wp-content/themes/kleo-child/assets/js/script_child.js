/*Seccion active (Se pinta la categoría del menú correspondiente al post visualizado*/
	$category=$("meta[property='article:section']").attr('content');

	if($('.product').length>0 || $('body.woocommerce-page').length>0){
		$category="Tienda";
	}
	if($('.checkout-steps').length>0 || $('p.cart-empty').length>0){
		$('.cart-contents').find('i').css('color','#F66 !important');
		document.styleSheets[0].addRule('.icon-basket-full-alt:before','color:#f66');
	}
	if(window.location.href.search("disenadores")>0 && $('body.woocommerce-page').length>0){
		$('#enlace-disenadores-bogadia').css('color','#f66');
	}
	if(window.location.href.search("colecciones")>0 && $('body.woocommerce-page').length>0){
		$('#enlace-colecciones-bogadia').css('color','#f66');
	}
	if(window.location.href.search("productos-bogadia")>0 || $('body.single-product').length>0){
		$('#enlace-productos-bogadia').css('color','#f66');
	}
	if(window.location.href.search("por-que-bogadia")>0){
		$('#enlace-por-que-bogadia').css('color','#f66');
	}
	$('.navbar-nav').find("li a[title='"+$category+"']").css('color','#F66');
/*Final seccion active*/