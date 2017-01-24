var lastScroll;
var initialLogoHeight,initialLogoTop;
var initialHeaderHeight;


jQuery(document).ready(function() {
  initialLogoHeight =  jQuery(".logo-menu").height();
  initialHeaderHeight =  jQuery(".menubar-container").height();
  initialLogoTop = parseInt(jQuery(".logo-menu").css('top'), 10);
  lastScroll = jQuery(window).scrollTop();
  jQuery(window).on('scroll',fixHeaderHeight);
  fixHeaderHeight();
  
  jQuery(".help-sub-menu a, .current-menu-item a").on("click", function(e) {
    e.preventDefault();
    history.pushState({}, "", this.href);
    jQuery("html, body").stop().animate({scrollTop:jQuery(window.location.hash).offset().top - 220}, 1500, 'swing', function() { 
  });
  });

  /*if (location.hash) {
    setTimeout(function() {

      window.scrollTo(0, 0);
    }, 1);
  }*/
  
  /*jQuery('#primary-menu > li').hover(function() {
    var el = this;
    if(!jQuery('.sub-menu', el).is(":visible")){
      jQuery('.sub-menu').stop(true, false);
      jQuery('.sub-menu').hide();
       jQuery('.sub-menu', el).show(); 
  	  fixSubMenuPosition();
    }
  }, function() {
    
  });
  jQuery('#primary-menu').hover(function(){},function(){
    if(!jQuery('.current_page_item > .sub-menu').is(":visible")){
    	jQuery('.sub-menu').stop(true, false);
      jQuery('.sub-menu').hide();
      jQuery('.current_page_item > .sub-menu').show();
  		fixSubMenuPosition();
    }
  });*/
  fixSubMenuPosition();
  jQuery(window).resize(function(){fixSubMenuPosition();});
});
function fixSubMenuPosition(){
  jQuery('.sub-menu').each(function(){
      if(screen.width < 768) {
          jQuery(this).css("right", "");
      } else {
          jQuery(this).css("right", (jQuery(window).width() / 2.0 - jQuery(this).width() / 2.0) + "px");
      }
  });
  
  jQuery('.help-sub-menu').each(function(){
      if(screen.width < 768){
          jQuery(this).css("right", "");
      } else {
          jQuery(this).css("right", (jQuery('#help-menu-container').width()/2.0 -jQuery(this).width()/2.0) + "px");
      }
  });
}
function fixHeaderHeight(){
    var currentScroll = jQuery(window).scrollTop();
  var adminBarHeight = 0;
  if( jQuery("#wpadminbar").length){
    adminBarHeight =  jQuery("#wpadminbar").outerHeight();
  }
    
    if((lastScroll < currentScroll && jQuery(".menubar-container").height() > 100)
       || lastScroll > currentScroll ){
      if(currentScroll/10 > initialHeaderHeight - 100)
      	currentScroll = (initialHeaderHeight - 100)*10;
     // jQuery(".logo-menu").css('height', initialLogoHeight - currentScroll/10);
        jQuery(".logo-menu").css('top', initialLogoTop - currentScroll/10);
    	jQuery(".menubar-container").css('height', initialHeaderHeight - currentScroll/10);
      jQuery(".site-header").css('top', adminBarHeight + 'px');
    		lastScroll = currentScroll;
    }
}