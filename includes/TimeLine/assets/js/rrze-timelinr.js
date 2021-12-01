/* ----------------------------------
 RRZE jQuery TimeLinr 1.0.0
 based on jQuery Timelinr 0.9.55
 tested with jQuery v1.6+

 Copyright 2011, CSSLab.cl
 Free under the MIT license.
 http://www.opensource.org/licenses/mit-license.php

 instructions: http://www.csslab.cl/2011/08/18/jquery-timelinr/
 ---------------------------------- */

jQuery.fn.timelinr = function (options) {
    // default plugin settings

    settings = jQuery.extend({
        orientation: 'horizontal', // value: horizontal | vertical, default to horizontal
        containerDiv: '#timeline-1', // value: any HTML tag or #id, default to #timeline
        datesDiv: options.containerDiv + ' .dates', // value: any HTML tag or #id, default to #dates
        datesSelectedClass: 'selected', // value: any class, default to selected
        datesSpeed: 'normal', // value: integer between 100 and 1000 (recommended) or 'slow', 'normal' or 'fast'; default to normal
        issuesDiv: options.containerDiv + ' .issues', // value: any HTML tag or #id, default to #issues
        issuesSelectedClass: 'selected', // value: any class, default to selected
        issuesSpeed: 'normal', // value: integer between 100 and 1000 (recommended) or 'slow', 'normal' or 'fast'; default to fast
        issuesTransparency: 0.2, // value: integer between 0 and 1 (recommended), default to 0.2
        issuesTransparencySpeed: 500, // value: integer between 100 and 1000 (recommended), default to 500 (normal)
        prevButton: options.containerDiv + ' .prev', // value: any HTML tag or #id, default to #prev
        nextButton: options.containerDiv + ' .next', // value: any HTML tag or #id, default to #next
        arrowKeys: 'true', // value: true | false, default to false
        startAt: 1, // value: integer, default to 1 (first)
        autoPlay: 'false', // value: true | false, default to false
        autoPlayDirection: 'forward', // value: forward | backward, default to forward
        autoPlayPause: 3000, // value: integer (1000 = 1 seg), default to 2000 (2segs)
        fixedSize : 'true',
    }, options);

    jQuery(function () {
        // Add dates list
        var timeline = jQuery(options.containerDiv);
        var dates_content = '';
        var issues = jQuery(timeline).find("ul.issues");
        dates_content += "<ul class='dates'>";
        jQuery(issues).find("li").each(function(j) {
            if (jQuery(this).attr("name").match("^__")) {
                dates_content += "<li><a href='#" + jQuery(this).attr("name") + "'><span class=\"sr-only\">" + jQuery(this).attr("name") + "</span>&nbsp;&nbsp;&nbsp;&nbsp;</a></li>";
            } else {
                //dates_content += "<li><a href='#" + jQuery(this).attr("name") + "'>" +jQuery(this).attr("name") + "</a></li>";
                dates_content += "<li><a href='#" + jQuery(this).attr("name") + "'>" +jQuery(this).data("date") + "</a></li>";
            }
        });
        dates_content += "</ul>";
        jQuery(timeline).prepend(dates_content);

        // Checks if required elements exist on page before initializing timelinr | improvement since 0.9.55
        if (jQuery(settings.datesDiv).length > 0 && jQuery(settings.issuesDiv).length > 0) {
            if (options.fixedSize == 'true') {
                updateSize();
            }

            // setting variables... many of them
            var howManyDates = jQuery(settings.datesDiv + ' li').length;
            var howManyIssues = jQuery(settings.issuesDiv + ' li').length;
            var currentDate = jQuery(settings.datesDiv).find('a.' + settings.datesSelectedClass);
            var currentIssue = jQuery(settings.issuesDiv).find('li.' + settings.issuesSelectedClass);
            var widthContainer = jQuery(settings.containerDiv).width();
            var heightContainer = jQuery(settings.containerDiv).height();
            var widthIssues = jQuery(settings.issuesDiv).width();
            var heightIssues = jQuery(settings.issuesDiv).height();
            var widthIssue = jQuery(settings.containerDiv).width();
            var heightIssue = jQuery(settings.issuesDiv + ' li').height();
            var widthDates = jQuery(settings.datesDiv).width();
            var heightDates = jQuery(settings.datesDiv).height();
            var widthDate = jQuery(settings.datesDiv + ' li').width();
            var heightDate = jQuery(settings.datesDiv + ' li').height();
            var currentIndex = jQuery(settings.issuesDiv).find('li.' + settings.issuesSelectedClass).index();
            var prevHref = jQuery(settings.datesDiv + ' li').eq(currentIndex).find('a').attr('href');
            var nextHref = jQuery(settings.datesDiv + ' li').eq(currentIndex + 2).find('a').attr('href');

            // set positions!
            if (settings.orientation == 'horizontal') {
                jQuery(settings.issuesDiv).find("li").outerWidth(widthContainer);
                jQuery(settings.issuesDiv).width(widthIssue * howManyIssues);
                jQuery(settings.datesDiv).width(widthDate * howManyDates).css('marginLeft', widthContainer / 2 - widthDate / 2);
                var defaultPositionDates = parseInt(jQuery(settings.datesDiv).css('marginLeft').substring(0, jQuery(settings.datesDiv).css('marginLeft').indexOf('px')));
            } else if (settings.orientation == 'vertical') {
                heightIssue = +heightIssue + +100; //add padding
                jQuery(settings.issuesDiv).find("li").width(widthContainer - widthDates);
                jQuery(settings.issuesDiv).height(heightIssue * howManyIssues);
                jQuery(settings.datesDiv).height(heightDate * howManyDates).css('marginTop', heightDate);
                //jQuery(settings.datesDiv).height(heightDate * howManyDates).css('marginTop', heightContainer / 2 - heightDate / 2);
                var defaultPositionDates = parseInt(jQuery(settings.datesDiv).css('marginTop').substring(0, jQuery(settings.datesDiv).css('marginTop').indexOf('px')));
            }

            // insert hrefs
            var prevHref = jQuery(settings.datesDiv + ' li').eq(settings.startAt - 2).find('a').attr('href');
            var nextHref = jQuery(settings.datesDiv + ' li').eq(settings.startAt).find('a').attr('href');
            jQuery(settings.prevButton).attr('href',prevHref);
            jQuery(settings.nextButton).attr('href',nextHref);

            jQuery(settings.datesDiv + ' a').click(function (event) {
                //event.preventDefault();
                // first vars
                var whichIssue = jQuery(this).text();
                var currentIndex = jQuery(this).parent().prevAll().length;
                // moving the elements
                if (settings.orientation == 'horizontal') {
                    jQuery(settings.issuesDiv).animate({'marginLeft': -widthIssue * currentIndex}, {queue: false, duration: settings.issuesSpeed});
                } else if (settings.orientation == 'vertical') {
                    jQuery(settings.issuesDiv).animate({'marginTop': -heightIssue * currentIndex}, {queue: false, duration: settings.issuesSpeed});
                }
                jQuery(settings.issuesDiv + ' li').animate({'opacity': settings.issuesTransparency}, {queue: false, duration: settings.issuesSpeed}).removeClass(settings.issuesSelectedClass).eq(currentIndex).addClass(settings.issuesSelectedClass).fadeTo(settings.issuesTransparencySpeed, 1);
                // prev/next buttons now disappears on first/last issue | bugfix from 0.9.51: lower than 1 issue hide the arrows | bugfixed: arrows not showing when jumping from first to last date
                if (howManyDates == 1) {
                    jQuery(settings.prevButton + ',' + settings.nextButton).fadeOut('fast');
                } else if (howManyDates == 2) {
                    if (jQuery(settings.issuesDiv + ' li:first-child').hasClass(settings.issuesSelectedClass)) {
                        jQuery(settings.prevButton).fadeOut('fast');
                        jQuery(settings.nextButton).fadeIn('fast');
                    } else if (jQuery(settings.issuesDiv + ' li:last-child').hasClass(settings.issuesSelectedClass)) {
                        jQuery(settings.nextButton).fadeOut('fast');
                        jQuery(settings.prevButton).fadeIn('fast');
                    }
                } else {
                    if (jQuery(settings.issuesDiv + ' li:first-child').hasClass(settings.issuesSelectedClass)) {
                        jQuery(settings.nextButton).fadeIn('fast');
                        jQuery(settings.prevButton).fadeOut('fast');
                    } else if (jQuery(settings.issuesDiv + ' li:last-child').hasClass(settings.issuesSelectedClass)) {
                        jQuery(settings.prevButton).fadeIn('fast');
                        jQuery(settings.nextButton).fadeOut('fast');
                    } else {
                        jQuery(settings.nextButton + ',' + settings.prevButton).fadeIn('slow');
                    }
                }
                // now moving the dates
                jQuery(settings.datesDiv + ' a').removeClass(settings.datesSelectedClass);
                jQuery(this).addClass(settings.datesSelectedClass);
                if (settings.orientation == 'horizontal') {
                    jQuery(settings.datesDiv).animate({'marginLeft': defaultPositionDates - (widthDate * currentIndex)}, {queue: false, duration: 'settings.datesSpeed'});
                } else if (settings.orientation == 'vertical') {
                    jQuery(settings.datesDiv).animate({'marginTop': defaultPositionDates - (heightDate * currentIndex)}, {queue: false, duration: 'settings.datesSpeed'});
                }

                // insert hrefs
                var prevHref = jQuery(settings.datesDiv + ' li').eq(currentIndex - 1).find('a').attr('href');
                var nextHref = jQuery(settings.datesDiv + ' li').eq(currentIndex + 1).find('a').attr('href');
                jQuery(settings.prevButton).attr('href',prevHref);
                jQuery(settings.nextButton).attr('href',nextHref);
            });

            jQuery(settings.nextButton).bind('click', function (event) {
                //event.preventDefault();
                // bugixed from 0.9.54: now the dates gets centered when there's too much dates.
                var currentIndex = jQuery(settings.issuesDiv).find('li.' + settings.issuesSelectedClass).index();
                var prevHref = jQuery(settings.datesDiv + ' li').eq(currentIndex).find('a').attr('href');
                var nextHref = jQuery(settings.datesDiv + ' li').eq(currentIndex + 2).find('a').attr('href');
                if (settings.orientation == 'horizontal') {
                    var currentPositionIssues = parseInt(jQuery(settings.issuesDiv).css('marginLeft').substring(0, jQuery(settings.issuesDiv).css('marginLeft').indexOf('px')));
                    var currentIssueIndex = currentPositionIssues / widthIssue;
                    var currentPositionDates = parseInt(jQuery(settings.datesDiv).css('marginLeft').substring(0, jQuery(settings.datesDiv).css('marginLeft').indexOf('px')));
                    var currentIssueDate = currentPositionDates - widthDate;
                    if (currentPositionIssues <= -(widthIssue * howManyIssues - (widthIssue))) {
                        jQuery(settings.issuesDiv).stop();
                        jQuery(settings.datesDiv + ' li:last-child a').click();
                    } else {
                        if (!jQuery(settings.issuesDiv).is(':animated')) {
                            // bugixed from 0.9.52: now the dates gets centered when there's too much dates.
                            jQuery(settings.datesDiv + ' li').eq(currentIndex + 1).find('a').trigger('click');
                            jQuery(settings.prevButton).attr('href',prevHref);
                            jQuery(settings.nextButton).attr('href',nextHref);
                        }
                    }
                } else if (settings.orientation == 'vertical') {
                    var currentPositionIssues = parseInt(jQuery(settings.issuesDiv).css('marginTop').substring(0, jQuery(settings.issuesDiv).css('marginTop').indexOf('px')));
                    var currentIssueIndex = currentPositionIssues / heightIssue;
                    var currentPositionDates = parseInt(jQuery(settings.datesDiv).css('marginTop').substring(0, jQuery(settings.datesDiv).css('marginTop').indexOf('px')));
                    var currentIssueDate = currentPositionDates - heightDate;
                    if (currentPositionIssues <= -(heightIssue * howManyIssues - (heightIssue))) {
                        jQuery(settings.issuesDiv).stop();
                        jQuery(settings.datesDiv + ' li:last-child a').click();
                    } else {
                        if (!jQuery(settings.issuesDiv).is(':animated')) {
                            // bugixed from 0.9.54: now the dates gets centered when there's too much dates.
                            jQuery(settings.prevButton).attr('href',prevHref);
                            jQuery(settings.nextButton).attr('href',nextHref);
                            jQuery(settings.datesDiv + ' li').eq(currentIndex + 1).find('a').trigger('click');
                        }
                    }
                }
                // prev/next buttons now disappears on first/last issue | bugfix from 0.9.51: lower than 1 issue hide the arrows
                if (howManyDates == 1) {
                    jQuery(settings.prevButton + ',' + settings.nextButton).fadeOut('fast');
                } else if (howManyDates == 2) {
                    if (jQuery(settings.issuesDiv + ' li:first-child').hasClass(settings.issuesSelectedClass)) {
                        jQuery(settings.prevButton).fadeOut('fast');
                        jQuery(settings.nextButton).fadeIn('fast');
                    } else if (jQuery(settings.issuesDiv + ' li:last-child').hasClass(settings.issuesSelectedClass)) {
                        jQuery(settings.nextButton).fadeOut('fast');
                        jQuery(settings.prevButton).fadeIn('fast');
                    }
                } else {
                    if (jQuery(settings.issuesDiv + ' li:first-child').hasClass(settings.issuesSelectedClass)) {
                        jQuery(settings.prevButton).fadeOut('fast');
                    } else if (jQuery(settings.issuesDiv + ' li:last-child').hasClass(settings.issuesSelectedClass)) {
                        jQuery(settings.nextButton).fadeOut('fast');
                    } else {
                        jQuery(settings.nextButton + ',' + settings.prevButton).fadeIn('slow');
                    }
                }
            });

            jQuery(settings.prevButton).click(function (event) {
                //event.preventDefault();
                // bugixed from 0.9.54: now the dates gets centered when there's too much dates.
                var currentIndex = jQuery(settings.issuesDiv).find('li.' + settings.issuesSelectedClass).index();
                var prevHref = jQuery(settings.datesDiv + ' li').eq(currentIndex).find('a').attr('href');
                var nextHref = jQuery(settings.datesDiv + ' li').eq(currentIndex + 2).find('a').attr('href');
                if (settings.orientation == 'horizontal') {
                    var currentPositionIssues = parseInt(jQuery(settings.issuesDiv).css('marginLeft').substring(0, jQuery(settings.issuesDiv).css('marginLeft').indexOf('px')));
                    var currentIssueIndex = currentPositionIssues / widthIssue;
                    var currentPositionDates = parseInt(jQuery(settings.datesDiv).css('marginLeft').substring(0, jQuery(settings.datesDiv).css('marginLeft').indexOf('px')));
                    var currentIssueDate = currentPositionDates + widthDate;
                    if (currentPositionIssues >= 0) {
                        jQuery(settings.issuesDiv).stop();
                        jQuery(settings.datesDiv + ' li:first-child a').click();
                    } else {
                        if (!jQuery(settings.issuesDiv).is(':animated')) {
                            // bugixed from 0.9.54: now the dates gets centered when there's too much dates.
                            jQuery(settings.datesDiv + ' li').eq(currentIndex - 1).find('a').trigger('click');
                            jQuery(settings.prevButton).attr('href',prevHref);
                            jQuery(settings.nextButton).attr('href',nextHref);
                        }
                    }
                } else if (settings.orientation == 'vertical') {
                    var currentPositionIssues = parseInt(jQuery(settings.issuesDiv).css('marginTop').substring(0, jQuery(settings.issuesDiv).css('marginTop').indexOf('px')));
                    var currentIssueIndex = currentPositionIssues / heightIssue;
                    var currentPositionDates = parseInt(jQuery(settings.datesDiv).css('marginTop').substring(0, jQuery(settings.datesDiv).css('marginTop').indexOf('px')));
                    var currentIssueDate = currentPositionDates + heightDate;
                    if (currentPositionIssues >= 0) {
                        jQuery(settings.issuesDiv).stop();
                        jQuery(settings.datesDiv + ' li:first-child a').click();
                    } else {
                        if (!jQuery(settings.issuesDiv).is(':animated')) {
                            // bugixed from 0.9.54: now the dates gets centered when there's too much dates.
                            jQuery(settings.prevButton).attr('href',prevHref);
                            jQuery(settings.nextButton).attr('href',nextHref);
                            jQuery(settings.datesDiv + ' li').eq(currentIndex - 1).find('a').trigger('click');
                        }
                    }
                }
                // prev/next buttons now disappears on first/last issue | bugfix from 0.9.51: lower than 1 issue hide the arrows
                if (howManyDates == 1) {
                    jQuery(settings.prevButton + ',' + settings.nextButton).fadeOut('fast');
                } else if (howManyDates == 2) {
                    if (jQuery(settings.issuesDiv + ' li:first-child').hasClass(settings.issuesSelectedClass)) {
                        jQuery(settings.prevButton).fadeOut('fast');
                        jQuery(settings.nextButton).fadeIn('fast');
                    } else if (jQuery(settings.issuesDiv + ' li:last-child').hasClass(settings.issuesSelectedClass)) {
                        jQuery(settings.nextButton).fadeOut('fast');
                        jQuery(settings.prevButton).fadeIn('fast');
                    }
                } else {
                    if (jQuery(settings.issuesDiv + ' li:first-child').hasClass(settings.issuesSelectedClass)) {
                        jQuery(settings.prevButton).fadeOut('fast');
                    } else if (jQuery(settings.issuesDiv + ' li:last-child').hasClass(settings.issuesSelectedClass)) {
                        jQuery(settings.nextButton).fadeOut('fast');
                    } else {
                        jQuery(settings.nextButton + ',' + settings.prevButton).fadeIn('slow');
                    }
                }
            });
            // keyboard navigation, added since 0.9.1
            if (settings.arrowKeys == 'true') {
                if (settings.orientation == 'horizontal') {
                    jQuery(document).keydown(function (event) {
                        if (event.keyCode == 39) {
                            jQuery(settings.nextButton).click();
                        }
                        if (event.keyCode == 37) {
                            jQuery(settings.prevButton).click();
                        }
                    });
                } else if (settings.orientation == 'vertical') {
                    jQuery(document).keydown(function (event) {
                        if (event.keyCode == 40) {
                            jQuery(settings.nextButton).click();
                        }
                        if (event.keyCode == 38) {
                            jQuery(settings.prevButton).click();
                        }
                    });
                }
            }
            // default position startAt, added since 0.9.3
            // modified B.Bothe: start at selected issue if hash given
            if (window.location.hash) {
                var identifier = window.location.hash.slice(1);
                jQuery(settings.datesDiv + ' li').find('a[href=#'+identifier+']').trigger('click');
            } else {
                jQuery(settings.datesDiv + ' li').eq(settings.startAt - 1).find('a').trigger('click');
            }
            // autoPlay, added since 0.9.4
            var intervalId = null;
            if (settings.autoPlay == 'true') {
                var intervalId = setInterval("autoPlay()", settings.autoPlayPause);
            }
            // Play/Pause Button, added by RRZE Webteam
            jQuery("a.toggle-autoplay").click(function () {
                var htmlPlay = "<i class=\"fa fa-play\" aria-hidden=\"true\"></i>" + "<span class=\"sr-only\">Play</span>";
                var htmlPause = "<i class=\"fa fa-pause\" aria-hidden=\"true\"></i>" + "<span class=\"sr-only\">Pause</span>";
                if (jQuery(this).data("toggle") === "pause") {
                    clearInterval(intervalId);
                    jQuery("a.toggle-autoplay").html(htmlPlay).data("toggle", "play") ;
                } else if (jQuery(this).data("toggle") === "play") {
                    intervalId = setInterval("autoPlay()", settings.autoPlayPause);
                    jQuery("a.toggle-autoplay").html(htmlPause).data("toggle", "pause") ;
                }
            });
        }
    });
};

