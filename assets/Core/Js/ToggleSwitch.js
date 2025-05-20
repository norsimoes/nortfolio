import { alert } from './Alert.js';

/**
 * Toggle switch
 *
 * Toggles the status of a record parameter.
 *
 * @param $
 * @param window
 * @param document 
 * @param undefined
 * @returns
 */
;( function( $, window, document, undefined ) {

	"use strict";

    $('body').on('click', 'a.j-toggle-switch', function(e) {

        e.preventDefault();

        var $this = $(this);

        var oldStatus = $this.data('status');
        var active = $this.data('active');
        var blocked = $this.data('blocked');

        var newStatus = oldStatus == active ? blocked : active;

        var url = $this.data('url') + newStatus + '/';

        $.get(url, function (response) {

            if (typeof response == 'object') {

                // Set a success message
                alert.display(response.status, response.message);

                // Update icon
                $this.find('span[class^="toggle-"]').attr('class', $this.data('icon-' + newStatus));

                // Clear current tooltip
                // $this.tooltipster("destroy");

                // Update title
                $this.attr('title', $this.data('title-' + newStatus));

                // Enable updated tooltip
                // new titleTooltip();

                // Update status
                $this.data('status', newStatus);

            } else {

                alert.display('error', $this.data('msgWrongResponse'));
            }

        }, "json")

        .done( function () {

        })

        .fail( function () {
            
            alert.display('error', $this.data('msgAjaxFail'));
        });
    });

} )( jQuery, window, document );
