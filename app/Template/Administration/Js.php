<?php

/* ----------------------------------------------------------------------------
 * Website interface js template
 * ----------------------------------------------------------------------------
 */
$files = [

    /*
     * Vendors
     */
    'lib/jquery-3.5.1/jquery.min.js' => 'text/javascript',
    'lib/popper-1.16.0/popper.min.js' => 'text/javascript',
    'lib/bootstrap-5.1.3/js/bootstrap.bundle.min.js' => 'text/javascript',
    'lib/datatables-1.10.15/datatables.min.js' => 'text/javascript',
    'lib/bootstrap-select-1.14.0/js/bootstrap-select.min.js' => 'text/javascript',
    'lib/fontawesome-iconpicker-master/dist/js/fontawesome-iconpicker.js' => 'text/javascript',
    'lib/sortable-1.7.0/sortable.min.js' => 'text/javascript',
    'lib/tilt.js/tilt.jquery.js' => 'text/javascript',

    /*
     * Core
     */
    'assets/Core/Js/DataTablesHashOption.js' => 'text/javascript',
    'assets/Core/Js/DataTables.js' => 'text/javascript',
    'assets/Core/Js/ToggleAvailable.js' => 'module',
    'assets/Core/Js/ToggleSwitch.js' => 'module',
    'assets/Core/Js/Sort.js' => 'module',
    'assets/Core/Js/TemplateModal.js' => 'module',
    'assets/Core/Js/ConfirmationModal.js' => 'text/javascript',

    /*
     * Interface
     */
    'assets/Administration/Js/Init.js' => 'module',
];

foreach ($files as $file => $type) {

    $version = APP_DEBUG ? "?v=" . time() : "";

    echo PHP_EOL . '<script type="' . $type . '" src="' . APP_URL . $file . $version . '"></script>';
}
