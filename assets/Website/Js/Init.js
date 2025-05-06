import { init as initTree } from './Tree.js';
import { init as initLineNumber } from './LineNumber.js';
import { init as initTooltip } from './Tooltip.js';
import { init as initContact } from './Contact.js';
import { init as initOverlay } from './Overlay.js';
import { init as initLanguage } from './Language.js';
import { init as initFormLogin } from './FormLogin.js';

/**
 * Website configuration object.
 *
 * @var {object} cfg
 */
export const cfg = {

    mainId: '#website-wrapper',

    mainEl: null
};

/**
 * Initializes the Website modules.
 *
 * @return {void}
 */
function init() {

    cfg.mainEl = $(cfg.mainId);

    if (cfg.mainEl.length) {

        initTree();
        initLineNumber();
        initTooltip();
        initContact();
        initOverlay();
        initLanguage();
        initFormLogin();
    }
}

/*
 * Initialize on page load.
 */
$(() => {

    init();
});
