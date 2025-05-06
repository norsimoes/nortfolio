/**
 * Import
 *
 * Handle the import module modal.
 *
 * @param $
 * @param window
 * @param document
 * @param undefined
 * @returns
 */
;( function( $, window, document, undefined ) {

	"use strict";

	var $modal = $('#j-import-module-modal');
	var $wrapper = $('#j-import-module-wrapper');

    if ($modal.length) {

        $modal.on('hidden.bs.modal', function() {

            $wrapper.html('');
        });

        $modal.on('shown.bs.modal', function(event) {

            var $form = $modal.find('form');
			var $invoker = $(event.relatedTarget);
            var getUrl = $invoker.data('getUrl');
            var postUrl = $invoker.data('postUrl');
            var saveUrl = $invoker.data('saveUrl');

			$.get(getUrl, function(response) {

				if (typeof response == 'object') {

					if (response.status == 'success') {

						$wrapper.html(response.data);

                        $form.attr('action', postUrl);

                        var $infoWrapper = $('#j-file-info-wrapper');

                        /*
                         * On file input change
                         */
                        var $fileInput = $('.j-file-input');

                        $fileInput.on('change', function() {

                            /*
                             * Show the selected filename in the input
                             */
                            var filename = $(this).val().replace(/^.*\\/, "");

                            $(this).next('.custom-file-label').html(filename);

                            /*
                             * Prepare file post
                             */
                            var formData = new FormData();

                            formData.append('attachment', this.files[0]);

                            /*
                             * Send post data
                             */
                            $.ajax({

                                url: postUrl,
                                data: formData,
                                cache: false,
                                processData: false,
                                contentType: false,
                                dataType: "json",
                                type: "POST",
                    
                                success: function (res) {

                                    $infoWrapper.html(res.data);
                                }
                            });
                        });

					} else {
	
						$wrapper.html('');

						new Alert(response.status, response.message);
					}

				} else {

					new Alert('danger', $invoker.data('msg-wrong-response'));
				}

            }, "json")

			.done(function() {

				$form.attr('action', saveUrl);

            })

			.fail(function() {

				new Alert('danger', $invoker.data('msg-ajax-fail'));
			});
		});
    }

} )( jQuery, window, document );
