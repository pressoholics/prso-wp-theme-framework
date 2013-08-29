;(function ($, window, undefined) {
  'use strict';

  var $doc = $(document),
      Modernizr = window.Modernizr;

  $(document).ready(function() {
    $.fn.foundationAlerts           ? $doc.foundationAlerts() : null;
    $.fn.foundationButtons          ? $doc.foundationButtons() : null;
    $.fn.foundationAccordion        ? $doc.foundationAccordion() : null;
    $.fn.foundationNavigation       ? $doc.foundationNavigation() : null;
    $.fn.foundationTopBar           ? $doc.foundationTopBar() : null;
    $.fn.foundationCustomForms      ? $doc.foundationCustomForms() : null;
    $.fn.foundationMediaQueryViewer ? $doc.foundationMediaQueryViewer() : null;
    $.fn.foundationTabs             ? $doc.foundationTabs({callback : $.foundation.customForms.appendCustomMarkup}) : null;
    $.fn.foundationTooltips         ? $doc.foundationTooltips() : null;
    $.fn.foundationMagellan         ? $doc.foundationMagellan() : null;
    $.fn.foundationClearing         ? $doc.foundationClearing() : null;
    $.fn.placeholder                ? $('input, textarea').placeholder() : null;
  });

  // UNCOMMENT THE LINE YOU WANT BELOW IF YOU WANT IE8 SUPPORT AND ARE USING .block-grids
  // $('.block-grid.two-up>li:nth-child(2n+1)').css({clear: 'both'});
  // $('.block-grid.three-up>li:nth-child(3n+1)').css({clear: 'both'});
  // $('.block-grid.four-up>li:nth-child(4n+1)').css({clear: 'both'});
  // $('.block-grid.five-up>li:nth-child(5n+1)').css({clear: 'both'});

  // Hide address bar on mobile devices (except if #hash present, so we don't mess up deep linking).
  if (Modernizr.touch && !window.location.hash) {
    $(window).load(function () {
      setTimeout(function () {
        window.scrollTo(0, 1);
      }, 0);
    });
  }

})(jQuery, this);

