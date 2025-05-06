/**
 * Tooltip configuration object.
 *
 * @var {object} cfg
 */
export const cfg = {

    initSelector: 'body',

    initEl: null,

    triggerSelector: '.overlay-trigger',

    triggerEl: null,

    tooltipSelector: '.header-tooltip',

    tooltipEl: null
};

/**
 * Initializes the website interface tooltip features.
 *
 * @return {void}
 */
export function init() {

    cfg.initEl = $(cfg.initSelector);
    cfg.triggerEl = $(cfg.triggerSelector);
    cfg.tooltipEl = $(cfg.tooltipSelector);

    if (cfg.initEl.length) {

        showTooltip();
        hideTooltip();
    }
}

/**
 * Show the target tooltip.
 *
 * @return {void}
 */
function showTooltip() {

    cfg.triggerEl.on('mouseenter', function () {

        let $template = $('#header-tooltip').html();
        let tooltip = $(this).data('tooltip');
        let $clone = $($template);

        $clone
            .text(tooltip)
            .appendTo("#header-tooltip-wrapper")
            .addClass('move-in');
    });
}

/**
 * Hide the active tooltip.
 *
 * @return {void}
 */
function hideTooltip() {

    cfg.triggerEl.on('mouseleave', function () {

        $('.header-tooltip')
            .removeClass('move-in')
            .addClass('move-out')
            .one('webkitAnimationEnd', function () {
                $(this).remove()
            });
    });
}
