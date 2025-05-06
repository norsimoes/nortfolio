<?php

/* ----------------------------------------------------------------------------
 * Website interface css template
 * ----------------------------------------------------------------------------
 */
$files = [

    /*
     * Vendors
     */
    'lib/bootstrap-5.1.3/css/bootstrap.min.css',
    'lib/fontawesome-free-5.15.1/css/all.min.css',
    'lib/datatables-1.10.15/datatables.min.css',
    'lib/bootstrap-select-1.14.0/css/bootstrap-select.min.css',
    'lib/fontawesome-iconpicker-master/dist/css/fontawesome-iconpicker.min.css',
    'lib/gridlex-master/dist/gridlex.css',

    /*
     * Core
     */
    'assets/Core/Css/Root.css',
    'assets/Core/Css/Alert.css',
    'assets/Core/Css/DataTables.css',
    'assets/Core/Css/BootstrapSelect.css',
    'assets/Core/Css/IconPicker.css',
    'assets/Core/Css/InnerBreadcrumb.css',
    'assets/Core/Css/TranslationField.css',
    'assets/Core/Css/ToggleSwitch.css',
    'assets/Core/Css/Sort.css',
    'assets/Core/Css/Modal.css',

    /*
     * Interface
     */
    'assets/Administration/Css/Init.css',
    'assets/Website/Css/Init.css',
    'assets/Website/Css/Overlay.css',
];

foreach ($files as $file) {
    
    echo PHP_EOL . '    <link rel="stylesheet" type="text/css" media="all" href="' . APP_URL . $file . '">';
}
