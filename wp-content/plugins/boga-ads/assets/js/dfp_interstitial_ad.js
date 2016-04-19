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
        jQuery('#interstitialModal').modal({show:true, backdrop: 'static'});
        localStorage.setItem('bogatitial', 1);
    }
});