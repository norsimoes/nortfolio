/**
 * Alert
 * 
 * Displays a message to the user.
 */
export class Alert {

    /**
     * Displays a message to the user
     * 
     * @param {string} type 
     * @param {string} message 
     * @return {void}
     */
    display(type, message) {

        var validTypes = ['success', 'error', 'info'];

        this.type = $.inArray(type, validTypes) !== -1 ? type : 'secondary';
        this.message = message || '...';

        this.template = $('#alert');
        this.target = $('#admin-alerts');
        
        // Create clone from template
        var template = $('#alert').html();
        var $clone = $(template);

        // Set message
        $clone.find('.alert-message').html(this.message);

        // Set class
        $clone.addClass('alert-' + this.type);

        // Append clone to DOM
        this.target.append($clone);

        this.bindClose();

        // Remove alert
        window.setTimeout(function() { 
            $clone.addClass('go-away').on('transitionend', function() { $clone.remove(); });
        }, 5000);
    }

    /**
     * Removes active alerts
     * 
     * @return {void}
     */
    remove() {

        window.setTimeout(function() { 
            $('.alert').each(function() { 
                $(this).addClass('go-away').on('transitionend', function() { $(this).remove(); }); 
            });
        }, 5000);
    }

    /**
     * Binds the click on the close button
     * 
     * @return {void}
     */
    bindClose() {

        $('button.close').click(function(event) {

            event.preventDefault();
            event.stopPropagation();

            $(this).closest('.alert').addClass('go-away');
        });
    }
}

export var alert = new Alert();