// autoPlay, added since 0.9.4
function autoPlay() {
    var currentDate = jQuery(settings.datesDiv).find('a.' + settings.datesSelectedClass);
    if (settings.autoPlayDirection == 'forward') {
        if (currentDate.parent().is('li:last-child')) {
            jQuery(settings.datesDiv + ' li:first-child').find('a').trigger('click');
        } else {
            currentDate.parent().next().find('a').trigger('click');
        }
    } else if (settings.autoPlayDirection == 'backward') {
        if (currentDate.parent().is('li:first-child')) {
            jQuery(settings.datesDiv + ' li:last-child').find('a').trigger('click');
        } else {
            currentDate.parent().prev().find('a').trigger('click');
        }
    }
}

// Adapt timeline height to biggest item, added by RRZE Webteam
// https://stackoverflow.com/a/21633150
function updateSize() {
    var minHeight=parseInt(jQuery('.issues li').eq(0).css('height'));
    jQuery(settings.issuesDiv +' li').each(function () {
        if (settings.orientation == 'vertical') {
            jQuery(settings.issuesDiv +' li').css('width',(jQuery(settings.containerDiv).width() - jQuery(settings.datesDiv).width()));
        }
        var thisHeight = parseInt(jQuery(this).css('height'));
        minHeight=(minHeight>=thisHeight?minHeight:thisHeight);
    });
    jQuery(settings.issuesDiv +' li').css('height',(+minHeight + +50)+'px');
    if (settings.orientation == 'vertical') {
        jQuery(settings.containerDiv).css('height',(+minHeight + +50)+'px');
    } else {
        jQuery(settings.containerDiv).css('height',(+minHeight + +60 + jQuery(settings.datesDiv).find('li').height())+'px');
    }
}
