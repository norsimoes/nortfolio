/**
 * Role permissions
 *
 * Handle features related to the role permissions.
 *
 * @param $
 * @param window
 * @param document
 * @param undefined
 * @returns
 */
;( function( $, window, document, undefined ) {

	"use strict";

	$(document).ready(function() {

        /*
         * Check/uncheck all
         */
        var $checkAll = $('#accordion .j-check-all');

        $checkAll.click(function() {

            var $checkBoxes = getInterfaceCheckbox($(this));

            $checkBoxes.prop( "checked", $(this).prop("checked") );
        });

        /*
         * Update check all status
         */
        var $checkbox = $('#accordion input:checkbox:not(.j-check-all)');

        $checkbox.click(function() {

            updateCheckAll($(this));
        });

        /*
         * Initial state
         */
        $checkAll.each(function() {

            updateCheckAll($(this));
        });

    });

    /**
     * Return all checkboxes inside the interface.
     * 
     * @param {object} $el jQuery DOM element reference
     * @return {object} 
     */
    function getInterfaceCheckbox($el) {

        return $el.closest('.interface-card').find('table input:checkbox');
    }

    /**
     * Update check all checkbox status.
     * 
     * @param {object} $el jQuery DOM element reference
     * @return {void} 
     */
    function updateCheckAll($el) {

        var $checkBoxes = getInterfaceCheckbox($el);
        var $checkAll = $el.closest('.interface-card').find('.j-check-all');

        var checkboxCount = $checkBoxes.length;
        var uncheckedCount = $checkBoxes.not(':checked').length;

        $checkAll.prop("indeterminate", false);

        if (!uncheckedCount) {

            $checkAll.prop("checked", true);

        } else if (uncheckedCount == checkboxCount) {

            $checkAll.prop("checked", false);

        } else {

            $checkAll.prop("indeterminate", true);
        }
    }

} )( jQuery, window, document );