jQuery(document).ready(function ($) {

  /* Use this js doc for all application specific JS */
  
  //Start Orbit banner in #featured div
  $("#featured").orbit({
  	  animation: 'horizontal-push',     // fade, horizontal-slide, vertical-slide, horizontal-push, vertical-push
      animationSpeed: 600,        		// how fast animtions are
      timer: true,            			// true or false to have the timer
      advanceSpeed: 4000,         		// if timer is enabled, time between transitions
      pauseOnHover: true,        		// if you hover pauses the slider
      startClockOnMouseOut: false,    	// if clock should start on MouseOut
      startClockOnMouseOutAfter: 1000,  // how long after MouseOut should the timer start again
      directionalNav: true,         	// manual advancing directional navs
      directionalNavRightText: 'Right', // text of right directional element for accessibility
      directionalNavLeftText: 'Left', 	// text of left directional element for accessibility
      captions: true,           		// do you want captions?
      captionAnimation: 'slideOpen',       	// fade, slideOpen, none
      captionAnimationSpeed: 600,     	// if so how quickly should they animate in
      resetTimerOnClick: false,      	// true resets the timer instead of pausing slideshow progress on manual navigation
      bullets: true,           			// true or false to activate the bullet navigation
      bulletThumbs: false,        		// thumbnails for the bullets
      bulletThumbLocation: '',      	// location from this file where thumbs will be
      afterSlideChange: $.noop,   		// empty function
      afterLoadComplete: $.noop, 		//callback to execute after everything has been loaded
      fluid: '16x6',
      centerBullets: true    			// center bullet nav with js, turn this off if you want to position the bullet nav manually
  });
  
  //Start Orbit banner in #featured-shortcode div
  $("#featured-shortcode").orbit({
  	  animation: 'horizontal-push',     // fade, horizontal-slide, vertical-slide, horizontal-push, vertical-push
      animationSpeed: 600,        		// how fast animtions are
      timer: false,            			// true or false to have the timer
      advanceSpeed: 4000,         		// if timer is enabled, time between transitions
      pauseOnHover: true,        		// if you hover pauses the slider
      startClockOnMouseOut: false,    	// if clock should start on MouseOut
      startClockOnMouseOutAfter: 1000,  // how long after MouseOut should the timer start again
      directionalNav: true,         	// manual advancing directional navs
      directionalNavRightText: 'Right', // text of right directional element for accessibility
      directionalNavLeftText: 'Left', 	// text of left directional element for accessibility
      captions: true,           		// do you want captions?
      captionAnimation: 'slideOpen',       	// fade, slideOpen, none
      captionAnimationSpeed: 600,     	// if so how quickly should they animate in
      resetTimerOnClick: false,      	// true resets the timer instead of pausing slideshow progress on manual navigation
      bullets: false,           			// true or false to activate the bullet navigation
      bulletThumbs: false,        		// thumbnails for the bullets
      bulletThumbLocation: '',      	// location from this file where thumbs will be
      afterSlideChange: $.noop,   		// empty function
      afterLoadComplete: $.noop, 		//callback to execute after everything has been loaded
      fluid: '16x6',
      centerBullets: true    			// center bullet nav with js, turn this off if you want to position the bullet nav manually
  });
  
  // Add animation effects to content images on scroll - Except IE6,7,8 (see feature detect logic)
  if( $.support.cssFloat ) {
	  
	$("#main img").css( 'visibility', 'hidden' );
	$("#main img").waypoint( function() {
	                                    
		$(this).delay(100).queue(function(next){
		    
		    if( !$(this).hasClass("animated") ) {
		    	//See _app-animate.scss for animation options
		        $(this).addClass("animated fadeIn");
		    }
		    
		    next();
		
		});                                    
	
	}, { offset: "99%" });
	  
  }
  
  // add foundation classes and color based on how many times tag is used
	function addFoundationClass(thisObj) {
	  var title = $(thisObj).attr('title');
	  if (title) {
	    var titles = title.split(' ');
	    if (titles[0]) {
	      var num = parseInt(titles[0]);
	      if (num > 0)
	      	$(thisObj).addClass('');
	      if (num > 2 && num < 4)
	        $(thisObj).addClass('success');
	      if (num > 5)
	        $(thisObj).addClass('alert');
	    }
	  }
	  return true;
	}

	$("#tag-cloud a").each(function() {
	    addFoundationClass(this);
	    return true;
	});
	
	$("ol.commentlist a.comment-reply-link").each(function() {
		$(this).addClass('button blue radius small');
		return true;
	});
	
	// Input placeholder text fix for IE
	$('[placeholder]').focus(function() {
	  var input = $(this);
	  if (input.val() == input.attr('placeholder')) {
		input.val('');
		input.removeClass('placeholder');
	  }
	}).blur(function() {
	  var input = $(this);
	  if (input.val() == '' || input.val() == input.attr('placeholder')) {
		input.addClass('placeholder');
		input.val(input.attr('placeholder'));
	  }
	}).blur();
	
	// Prevent submission of empty form
	$('[placeholder]').parents('form').submit(function() {
	  $(this).find('[placeholder]').each(function() {
		var input = $(this);
		if (input.val() == input.attr('placeholder')) {
		  input.val('');
		}
	  })
	});

});

/* imgsizer (flexible images for fluid sites) */
var imgSizer={Config:{imgCache:[],spacer:"/path/to/your/spacer.gif"},collate:function(aScope){var isOldIE=(document.all&&!window.opera&&!window.XDomainRequest)?1:0;if(isOldIE&&document.getElementsByTagName){var c=imgSizer;var imgCache=c.Config.imgCache;var images=(aScope&&aScope.length)?aScope:document.getElementsByTagName("img");for(var i=0;i<images.length;i++){images[i].origWidth=images[i].offsetWidth;images[i].origHeight=images[i].offsetHeight;imgCache.push(images[i]);c.ieAlpha(images[i]);images[i].style.width="100%";}
if(imgCache.length){c.resize(function(){for(var i=0;i<imgCache.length;i++){var ratio=(imgCache[i].offsetWidth/imgCache[i].origWidth);imgCache[i].style.height=(imgCache[i].origHeight*ratio)+"px";}});}}},ieAlpha:function(img){var c=imgSizer;if(img.oldSrc){img.src=img.oldSrc;}
var src=img.src;img.style.width=img.offsetWidth+"px";img.style.height=img.offsetHeight+"px";img.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+src+"', sizingMethod='scale')"
img.oldSrc=src;img.src=c.Config.spacer;},resize:function(func){var oldonresize=window.onresize;if(typeof window.onresize!='function'){window.onresize=func;}else{window.onresize=function(){if(oldonresize){oldonresize();}
func();}}}}