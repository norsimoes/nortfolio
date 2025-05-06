import { alert } from '../../Core/Js/Alert.js';
import { init as initOverlay } from '../../Website/Js/Overlay.js';
import { init as initLanguage } from '../../Website/Js/Language.js';
import { init as initCounter } from './Counter.js';
import { init as initTranslationField } from './TranslationField.js';
import { init as initFileImage } from './FileImage.js';

/**
 * Administration configuration object.
 *
 * @var {object} cfg
 */
export const cfg = {

    mainId: '#admin-wrapper',

    mainEl: null,

    debugChain: ['Administration', 'Init']
};

/**
 * Initializes the Administration modules.
 *
 * @return {void}
 */
export function init() {

    cfg.mainEl = $(cfg.mainId);

    if (cfg.mainEl.length) {

        initOverlay();
        initLanguage();

        initCounter();
        initTranslationField();
        initFileImage();

        alert.remove();
        alert.bindClose();

        console.info(...cfg.debugChain, 'OK!');

    } else {

        console.warn(...cfg.debugChain, 'Failed to locate the main wrapper!');
    }
}

/*
 * Initialize on page load
 */
$(function () {

    init();

    $('.iconpicker').iconpicker();
    $('.bs-select').selectpicker();
});

/*
 * Initialize on Ajax complete
 */
$(document).ajaxComplete(function () {

    $('.bs-select').selectpicker();
});
