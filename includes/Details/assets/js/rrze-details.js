jQuery(document).ready(function($) {

    /**
     * RRZE Elements - Accordion 1.0.4
     * Refactored using <details> element
     */

    /**
     * Checks if all items within an accordion are open to toggle the 'Expand All/Collapse All' button state.
     */
    $('.accordion').each(function() {
        const $accordion = $(this);
        const items = $accordion.find(".accordion-group");
        const openItems = items.find(".accordion-body.open");
        if (items.length === openItems.length) {
            $accordion.find('button.expand-all').attr("data-status", 'open').data('status', 'open').html(elementsTranslations.collapse_all);
        }
    });

    /**
     * Sanitizes a jQuery selector to prevent jQuery selector injection.
     * @param {string} selector - The selector to sanitize.
     * @returns {string} The sanitized selector.
     */
    function sanitizeSelector(selector) {
        return selector.replace(/[^a-zA-Z0-9_\-#]/g, '');
    }

    /**
     * Opens an accordion based on a target anchor link and scrolls to the accordion.
     * @param {jQuery} $target - The target accordion body element to be opened.
     */
    function openAnchorAccordion($target) {
        if ($target.closest('.accordion').parent().closest('.accordion-group')) {
            const $thisgroup = $($target).closest('.accordion-group');
            const $othergroups = $($target).closest('.accordion').find('.accordion-group').not($thisgroup);
            $($othergroups).find('.accordion-toggle').removeClass('active').attr('aria-expanded', 'false');
            $($othergroups).find('.accordion-body').not('.accordion-body.stayopen').slideUp();
            $($thisgroup).find('.accordion-toggle:first').not('.active').addClass('active').attr('aria-expanded', 'true');
            $($thisgroup).find('.accordion-body:first').slideDown();
            $($thisgroup).parents('.accordion-group').find('.accordion-toggle:first').not('.active').addClass('active').attr('aria-expanded', 'true');
            $($thisgroup).parents('.accordion-body').slideDown();
        }
        const offset = $target.offset();
        const $scrolloffset = offset.top - 300;
        $('html,body').animate({
            scrollTop: $scrolloffset
        }, 'slow');
    }

    /**
     * Checks if the URL contains a hash and opens the corresponding accordion if it exists.
     */
    if (window.location.hash) {
        const identifier = window.location.hash.split('_')[0];
        const inpagenum = window.location.hash.split('_')[1];
        let $target;

        if (identifier === '#collapse' || identifier === '#panel') {
            const prefix = identifier === '#collapse' ? 'collapse_' : 'panel_';
            if (inpagenum) {
                const $findid = prefix + inpagenum;
                $target = $('body').find('#' + sanitizeSelector($findid));
            }
        } else {
            const $findname = window.location.hash.replace('#', '');
            $target = $('body').find('div[name=' + sanitizeSelector($findname) + ']');
        }
        
        if ($target && $target.length > 0) {
            openAnchorAccordion($target);
        }
    }

    /**
     * Handles clicks on the 'expand all' or 'collapse all' buttons within an accordion.
     * Toggles the expansion state of all accordion bodies and toggles within the same accordion.
     */
    document.querySelectorAll(".expand-all").forEach(button => {
        button.addEventListener("click", function () {
            const $this = $(this);
            const container = this.closest(".details-wrapper");
            const detailsElements = container.querySelectorAll("details");
            if ($this.data('status') === 'open') {
                detailsElements.forEach(detail => {
                    detail.setAttribute("name", $this.data('name'));
                    detail.open = false;
                });
                $this.attr("data-status", 'closed').data('status', 'closed').html(elementsTranslations.expand_all);
            } else {
                detailsElements.forEach(detail => {
                    detail.removeAttribute("name");
                    detail.open = true;
                });
                $this.attr("data-status", 'open').data('status', 'open').html(elementsTranslations.collapse_all);
            }
        });
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

    /**
     * Handles clicks on links and opens the corresponding accordion if it exists.
     */
    $('a:not(.prev, .next)').click(function(e) {
        if (($('[id^=accordion-]').length) &&
            (!$(this).hasClass("accordion-toggle")) &&
            (!$(this).hasClass("accordion-tabs-nav-toggle"))) {
            const $hash = $(this).prop("hash");
            const identifier = $hash.split('_')[0];
            const inpagenum = $hash.split('_')[1];
            let $target;

            if (identifier === '#collapse' || identifier === '#panel') {
                const prefix = identifier === '#collapse' ? 'collapse_' : 'panel_';
                if (inpagenum) {
                    const $findid = prefix + inpagenum;
                    $target = $('body').find('#' + sanitizeSelector($findid));
                }
            } else {
                const $findname = identifier.replace('#', '');
                $target = $('body').find('div[name=' + sanitizeSelector($findname) + ']');
            }

            if ($target && $target.length > 0) {
                openAnchorAccordion($target);
            }
        }
    });
});

const accordions = document.querySelectorAll(".details-wrapper details");

accordions.forEach(details => {
    const summary = details.querySelector("summary");
    const content = details.querySelector(".details-content");
    console.log(content);
    summary.addEventListener("click", (e) => {
        e.preventDefault();

        // Wenn bereits offen → schließen
        if (details.hasAttribute("open")) {
            collapse(details, content);
        } else {
            // Andere schließen
            accordions.forEach(other => {
                if (other !== details && other.hasAttribute("open")) {
                    const otherContent = other.querySelector(".details-content");
                    collapse(other, otherContent);
                }
            });

            // Dann dieses öffnen
            expand(details, content);
        }
    });
});

function expand(details, content) {
    details.setAttribute("open", "");
    const height = content.scrollHeight;
    content.style.height = "0px";
    requestAnimationFrame(() => {
        content.style.height = height + "px";
    });
    content.addEventListener("transitionend", function handler() {
        content.style.height = "auto";
        content.removeEventListener("transitionend", handler);
    });
}

function collapse(details, content) {
    const height = content.scrollHeight;
    content.style.height = height + "px";
    requestAnimationFrame(() => {
        content.style.height = "0px";
    });
    content.addEventListener("transitionend", function handler() {
        details.removeAttribute("open");
        content.removeEventListener("transitionend", handler);
    });
}