/**
 * Line number configuration object.
 *
 * @var {object} cfg
 */
export const cfg = {

    initSelector: '#website-header',

    initEl: null,

    lineSelector: '.line-number',

    lineEl: null
};

/**
 * Initializes the website interface line number features.
 *
 * @return {void}
 */
export function init() {

    cfg.initEl = $(cfg.initSelector);
    cfg.lineEl = $(cfg.lineSelector);

    if (cfg.initEl.length) {

        initLineNumber();
    }
}

/**
 * Write the gutter line numbers.
 *
 * @return {void}
 */
function initLineNumber() {

    let number = 1;

    cfg.lineEl.each(function(key, obj) {

        setTimeout(function() { $(obj).html(number ++); }, (key + 1) * 20);
    });
}
