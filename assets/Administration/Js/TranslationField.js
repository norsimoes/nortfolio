/**
 * Translation field configuration object.
 *
 * @var {object} cfg
 */
export const cfg = {

    triggerSelector: '.translation-trigger',

    triggerEl: null,

    modalSelector: '#j-translation-modal',

    modalEl: null,

    formSelector: '#j-translation-form',

    formEl: null,

    wrapperSelector: '#j-translation-wrapper',

    wrapperEl: null,

    debugChain: ['Administration', 'TranslationField']
};

/**
 * Initializes the application interface ajax module features.
 *
 * @return {void}
 */
export function init() {

    cfg.triggerEl = $(cfg.triggerSelector);
    cfg.modalEl = $(cfg.modalSelector);
    cfg.formEl = $(cfg.formSelector);
    cfg.wrapperEl = $(cfg.wrapperSelector);

    if (cfg.triggerEl.length && cfg.modalEl.length && cfg.wrapperEl.length) {

        modalShownEvent();

        modalHiddenEvent();

        console.info(...cfg.debugChain, 'OK!');
    }
}

/**
 * Handles the 'shown.bs.modal' event.
 *
 * @return {void}
 */
 function modalShownEvent() {

    cfg.modalEl.on('shown.bs.modal', function (event) {

        let $invoker = $(event.relatedTarget);

        let type = $invoker.data('type');

        let $hidden = $invoker.closest('.input-group').find('input[type="hidden"]');

        // Clear current fields
        $(this).find('.mb-3').remove();

        $hidden.each(function(key, obj) {

            let iso2 = $(obj).data('iso2');
            let name = $(obj).attr('name');
            let value = $(obj).attr('value');

            if (type == 'input') {

                $('#j-translation-wrapper').append(
                    '<div class="mb-3"><label>' + iso2 + '</label><input type="text" class="form-control" name="' + name + '" value="' + value + '"></div>'
                );

            } else {

                $('#j-translation-wrapper').append(
                    '<div class="mb-3"><label>' + iso2 + '</label><textarea class="form-control" name="' + name + '">' + value + '</textarea></div>'
                );
            }
        });

        bindFormSubmit($invoker);
    });
}

/**
 * Handles the 'hidden.bs.modal' event.
 *
 * @return {void}
 */
function modalHiddenEvent() {

    cfg.modalEl.on('hidden.bs.modal', function (event) {


    });
}

/**
 * Handles the form submission.
 *
 * @return {void}
 */
function bindFormSubmit($invoker) {

    cfg.formEl.on('submit', function (event) {

        event.preventDefault();
        event.stopPropagation();

        let data = $(this).serializeArray();

        data.forEach(function(obj) {

            let $target = $invoker.closest('.input-group').find('input[name="' + obj.name + '"]');

            $target.attr('value', obj.value);
        });

        cfg.modalEl.modal('toggle');
    });
}
