/**
 * DataTables Hash Option
 *
 * Handles URL hash parameters to trigger dataTables features.
 *
 * @param object dtInstance
 */
var dataTablesHashOption = function (dtInstance) {

	"use strict";

	if (window.location.hash) {

		var hash = window.location.hash.substring(1);

		if (hash) {

			var hashArr = hash.split(',');

			hashArr.forEach(function (value, index, arr) {

				var triggerArr = value.split('/');

				if (triggerArr.length == 2) {

					hashArr.forEach(function (value, index, arr) {

						switch (triggerArr[0]) {

							case "dt-search": {

								dtInstance.search(decodeURIComponent(triggerArr[1])).draw();

								break;

							}

							default: {

								var dtFilterList = document.getElementsByName(triggerArr[0]);

								if (dtFilterList) {

									for (let dtFilter of dtFilterList) {

										dtFilter.value = decodeURIComponent(triggerArr[1]);

										if ("createEvent" in document) {

											var evt = document.createEvent("HTMLEvents");

											evt.initEvent("change", true, true);

											dtFilter.dispatchEvent(evt);

										} else {

											dtFilter.fireEvent("onchange");

										}
									}

								}
							}

						}

					});

				}

			});

		}

	}

};
