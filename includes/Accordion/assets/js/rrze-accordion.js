/**
 * RRZE Accordion 1.0.0
 * RRZE Webteam
 */

const { __, _x, _n, sprintf } = wp.i18n;

jQuery(document).ready(function($) {
    // Close Accordions on start, except first
    $('.accordion-body').not(".accordion-body.open").not('.accordion-body.stayopen').hide();
    $('.accordion-body.open').each( function () {
        $(this).closest('.accordion-group').find('button.accordion-toggle').first().addClass('active');
    })
    $('.accordion').each(function() {
        if ($(this).find('button.expand-all').length > 0) {
            let items = $(this).find(".accordion-group");
            let open = $(this).find(".accordion-body.open");
            if (items.length == open.length) {
                $(this).find('button.expand-all').attr("data-status", 'open').data('status', 'open').html(__('Collapse All', 'rrze-elements'));
                //$(this).find('button.expand-all').attr("data-status", 'open').data('status', 'open').html(accordionToggle.collapse_all);
                //console.log("items = " + items.length + " open = " + open.length);
            }
        }
    });

    $('.accordion-toggle').bind('mousedown', function(event) {
        event.preventDefault();
        let $accordion = $(this).attr('href');
        let $name = $(this).data('name');
        toggleAccordion($accordion);
        // Put name attribute in URL path if available, else href
        if (typeof($name) !== 'undefined') {
            window.history.replaceState(null, null, '#' + $name);
        } else {
            window.history.replaceState(null, null, $accordion);
        }
    });

    // Keyboard navigation for accordions
    $('.accordion-toggle').keydown(function(event) {
        if (event.keyCode == 32) {
            let $accordion = $(this).attr('href');
            let $name = $(this).data('name');
            toggleAccordion($accordion);
            if (typeof($name) !== 'undefined') {
                window.history.replaceState(null, null, '#' + $name);
            } else {
                window.history.replaceState(null, null, $accordion);
            }
        }
    });

    function toggleAccordion($accordion) {
        let $thisgroup = $($accordion).closest('.accordion-group');
        let $othergroups = $($accordion).closest('.accordion').find('.accordion-group').not($thisgroup);
        $($othergroups).children('.accordion-heading').children(' .accordion-toggle').removeClass('active');
        $($othergroups).children('.accordion-body').not('.accordion-body.stayopen').slideUp();
        $($thisgroup).children('.accordion-heading').children('.accordion-toggle').toggleClass('active');
        $($thisgroup).children('.accordion-body').slideToggle();
        // refresh Slick Gallery
        let $slick = $($thisgroup).find("div.slick-slider");
            if ($slick.length < 0) {
                $slick.slick("refresh");
            }
    }

    function openAnchorAccordion($target) {
        if ($target.closest('.accordion').parent().closest('.accordion-group')) {
            let $thisgroup = $($target).closest('.accordion-group');
            let $othergroups = $($target).closest('.accordion').find('.accordion-group').not($thisgroup);
            $($othergroups).find('.accordion-toggle').removeClass('active');
            $($othergroups).find('.accordion-body').not('.accordion-body.stayopen').slideUp();
            $($thisgroup).find('.accordion-toggle:first').not('.active').addClass('active');
            $($thisgroup).find('.accordion-body:first').slideDown();
            // open parent accordion bodies if target = nested accordion
            $($thisgroup).parents('.accordion-group').find('.accordion-toggle:first').not('.active').addClass('active');
            $($thisgroup).parents('.accordion-body').slideDown();
        }
        let offset = $target.offset();
        let $scrolloffset = offset.top - 300;
        $('html,body').animate({
            scrollTop: $scrolloffset
        }, 'slow');
    }

    if (window.location.hash) {
        let identifier = window.location.hash.split('_')[0];
        let inpagenum = window.location.hash.split('_')[1];
        if (identifier == '#collapse') {
            if ($.isNumeric(inpagenum)) {
                let $findid = 'collapse_' + inpagenum;
                let $target = $('body').find('#' + $findid);
            }
        } else {
            let $findname = window.location.hash.replace('\#', '');
            let $target = $('body').find('div[name=' + $findname + ']');
        }
        if ($target.length > 0) {
            openAnchorAccordion($target);
        }
    }

    $('a:not(.prev, .next)').click(function(e) { // prev und next wegen Konflikt mit Timeline ausgeschlossen
        // nur auf Seiten, auf denen ein Accordion existiert,
        // und nur, wenn der geklickte Link nicht der Accordion-Toggle-Link oder der Expand-All-Link ist
        if (($('[id^=accordion-]').length) &&
            (!$(this).hasClass("accordion-toggle")) &&
            (!$(this).hasClass("accordion-tabs-nav-toggle"))) {
            let $hash = $(this).prop("hash");
            let identifier = $hash.split('_')[0];
            let inpagenum = $hash.split('_')[1];
            if (identifier == '#collapse') {
                if ($.isNumeric(inpagenum)) {
                    let $findid = 'collapse_' + inpagenum;
                    let $target = $('body').find('#' + $findid);
                }
            } else {
                let $findname = identifier.replace('\#', '');
                let $target = $('body').find('div[name=' + $findname + ']');
            }
            if ($target) {
                openAnchorAccordion($target);
            }
        }
    });

    $('.expand-all').click(function(e) {
        let $thisgroup = $(this).closest('.accordion');
        if ($(this).data('status') === 'open') {
            $($thisgroup).find('.accordion-body').slideUp();
            $($thisgroup).find('.accordion-toggle').removeClass('active');
            $(this).attr("data-status", 'closed').data('status', 'closed').html(__('Expand All', 'rrze-elements'));
        } else {
            $($thisgroup).find('.accordion-body').slideDown();
            $($thisgroup).find('.accordion-toggle').addClass('active');
            $(this).attr("data-status", 'open').data('status', 'open').html(__('Collapse All', 'rrze-elements'));
        }
    });

    // Assistant tabs
    $('.assistant-tabs-nav a').bind('click', function (event) {
        event.preventDefault();
        let pane = $(this).attr('href');
        $(this).parents('ul').find('a').removeClass('active');
        $(this).addClass('active');
        $(this).parents('.assistant-tabs').find('.assistant-tab-pane').removeClass('assistant-tab-pane-active');
        $(pane).addClass('assistant-tab-pane-active');
    });

    // Keyboard navigation for assistant tabs
    $('.assistant-tabs-nav a').keydown('click', function (event) {
        if (event.keyCode == 32) {
            let pane = $(this).attr('href');
            $(this).parents('ul').find('a').removeClass('active');
            $(this).addClass('active');
            $(this).parents('.assistant-tabs').find('.assistant-tab-pane').removeClass('assistant-tab-pane-active');
            $(pane).addClass('assistant-tab-pane-active');
        }
    });

});
