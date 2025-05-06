import {ajax} from '../../Website/Js/Ajax.js';

/**
 * Translation field configuration object.
 *
 * @var {object} cfg
 */
export const cfg = {

    triggerSelector: '.file-image',

    triggerEl: null,

    inputSelector: '.file-image-input',

    inputEl: null,

    previewSelector: '.file-image-preview',

    previewEl: null,

    closeSelector: '.file-image-close',

    closeEl: null,

    debugChain: ['Administration', 'FileImage']
};

/**
 * Initializes the application interface ajax module features.
 *
 * @return {void}
 */
export function init() {

    cfg.triggerEl = $(cfg.triggerSelector);
    cfg.inputEl = $(cfg.inputSelector);
    cfg.previewEl = $(cfg.previewSelector);
    cfg.closeEl = $(cfg.closeSelector);

    if (cfg.triggerEl.length && cfg.inputEl.length) {

        bindSelectImage();
        bindInputChange();
        initCloseButton();
        bindCloseButton();

        console.info(...cfg.debugChain, 'OK!');
    }
}

/**
 * Handles the click on the file input.
 *
 * @return {void}
 */
function bindSelectImage() {

    cfg.triggerEl.click(function (event) {

        $(this).next('input').click();
    });
}

/**
 * Handles the file input change.
 *
 * @return {void}
 */
function bindInputChange() {

    cfg.inputEl.change(function (event) {

        readURL(this);
    });
}

/**
 * Shows the uploaded image preview in the container.
 *
 * @return {void}
 */
function readURL(input) {

    if (input.files && input.files[0]) {

        var reader = new FileReader();

        reader.onload = function(e) {
            cfg.previewEl.attr('src', e.target.result);
            cfg.closeEl.show();
        }

        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Handles the close button display.
 *
 * @return {void}
 */
function initCloseButton() {

    let src = cfg.previewEl.attr('src');

    if (src.indexOf('Blank.svg') >= 0) {
        cfg.closeEl.hide();
    }
}

/**
 * Handles the click on the close button.
 *
 * @return {void}
 */
function bindCloseButton() {

    cfg.closeEl.click(function (event) {

        event.stopPropagation();

        let src = cfg.previewEl.attr('src');
        let assetsUrl = $(this).data('assets');

        if (src.indexOf('cdn/') >= 0) {

            cfg.inputEl.val('');

            let successCallback = function(data) {

                cfg.previewEl.attr('src', assetsUrl + 'Core/Img/Placeholder/Blank.svg');
                cfg.closeEl.hide();
            }

            ajax('get', $(this).data('delete'), null, successCallback);

        } else {

            cfg.previewEl.attr('src', assetsUrl + 'Core/Img/Placeholder/Blank.svg');
            cfg.closeEl.hide();
        }
    });
}
