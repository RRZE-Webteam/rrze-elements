/**
 * RRZE jQuery FlexSlider 1.0.0
 * use jQuery FlexSlider v2.7.2
 * RRZE Webteam
 */

jQuery(document).ready(function($) {
    $('.flexslider.carousel').flexslider({
        animation: "slide",
        animationLoop: true,
        itemWidth: 300,
        itemMargin: 5,
        pausePlay: true,
         start: function(){$(".flexslider").flexslider(object_name.start)}
    });
    $('.flexslider:not(carousel)').flexslider({
        pausePlay: true,
        start:function(){$(".flexslider").flexslider(object_name.start)}
    });
});
