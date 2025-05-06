import { alert } from './Alert.js';

/**
 * Toggle available
 *
 * Toggles the available status of a record
 * and handles related operations.
 *
 * @param $
 * @param window
 * @param document 
 * @param undefined
 * @returns
 */
;( function( $, window, document, undefined ) {

	"use strict";

    var $invoker;

    var $modal = $('#j-activation-modal');
    var $wrapper = $('#j-activation-wrapper');

    $('body').on('click', '.j-toggle-available', function(event) {

        $invoker = $(this);

        var oldStatus = $invoker.data('status');

        if (oldStatus == 0) {

            /*
             * Switch turning on, show activation modal
             */
            $modal.modal('toggle');

        } else {

            /*
             * Switch turning off, just do it
             */
            event.preventDefault();
            event.stopPropagation();

            handleAvailableSwitch();
        }
    });

    /*
     * Render confirmation modal
     */
    $modal.on('shown.bs.modal', function(event) {

        var getUrl = $invoker.data('getUrl') + $invoker.data('id') + '/';

        $.get(getUrl, function(response) {

            if (typeof response == 'object') {

                $wrapper.html(response.data);

            } else {

                alert.display('error', $invoker.data('msgWrongResponse'));
            }

        }, "json");
    });

    /*
     * Click on modal submit button
     */
    $modal.on('click', '#submit', function (event) {

        event.preventDefault();
        event.stopPropagation();

        $modal.modal('toggle');

        handleAvailableSwitch();
    });

    /**
     * Handle available switch
     */
    function handleAvailableSwitch() {

        var oldStatus = $invoker.data('status');
        var active = $invoker.data('active');
        var blocked = $invoker.data('blocked');

        var newStatus = oldStatus == active ? blocked : active;

        var url = $invoker.data('postUrl') + newStatus + '/';

        $.get(url, function (response) {

            if (typeof response == 'object') {

                // Set a success message
                alert.display(response.status, response.message);

                // Update icon
                $invoker.find('span[class^="toggle-"]').attr('class', $invoker.data('icon-' + newStatus));

                // Update the active switch
                handleActiveSwitch();

                // Update status
                $invoker.data('status', newStatus);

            } else {

                alert.display('error', $invoker.data('msgWrongResponse'));
            }

        }, "json");
    }

    /**
     * Handle active switch
     */
    function handleActiveSwitch() {

        // Get the toggle method url
        var activeUrl = $('#j-wrapper').data('url') + $invoker.data('id') + '/0/';

        // Call the toggle method
        $.get(activeUrl);

        // Redraw dataTables
        $('#DataTables_Table_0').DataTable().draw();
    }

} )( jQuery, window, document );
