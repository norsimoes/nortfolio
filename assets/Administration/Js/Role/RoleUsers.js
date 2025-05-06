/**
 * Role users
 *
 * List the users that belong to a role.
 *
 * @param $
 * @param window
 * @param document
 * @param undefined
 * @returns
 */
;( function( $, window, document, undefined ) {

	"use strict";

	var $modal = $('#j-role-users-modal');
	var $usersWrapper = $('#j-role-users-wrapper');

    if ($modal.length) {

        $modal.on('hidden.bs.modal', function() {

            $modal.find('#j-role-users-title span').html('');
            $usersWrapper.html('');
        });

        $modal.on('shown.bs.modal', function(event) {

            var $form = $modal.find('form');
			var $invoker = $(event.relatedTarget);
            var getUrl = $invoker.data('getUrl');

			$modal.find('#j-role-users-title span').html('');

			$.get(getUrl, function(response) {

				if (typeof response == 'object') {

					if (response.status == 'success') {

						$usersWrapper.html(response.data);

					} else {
	
						$usersWrapper.html('');
						new Alert(response.status, response.message);
					}

				} else {

					new Alert('danger', $invoker.data('msg-wrong-response'));
				}

            }, "json")

			.done(function() {

				$form.find('#j-role-users-title span').html($invoker.data('name'));

            })

			.fail(function() {

				new Alert('danger', $invoker.data('msg-ajax-fail'));
			});
		});
    }

} )( jQuery, window, document );
