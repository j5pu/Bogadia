var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];
(function() {
    var gads = document.createElement('script');
    gads.async = true;
    gads.type = 'text/javascript';
    var useSSL = 'https:' == document.location.protocol;
    gads.src = (useSSL ? 'https:' : 'http:') +
        '//www.googletagservices.com/tag/js/gpt.js';
    var node = document.getElementsByTagName('script')[0];
    node.parentNode.insertBefore(gads, node);
})();
googletag.cmd.push(function() {
    var width = window.innerWidth || document.documentElement.clientWidth;
    if (width >= 1635) {
        googletag.defineSlot('/61601326/u_lateral_derecho', [300, 600], 'div-gpt-ad-1457625928117-1').addService(googletag.pubads());
        googletag.defineSlot('/61601326/u_lateral_izquierdo', [300, 600], 'div-gpt-ad-1457625928117-2').addService(googletag.pubads());
        jQuery("#div-gpt-ad-1457722273425-0").hide();
        jQuery("#div-gpt-ad-1457722273425-1").hide();
    }
    if (width >= 1238 && width <= 1635) {
        googletag.defineSlot('/61601326/u_lateral_derecho_pequeño', [120, 600], 'div-gpt-ad-1457722273425-0').addService(googletag.pubads());
        googletag.defineSlot('/61601326/u_lateral_izquierda_pequeño', [120, 600], 'div-gpt-ad-1457722273425-1').addService(googletag.pubads());
        jQuery("#div-gpt-ad-1457625928117-1").hide();
        jQuery("#div-gpt-ad-1457625928117-2").hide();
    }
/*    if (width >= 991 && width <= 1237) {
        googletag.defineSlot('/61601326/cabecera', [970, 90], 'div-gpt-ad-1457985102265-0').addService(googletag.pubads());
        var ls = document.createElement('link');
        ls.rel="stylesheet";
        ls.href="/wp-content/themes/kleo-child/assets/css/cabecera_ad.css";
        document.getElementsByTagName('head')[0].appendChild(ls);
    }*/
    if (width <= 990){
        googletag.defineSlot('/61601326/mayor', [300, 600], 'div-gpt-ad-1457972362060-0').addService(googletag.pubads());
    }
    googletag.pubads().enableSingleRequest();
    googletag.enableServices();
});