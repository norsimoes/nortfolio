/**
 * Overlay configuration object.
 *
 * @var {object} cfg
 */
export const cfg = {

    initSelector: '#website-header',

    initEl: null,

    triggerSelector: '.overlay-trigger',

    triggerEl: null,

    overlaySelector: '.overlay',

    overlayEl: null,

    titleSelector: '.overlay-title div',

    titleEl: null
};

/**
 * Initializes the website interface overlay features.
 *
 * @return {void}
 */
export function init() {

    cfg.initEl = $(cfg.initSelector);
    cfg.triggerEl = $(cfg.triggerSelector);
    cfg.overlayEl = $(cfg.overlaySelector);
    cfg.titleEl = $(cfg.titleSelector);

    if (cfg.initEl.length) {

        showOverlay();
        hideOverlay();
        initTilt();
    }
}

/**
 * Show the target overlay.
 *
 * @return {void}
 */
function showOverlay() {

    cfg.triggerEl.on('click', function () {

        let target = $(this).data('target');
        let $targetEl = $('#' + target);

        $targetEl.addClass('active');

        // Load qrcode image when overlay is shown
        if (target === 'overlay-qrcode') {
            let $img = $targetEl.find('img');
            $img.attr('src', $img.data('src'));
            $img.addClass('qrcode');
        }
    });
}

/**
 * Hide the active overlay.
 *
 * @return {void}
 */
function hideOverlay() {

    cfg.overlayEl.on('click', function (event) {

        let $keepOpen = $(event.target).closest('.form-wrapper').hasClass('keep-open');

        if (!$keepOpen) {

            $(this).removeClass('active');

            $(this).find('input, textarea').removeClass('has-val invalid invalid-email').val('');

            $(this).find('.login-message').text('');

            $(this).find('form').attr('data-validated', 0);
        }
    });
}

/**
 * Initialize the tilt effect on the title.
 *
 * @return {void}
 */
function initTilt() {

    cfg.titleEl.tilt({
        maxTilt: 25,
        perspective: 1000,
        easing: 'cubic-bezier(.03,.98,.52,.99)',
        scale: 1.1
    });
}
