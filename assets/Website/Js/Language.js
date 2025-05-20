import { ajax } from './Ajax.js'

/**
 * User overlay configuration object.
 *
 * @var {object} cfg
 */
export const cfg = {

    initSelector: '#overlay-language',

    initEl: null,

    itemSelector: '.lang-item',

    itemEl: null,

    formSelector: '#language-form',

    formEl: null
};

/**
 * Initializes the website interface user overlay features.
 *
 * @return {void}
 */
export function init() {

    cfg.initEl = $(cfg.initSelector);
    cfg.itemEl = $(cfg.itemSelector);
    cfg.formEl = $(cfg.formSelector);

    if (cfg.initEl.length) {

        bindItemClick();
    }
}

/**
 * Handle the language selection.
 *
 * @return {void}
 */
function bindItemClick() {

    cfg.itemEl
        .off('click.languageItem')
        .on('click.languageItem', function (event) {

            event.preventDefault();
            event.stopPropagation();

            let url = cfg.formEl.prop('action');
            let id = $(this).data('id');
            let languageInput = cfg.formEl.find('input');
            languageInput.val(id);
            let data = cfg.formEl.serialize();

            let successCallback = function (response) {

                window.location.replace(response.redirect);
            }

            ajax('post', url, data, successCallback, null);
        });
}
