/**
 * Translation panel
 *
 * @param $
 * @param window
 * @param document 
 * @param undefined
 * @returns
 */
;( function( $, window, document, undefined ) {

	"use strict";

    /*
     * Prevent modal dismiss on click
     */
    $(document).on('click', '.translation-trigger', function (e) {

        e.stopPropagation();
        e.preventDefault();
    });

} )( jQuery, window, document );
