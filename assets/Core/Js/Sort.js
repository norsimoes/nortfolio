import { alert } from './Alert.js';

/**
 * Sort menu items
 *
 * @param $
 * @param window
 * @param document
 * @param undefined
 * @returns
 */
;( function( $, window, document, undefined ) {

	"use strict";

    var container = document.getElementById("j-sort");

    if (container) {

        var sort = Sortable.create(container, {
    
            animation: 150,
            handle: ".j-sort-handle",
            draggable: ".j-sort-tile",
    
            onUpdate: function () {

                var postUrl = $(container).data('postUrl');
                var posArr = [];
    
                $(container).find('.j-sort-tile').each(function() {
    
                    var id = $(this).attr('id').replace('item-', '');
                    posArr.push(id);
                });

                if (posArr.length > 0) {

                    $.post(postUrl, {position: posArr}, function(response) {

                        if (typeof response == 'object') {

                            if (response.status == 'success') {

                                alert.display(response.status, response.message);
        
                            } else {
    
                                alert.display(response.status, response.message);
                            }
        
                        } else {
        
                            alert.display('error', $(container).data('msg-wrong-response'));
                        }
                    
                    }, "json")
    
                    .done( function() {
        
                    })
        
                    .fail( function() {
        
                        alert.display('error', $(container).data('msg-ajax-fail'));
                    });
                }
            }
        });
    }

} )( jQuery, window, document );
