import { alert } from './Alert.js';

/**
 * Template modal
 *
 * Shows a template modal for multiple purposes.
 *
 * @param $
 * @param window
 * @param document
 * @param undefined
 * @returns
 */
;( function( $, window, document, undefined ) {

    "use strict";

	var $modal = $('#j-template-modal');
	var $wrapper = $('#j-template-wrapper');

    if ($modal.length) {

        $modal.on('hidden.bs.modal', function() {

            $('.modal-header').addClass('d-none');
            $wrapper.html('');
        });

        $modal.on('shown.bs.modal', function(event) {

            var $form = $modal.find('form');
            var $invoker = $(event.relatedTarget);
            
            var width = $invoker.data('width');
            var modalTitle = $invoker.data('modalTitle');
            var getUrl = $invoker.data('getUrl');
            var postUrl = $invoker.data('postUrl');
            var msgWrongResponse = $invoker.data('msgWrongResponse');
            var msgAjaxFail = $invoker.data('msgAjaxFail');

			$.get(getUrl, function(response) {

				if (typeof response == 'object') {

                    if (response.status == 'success') {

                        $wrapper.html(response.data);

					} else {
	
						$wrapper.html('');
						alert.display(response.status, response.message);
					}

				} else {

					alert.display('error', msgWrongResponse);
				}

            }, "json")

			.done(function() {

                // Set modal width
                if (width) $form.css('width', width);

                // Complete the form action
                if (postUrl) $form.attr('action', postUrl);

                // Complete the modal title
                if (modalTitle.length) { 
                    $('#j-template-title').closest('div.modal-header').removeClass('d-none');
                    $('#j-template-title').html(modalTitle);
                }

                /*
                 * Start task tree toggler
                 */
                var $toggler = $('.caret');

                if ($toggler.length) {

                    $toggler.each(function () {
    
                        $(this).click(function () {
    
                            $(this).next('.nested').toggleClass('active');
    
                            $(this).toggleClass('caret-down');
                        });
                    });
                }

            })

			.fail(function() {

				alert.display('error', msgAjaxFail);
			});
		});
    }

} )( jQuery, window, document );
