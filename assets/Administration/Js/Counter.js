/**
 * Counter configuration object.
 *
 * @var {object} cfg
 */
export const cfg = {

    triggerSelector: '#admin-content',

    triggerEl: null,

    itemSelector: '.record-counter',

    itemEl: null,

    debugChain: ['Administration', 'Counter']
};

/**
 * Initializes the application interface ajax module features.
 *
 * @return {void}
 */
export function init() {

    cfg.triggerEl = $(cfg.triggerSelector);
    cfg.itemEl = $(cfg.itemSelector);

    if (cfg.triggerEl.length && cfg.itemEl.length) {

        initCounter();

        console.info(...cfg.debugChain, 'OK!');
    }
}

/**
 * Initialize the counter animation.
 *
 * @return {void}
 */
 function initCounter() {

    cfg.itemEl.each(function () {

        // $(this).removeClass('d-none');

        $(this).prop('Counter', 0).animate({
            Counter: $(this).text()
        }, {
            duration: 1500,
            easing: 'swing',
            step: function (now) {
                if ($(this).text() !== 0) {
                    $(this).text(Math.ceil(now));
                }
            }
        });
    });
}
