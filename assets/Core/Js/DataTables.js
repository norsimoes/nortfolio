var tplDataTables = function () {

	// Grab a reference to each data-table enabled element

	var $table = $(".j-data-table:not(.dataTable)");

	// If at least one exists

	if ($table.length >= 1) {

		// For each found, bind events

		$table.each(function () {

			// The table element
			var $me = $(this);

			/*
			 * Sorting options
			 */
			var aaSortingOrdered = [];
			var aaSortingUnordered = [];

			$me.find("thead th[data-sort-column]").each(function () {

				var aaSortingEl = $(this);

				// datatables column index
				var i = aaSortingEl.index();

				// mysql sorting order
				var order = aaSortingEl.attr('data-sort-column');

				if (jQuery.inArray(order, ['asc', 'desc'])) {
					order = aaSortingEl.attr('data-sort-column');
				}

				// datatables sorting position
				var position = aaSortingEl.attr('data-sort-order');

				if (position) {
					aaSortingOrdered[position] = [i, order.toLowerCase()];
				} else {
					aaSortingUnordered.push([i, order.toLowerCase()]);
				}

			});

			if (aaSortingOrdered === undefined || aaSortingOrdered.length == 0) {
				var aaSorting = aaSortingUnordered;
			} else if (aaSortingUnordered === undefined || aaSortingUnordered.length == 0) {
				var aaSorting = aaSortingOrdered;
			} else {
				var aaSorting = [...aaSortingOrdered, ...aaSortingUnordered];
			}

			aaSorting = aaSorting.filter(n => true);

			/*
			 * Disabling Sorting
			 */
			var bSortableEl = $me.find('thead th[data-no-sort]');

			var bSortableTargets = [];

			if (bSortableEl.length >= 1) {

				bSortableEl.each(function () {

					bSortableTargets.push($(this).index());
				});
			}

			/*
			 * Script path for the I18N file
			 */
			var i18nUrl = '';

			// var dir = document.querySelector('script[src$="datatables.min.js"]').getAttribute('src');
			var dir = '/cv/lib/datatables-1.10.15/';

			if (dir) {

				var name = dir.split('/').pop();

				dir = dir.replace('/' + name, '/');

				i18nUrl = dir + 'i18n/' + $('html').attr('lang') + '.json';
			}

			/*
			 * Initialize dataTables
			 */
			var dtOptions = {
				language: {
					url: i18nUrl
				},
				"retrieve": true,
				"stateSave": true,
				"scrollX": ($me.data('scrollX') == 'true'),
				"createdRow": function (row, data, index) {

					// For each column

					$(row).find("> td").each(function (index) {

						// get this cell table heading

						var $tHead = $table.find('> thead > tr > th:nth-child(' + (index + 1) + ')');

						// search for classes to pass to the cell

						var classes = $tHead.attr("data-col-class");

						// if we have something, apply it

						if (classes) {

							$(row).find("td:nth-child(" + (index + 1) + ")").addClass(classes);
						}

					});

					// css for the last column (if none already set by the developer )

					var $lastColumn = $(row).find("td:last-child");

					if ($lastColumn.attr("class") == "") {

						$lastColumn.addClass("text-center");

					}

				},
				"columnDefs": [
					{
						targets: -1,
						className: "text-center"
					},
					{
						targets: "_all"
					}
				],
				"aoColumnDefs": [
					{
						"bSortable": false,
						"aTargets": bSortableTargets
					}
				],
				//'aaSorting': [[ aaSortingCol, aaSortingOrder.toLowerCase() ]],    // single column sorting
				"aaSorting": aaSorting,                                             // multi-column sorting
				"sPaginationType": "full_numbers",
				"initComplete": function (settings, json) {

					// adjust number of entries visual aspect
					$me.closest(".dataTables_wrapper").find(".dataTables_length label select").addClass("custom-select");

				},
				"drawCallback": function (settings) {

				}
			};

			/*
			 * Enable ajax data source
			 */
			if ($me.data('source')) {

				dtOptions.processing = true;
				dtOptions.serverSide = true;
				dtOptions.ajax = {
					url: $me.data('source'),
					data: function (data) {
						data.__source = 'hey';
					}
				}

			}

			const dtConfig = dtOptions;

			var dtInstance = $me.DataTable(dtConfig);

			/*
			 * Parse hash data
			 */
			dataTablesHashOption(dtInstance);

			/*
			 * Dropdown filters
			 */
			$me.find("thead th").each(function (index) {

				var id = $(this).attr("data-filter");

				if (id) {

					$("#" + id).on("change", function () {

						dtInstance.columns(index).search(this.value).draw();

					}).trigger("change");

				}

			});

			/*
			 * Reset Filters
			 */
			$(".j-dt-reset").on("click", function () {

				dtInstance.state.clear();

				var targetUrl = window.location.pathname + window.location.search;

				targetUrl = removeParam("dt", targetUrl);

				history.pushState("", document.title, targetUrl);

				window.location.reload(true);

			});

			/*
			 * Refresh
			 * When a modal is closed, the dataTables gets refreshed since data may have been changed server-side.
			 * Refreshes only if the datatables is powered by Ajax.
			 */
			$("*[data-dismiss='modal']").on("click", function () {

				if ($me.data("source")) {

					dtInstance.ajax.reload();
				}

			});

			$(window).on("resize", function () {

				$me.removeAttr("style");

			});

		});
	}
};

/**
 * Remove URL parameters that are filters for the dataTables, 
 * from the received URL.
 * 
 * @param {string} prefix 
 * @param {string} sourceURL 
 */
function removeParam(prefix, sourceURL) {

	var rtn = sourceURL.split("?")[0],
		param,
		params_arr = [],
		queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";

	if (queryString !== "") {

		params_arr = queryString.split("&");

		for (var i = params_arr.length - 1; i >= 0; i -= 1) {

			param = params_arr[i].split("=")[0];

			if (param.startsWith(prefix)) {

				params_arr.splice(i, 1);

			}

		}

		if (params_arr.length) {

			rtn = rtn + "?" + params_arr.join("&");

		}

	}

	return rtn;

}

/**
 * DataTables
 *
 * Initializes the dataTables jQuery plugin on DOM ready and/or after each ajax call.
 *
 * @param $
 * @param window
 * @param document
 * @param undefined
 * @returns
 */
var tplDataTables;

; (function ($, window, document, undefined) {

	"use strict";

	// Auto-initialized

	tplDataTables();

	// Initialize after an ajax call

	const send = XMLHttpRequest.prototype.send;

	XMLHttpRequest.prototype.send = function () {

		this.addEventListener('load', function () {

			if (this.readyState == 4 && this.status == 200) {

				tplDataTables();

			}

		});

		return send.apply(this, arguments);

	};

})(jQuery, window, document);
