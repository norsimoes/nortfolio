/*
 * Form
 * 
 * Handle form elements.
 */
$(document).ready(function() {
    
    /*
     * File input
     * Show the selected filename in the input
     */
    var $fileInput = $('.j-file-input');

    $(document).ready(function () {

        $fileInput.next('.custom-file-label').html($fileInput.attr('value'));
    });

    $fileInput.on('change',function(){

        var filename = $(this).val().replace(/^.*\\/, "");

        $(this).next('.custom-file-label').html(filename);
    });
});
