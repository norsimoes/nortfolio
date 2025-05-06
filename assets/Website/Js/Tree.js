/**
 * Tree configuration object.
 *
 * @var {object} cfg
 */
export const cfg = {

    wrapperSelector: '.tree',

    wrapperEl: null,

    caretSelector: '.caret',

    barSelector: '.bar-wrapper',

    barEl: null,

    progressSelector: '.bar-fg',

    progressEl: null
};

/**
 * Initializes the tree.
 *
 * @return {void}
 */
export function init() {

    cfg.wrapperEl = $(cfg.wrapperSelector);
    cfg.barEl = $(cfg.barSelector);
    cfg.progressEl = $(cfg.progressSelector);

    if (cfg.wrapperEl.length) {

        bindCaretClick();
        initProgressBar();
        animateProgressBar();
        initTreeClose();
    }

    /*
     * Handle the window resize event
     */
    let width = $(window).width();

    $(window).on('resize', function () {

        cfg.barEl.css({'width': '100%', 'opacity': 0});

        initProgressBar();

        if ($(this).width() !== width) {
            width = $(this).width();
            initTreeClose();
        }
    });
}

/**
 * Handles the task tree expand/collapse features.
 *
 * @return {void}
 */
function bindCaretClick() {

    cfg.wrapperEl
        .off('click.caretClick')
        .on('click.caretClick', cfg.caretSelector, function () {

            let $this = $(this);

            $this.next('.nested').toggleClass('active');
            $this.toggleClass('caret-down');

        });
}

/**
 * Initializes the progress bar.
 *
 * @return {void}
 */
function initProgressBar() {

    // Set bar width
    let arr = [];

    cfg.barEl.each(function (key, obj) {
        arr.push(obj.offsetWidth);
    });

    let min = Math.min(...arr);

    cfg.barEl.css({'width': min - 10, 'opacity': 1});
}

/**
 * Animate the progress bar.
 *
 * @return {void}
 */
function animateProgressBar() {

    cfg.progressEl.each(function (key, obj) {

        let progress = $(obj).data('width');

        $(obj).animate({width: progress + '%'}, 500);
    });
}

/**
 * Closes the tree first level below media query width.
 *
 * @return {void}
 */
function initTreeClose() {

    let width = $(window).width();

    cfg.wrapperEl.each(function (key, obj) {

        let caret = $(obj).find('.l-1 > li > span');
        let content = $(obj).find('.l-1 > li > ul');

        setTimeout(function () {
            if (width < 1024) {
                caret.removeClass('caret-down');
                content.removeClass('active');
            } else {
                caret.addClass('caret-down');
                content.addClass('active');
            }
        }, (key + 1) * 1000);
    });
}
