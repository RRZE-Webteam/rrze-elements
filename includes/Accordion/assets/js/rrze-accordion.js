/**
 * RRZE Accordion 1.25.21
 * Refactored and documented for efficiency and readability.
 * Uses jQuery for DOM manipulation and event handling.
 */

const { __, _x, _n, sprintf } = wp.i18n;

jQuery(document).ready(function($) {
    /**
     * Initially hides all accordion bodies except those marked as open or should stay open,
     * and marks the corresponding toggles as active.
     */
    $('.accordion-body').not(".open, .stayopen").hide();
    $('.accordion-body.open').each(function() {
        $(this).closest('.accordion-group').find('.accordion-toggle').first().addClass('active');
    });

    /**
     * Checks if all items within an accordion are open to toggle the 'Expand All/Collapse All' button state.
     */
    $('.accordion').each(function() {
        const $accordion = $(this);
        const items = $accordion.find(".accordion-group");
        const openItems = items.find(".accordion-body.open");
        if (items.length === openItems.length) {
            $accordion.find('button.expand-all').attr("data-status", 'open').data('status', 'open').html(__('Collapse All', 'rrze-elements'));
        }
    });

    /**
     * Retrieves the target accordion ID from a button or link element.
     * @param {jQuery} $elem - The element that may contain a href or data-href attribute.
     * @returns {string} The target selector for the accordion to be toggled.
     */
    function getAccordionTarget($elem) {
        return $elem.data('href') || $elem.attr('href');
    }

    /**
     * Toggles the visibility of an accordion's content.
     * @param {string} $accordion - The selector of the accordion whose visibility will be toggled.
     */
    function toggleAccordion($accordion) {
        const $group = $($accordion).closest('.accordion-group');
        const $directBody = $group.children('.accordion-body');
        const $directToggle = $group.children('.accordion-heading').children('.accordion-toggle');
        const $otherGroups = $group.siblings();

        $otherGroups.children('.accordion-heading').children('.accordion-toggle').removeClass('active');
        $otherGroups.children('.accordion-body').not('.accordion-body.stayopen').slideUp();

        $directToggle.toggleClass('active');
        $directBody.slideToggle();

        refreshSlickGallery($group);
    }

    /**
     * Refreshes the Slick Gallery within an accordion if it exists.
     * This is needed because changes in visibility can affect Slick's layout.
     * @param {jQuery} $group - The accordion group that may contain a Slick slider.
     */
    function refreshSlickGallery($group) {
        const $slick = $group.find(".slick-slider");
        if ($slick.length) {
            $slick.slick("refresh");
        }
    }

    /**
     * Binds mousedown and keydown events to accordion toggles.
     * Prevents default action and toggles the accordion based on the target derived from the element.
     * Updates the URL hash if a name is provided.
     */
    $('.accordion-toggle').on('mousedown keydown', function(event) {
        if (event.type === 'mousedown' || event.keyCode === 32) {
            event.preventDefault();
            const $target = getAccordionTarget($(this));
            const $name = $(this).data('name');
            toggleAccordion($target);
            window.history.replaceState(null, null, $name ? '#' + $name : $target);
        }
    });

    /**
     * Handles clicks on the 'expand all' or 'collapse all' buttons within an accordion.
     * Toggles the expansion state of all accordion bodies and toggles within the same accordion.
     */
    $('.expand-all').on('click', function() {
        const $this = $(this);
        const $accordion = $this.closest('.accordion');
        const $bodies = $accordion.find('.accordion-body');
        const $toggles = $accordion.find('.accordion-toggle');
        if ($this.data('status') === 'open') {
            $bodies.slideUp();
            $toggles.removeClass('active');
            $this.attr("data-status", 'closed').data('status', 'closed').html(__('Expand All', 'rrze-elements'));
        } else {
            $bodies.slideDown();
            $toggles.addClass('active');
            $this.attr("data-status", 'open').data('status', 'open').html(__('Collapse All', 'rrze-elements'));
        }
    });

    /**
     * Binds click and keydown events to links within assistant tabs.
     * Activates the tab associated with the clicked link and displays its corresponding pane.
     */
    $('.assistant-tabs-nav a').on('click keydown', function(event) {
        if (event.type === 'click' || event.keyCode === 32) {
            event.preventDefault();
            const $link = $(this);
            const $tabs = $link.parents('.assistant-tabs');
            const $paneId = $link.attr('href');
            $link.closest('ul').find('a').removeClass('active');
            $link.addClass('active');
            $tabs.find('.assistant-tab-pane').removeClass('assistant-tab-pane-active');
            $($paneId).addClass('assistant-tab-pane-active');
        }
    });
});