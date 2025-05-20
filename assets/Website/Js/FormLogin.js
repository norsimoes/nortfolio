import {ajax} from './Ajax.js';

/**
 * User overlay configuration object.
 *
 * @var {object} cfg
 */
export const cfg = {

    initSelector: '#overlay-user',

    initEl: null,

    formSelector: '#form-login',

    formEl: null,

    inputSelector: '.input-field',

    inputEl: null,

    messageSelector: '.login-message',

    messageEl: null
};

/**
 * Initializes the website interface user overlay features.
 *
 * @return {void}
 */
export function init() {

    cfg.initEl = $(cfg.initSelector);
    cfg.formEl = $(cfg.formSelector);
    cfg.inputEl = $(cfg.inputSelector);
    cfg.messageEl = $(cfg.messageSelector);

    if (cfg.initEl.length) {

        bindInputEvents();
        bindFormSubmit();
    }
}

/**
 * Handle the input validation for specified events.
 *
 * @return {void}
 */
function bindInputEvents() {

    cfg.inputEl.each((key, obj) => {

        $(obj).on('blur focus keyup input', () => {

            validate($(obj));
        });
    });
}

/**
 * Validate the input value.
 *
 * @param {object} $this
 * @return {void}
 */
function validate($this) {

    if ($this.val()) {

        $this.removeClass('invalid').addClass('has-val');

    } else {

        $this.removeClass('has-val');

        if (cfg.formEl.attr('data-validated') === '1') {
            $this.addClass('invalid');
        }
    }
}

/**
 * Handle the form submission.
 *
 * @return {void}
 */
function bindFormSubmit() {

    cfg.formEl.on('submit', function (event) {

        event.preventDefault();
        event.stopPropagation();

        // Flag the attempt to validate the form for the first time
        cfg.formEl.attr('data-validated', '1');

        // Get the required form elements to validate
        let $validationEl = $(this).find('.input-field[required]');

        $validationEl.each(() => {

            // Validate the elements
            validate($(this));
        });

        // Set focus on the first invalidated form element
        $validationEl.each(() => {

            let $this = $(this);

            if ($this.hasClass('invalid')) {

                $this.removeClass('invalid');
                $this.trigger('focus');

                return false;
            }
        });

        // Create an Ajax request if there are no more invalid items
        let invalidElements = $(this).find('.input-field.invalid').length;

        if (!invalidElements) {

            let url = $(this).attr('action');
            let target = $(this).data('target');
            let formData = cfg.formEl.serialize();

            let successCallback = function () {

                window.location.replace(target);
            }

            let failCallback = function () {

                cfg.messageEl.text('Login failed successfully!');
            }

            ajax('post', url, formData, successCallback, failCallback);
        }
    });
}
