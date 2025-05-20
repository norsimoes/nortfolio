/**
 * Confirmation modal
 *
 * Shows a confirmation modal for multiple purposes.
 *
 * @param $
 * @param window
 * @param document
 * @param undefined
 * @returns
 */
;( function( $, window, document, undefined ) {

    "use strict";
    
	var $modal = $('#j-confirmation-modal');

    if ($modal.length) {

        $modal.on('hidden.bs.modal', function() {

            $modal.find('#j-icon-wrapper').addClass('d-none');
            $modal.find('#j-title').addClass('d-none');
            $modal.find('#j-text').addClass('d-none');
            $modal.find('#j-confirm').addClass('d-none');
            $modal.find('#j-cancel').addClass('d-none');
        });

        $modal.on('shown.bs.modal', function(event) {
console.log('in', $modal);
            /*
             * Set vars
             */
            var $invoker = $(event.relatedTarget);

            var url = $invoker.data('url');
            var icon = $invoker.data('icon');
            var color = $invoker.data('color');
            var title = $invoker.data('title');
            var text = $invoker.data('text');
            var submitColor = $invoker.data('submitColor');
            var submitLabel = $invoker.data('submitLabel');
            var cancelLabel = $invoker.data('cancelLabel');

            /*
             * Set icon
             */
            if (icon && color) {
                $modal.find('#j-icon-wrapper').removeClass('d-none')
                $modal.find('#j-icon').addClass('fa-stack-1x fa-inverse ' + icon);
                $modal.find('#j-circle').addClass('fas fa-circle fa-stack-2x text-' + color);
            }

            /*
             * Set content
             */
            if (title) $modal.find('#j-title').removeClass('d-none').html(title);
            if (text) $modal.find('#j-text').removeClass('d-none').html(text);
            if (!text) $modal.find('#j-title').removeClass('mb-3').addClass('mb-0');

            /*
             * Set buttons
             */
            if (submitLabel) $modal.find('#j-confirm').removeClass('d-none btn-secondary').addClass('btn-' + submitColor).html(submitLabel);
            if (cancelLabel) $modal.find('#j-cancel').removeClass('d-none').html(cancelLabel);

            /*
             * Click on confirmation button
             */
            $modal.on('click', '#j-confirm', function (event) {

                event.preventDefault();
                window.location.href = url;
            });
		});
    }

} )( jQuery, window, document );
