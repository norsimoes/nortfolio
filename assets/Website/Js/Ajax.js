/**
 * Performs an Ajax request and executes the received callback functions.
 *
 * @param {string} type
 * @param {string} url
 * @param data
 * @param {function} successCallback
 * @param {function} failCallback
 * @return {void}
 */
export function ajax(type, url, data, successCallback, failCallback) {

    $.ajax({

        type: type,
        url: url,
        data: data,
        dataType: 'json',

        success: (response) => {

            if (typeof response == "object") {

                if (response.hasOwnProperty("status")) {

                    if (response.status === "success") {

                        if (response.hasOwnProperty("message") && response.message) {

                            if (typeof successCallback === "function") {

                                successCallback(response);
                            }
                        }

                    } else {

                        if (typeof failCallback === "function") {

                            failCallback(response);
                        }

                        console.warn(response.message);
                    }

                } else {

                    console.warn('Status property missing!');
                }

            } else {

                console.warn('Wrong server response!');
            }
        },

        error: () => {

            if (typeof failCallback === "function") {

                failCallback();
            }
        }
    });
}
