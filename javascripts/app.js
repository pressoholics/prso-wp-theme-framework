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
	
	
  /* TABS --------------------------------- */
  /* Remove if you don't need :) */

  function activateTab($tab) {
    var $activeTab = $tab.closest('dl').find('dd.active'),
        contentLocation = $tab.children('a').attr("href") + 'Tab';

    // Strip off the current url that IE adds
    contentLocation = contentLocation.replace(/^.+#/, '#');

    //Make Tab Active
    $activeTab.removeClass('active');
    $tab.addClass('active');

    //Show Tab Content
    $(contentLocation).closest('.tabs-content').children('li').removeClass('active').hide();
    $(contentLocation).css('display', 'block').addClass('active');
  }

  $('dl.tabs dd a').on('click.fndtn', function (event) {
    activateTab($(this).parent('dd'));
  });

  if (window.location.hash) {
    activateTab($('a[href="' + window.location.hash + '"]').parent('dd'));
    $.foundation.customForms.appendCustomMarkup();
  }
  
  /* Accordion --------------------------------- */
  /* Remove if you don't need :) */
  ;(function ($, window, undefined){
	  'use strict';
	
	  $.fn.foundationAccordion = function (options) {
	
	    $('.accordion li', this).on('click.fndtn', function () {
	    var p = $(this).parent(); //changed this
	      var flyout = $(this).children('.content').first();
	      $('.content', p).not(flyout).hide().parent('li').removeClass('active'); //changed this
	      flyout.show(0, function () {
	        flyout.parent('li').addClass('active');
	      });
	    });
	
	  };
	
	})( jQuery, this );
  
  $(document).foundationAccordion();
  
  /* ALERT BOXES ------------ */
  $(".alert-box").delegate("a.close", "click", function(event) {
    event.preventDefault();
    $(this).closest(".alert-box").fadeOut(function(event){
      $(this).remove();
    });
  });

  /* PLACEHOLDER FOR FORMS ------------- */
  /* Remove this and jquery.placeholder.min.js if you don't need :) */
  $('input, textarea').placeholder();

  /* TOOLTIPS ------------ */
  $(this).tooltips();

  /* UNCOMMENT THE LINE YOU WANT BELOW IF YOU WANT IE6/7/8 SUPPORT AND ARE USING .block-grids */
  //  $('.block-grid.two-up>li:nth-child(2n+1)').css({clear: 'left'});
  //  $('.block-grid.three-up>li:nth-child(3n+1)').css({clear: 'left'});
  //  $('.block-grid.four-up>li:nth-child(4n+1)').css({clear: 'left'});
  //  $('.block-grid.five-up>li:nth-child(5n+1)').css({clear: 'left'});


  /* DROPDOWN NAV ------------- */

  var lockNavBar = false;
  /* Windows Phone, sadly, does not register touch events :( */
  if (Modernizr.touch || navigator.userAgent.match(/Windows Phone/i)) {
    $('.nav-bar a.flyout-toggle').on('click.fndtn touchstart.fndtn', function(e) {
      e.preventDefault();
      var flyout = $(this).siblings('.flyout').first();
      if (lockNavBar === false) {
        $('.nav-bar .flyout').not(flyout).slideUp(500);
        flyout.slideToggle(500, function(){
          lockNavBar = false;
        });
      }
      lockNavBar = true;
    });
    $('.nav-bar>li.has-flyout').addClass('is-touch');
  } else {
    $('.nav-bar>li.has-flyout').hover(function() {
      $(this).children('.flyout').show();
    }, function() {
      $(this).children('.flyout').hide();
    });
  }

  /* DISABLED BUTTONS ------------- */
  /* Gives elements with a class of 'disabled' a return: false; */
  $('.button.disabled').on('click.fndtn', function (event) {
    event.preventDefault();
  });
  

  /* SPLIT BUTTONS/DROPDOWNS */
  $('.button.dropdown > ul').addClass('no-hover');

  $('.button.dropdown').on('click.fndtn touchstart.fndtn', function (e) {
    e.stopPropagation();
  });
  $('.button.dropdown.split span').on('click.fndtn touchstart.fndtn', function (e) {
    e.preventDefault();
    $('.button.dropdown').not($(this).parent()).children('ul').removeClass('show-dropdown');
    $(this).siblings('ul').toggleClass('show-dropdown');
  });
  $('.button.dropdown').not('.split').on('click.fndtn touchstart.fndtn', function (e) {
    $('.button.dropdown').not(this).children('ul').removeClass('show-dropdown');
    $(this).children('ul').toggleClass('show-dropdown');
  });
  $('body, html').on('click.fndtn touchstart.fndtn', function () {
    $('.button.dropdown ul').removeClass('show-dropdown');
  });

  // Positioning the Flyout List
  var normalButtonHeight  = $('.button.dropdown:not(.large):not(.small):not(.tiny)').outerHeight() - 1,
      largeButtonHeight   = $('.button.large.dropdown').outerHeight() - 1,
      smallButtonHeight   = $('.button.small.dropdown').outerHeight() - 1,
      tinyButtonHeight    = $('.button.tiny.dropdown').outerHeight() - 1;

  $('.button.dropdown:not(.large):not(.small):not(.tiny) > ul').css('top', normalButtonHeight);
  $('.button.dropdown.large > ul').css('top', largeButtonHeight);
  $('.button.dropdown.small > ul').css('top', smallButtonHeight);
  $('.button.dropdown.tiny > ul').css('top', tinyButtonHeight);
  
  $('.button.dropdown.up:not(.large):not(.small):not(.tiny) > ul').css('top', 'auto').css('bottom', normalButtonHeight - 2);
  $('.button.dropdown.up.large > ul').css('top', 'auto').css('bottom', largeButtonHeight - 2);
  $('.button.dropdown.up.small > ul').css('top', 'auto').css('bottom', smallButtonHeight - 2);
  $('.button.dropdown.up.tiny > ul').css('top', 'auto').css('bottom', tinyButtonHeight - 2);

  /* CUSTOM FORMS */
  $.foundation.customForms.appendCustomMarkup();

});

/* imgsizer (flexible images for fluid sites) */
var imgSizer={Config:{imgCache:[],spacer:"/path/to/your/spacer.gif"},collate:function(aScope){var isOldIE=(document.all&&!window.opera&&!window.XDomainRequest)?1:0;if(isOldIE&&document.getElementsByTagName){var c=imgSizer;var imgCache=c.Config.imgCache;var images=(aScope&&aScope.length)?aScope:document.getElementsByTagName("img");for(var i=0;i<images.length;i++){images[i].origWidth=images[i].offsetWidth;images[i].origHeight=images[i].offsetHeight;imgCache.push(images[i]);c.ieAlpha(images[i]);images[i].style.width="100%";}
if(imgCache.length){c.resize(function(){for(var i=0;i<imgCache.length;i++){var ratio=(imgCache[i].offsetWidth/imgCache[i].origWidth);imgCache[i].style.height=(imgCache[i].origHeight*ratio)+"px";}});}}},ieAlpha:function(img){var c=imgSizer;if(img.oldSrc){img.src=img.oldSrc;}
var src=img.src;img.style.width=img.offsetWidth+"px";img.style.height=img.offsetHeight+"px";img.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+src+"', sizingMethod='scale')"
img.oldSrc=src;img.src=c.Config.spacer;},resize:function(func){var oldonresize=window.onresize;if(typeof window.onresize!='function'){window.onresize=func;}else{window.onresize=function(){if(oldonresize){oldonresize();}
func();}}}}