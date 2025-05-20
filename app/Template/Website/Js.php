<?php

/* ----------------------------------------------------------------------------
 * Website interface js template
 * ----------------------------------------------------------------------------
 */
$files = [

    // Vendors
    'lib/jquery-3.5.1/jquery-3.5.1.min.js' => 'text/javascript',
    'lib/tilt.js/tilt.jquery.js' => 'text/javascript',

    // Interface
    'assets/Website/Js/Init.js' => 'module',

];

foreach ($files as $file => $type) {

    $typeAttr = $type != 'text/javascript' ? ' type="' . $type . '"' : '';

    $version = APP_DEBUG ? "?v=" . time() : "";

    echo PHP_EOL . '<script src="' . APP_URL . $file . $version . '"' . $typeAttr . '></script>';
}
