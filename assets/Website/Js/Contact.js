/**
 * Contact links configuration object.
 *
 * @var {object} cfg
 */
export const cfg = {

    initSelector: '#website-wrapper',

    initEl: null,

    phoneSelector: 'a[href="#phone"]',

    phoneEl: null,

    emailSelector: 'a[href="#email"]',

    emailEl: null
};

/**
 * Initializes the website interface contact features.
 *
 * @return {void}
 */
export function init() {

    cfg.initEl = $(cfg.initSelector);
    cfg.phoneEl = $(cfg.phoneSelector);
    cfg.emailEl = $(cfg.emailSelector);

    if (cfg.initEl.length) {

        handlePhone();
        handleEmail();
    }
}

/**
 * Handle the phone link.
 *
 * @return {void}
 */
function handlePhone() {

    // Remove the target attr
    cfg.phoneEl.removeAttr('target');

    /*
     * Set the encoded phone
     */
    const encPhone = 'KzM1MSA5MzIgMDEwIDI5NQ==';
    const tel = atob(encPhone).replace(/\s/g, '');

    cfg.phoneEl.attr('href', 'tel:' + tel);
    cfg.phoneEl.text(atob(encPhone));
}

/**
 * Handle the email link.
 *
 * @return {void}
 */
function handleEmail() {

    // Remove the target attr
    cfg.emailEl.removeAttr('target');

    /*
     * Set the encoded email
     */
    const encEmail = 'bm9yQG5vcnRmb2xpby5wdA==';

    cfg.emailEl.attr('href', 'mailto:' + atob(encEmail));
    cfg.emailEl.text(atob(encEmail));
}
