<?php

/* ----------------------------------------------------------------------------
 * Website interface css template
 * ----------------------------------------------------------------------------
 */
$files = [

    // Interface
    'assets/Website/Css/Init.css' => 'screen',
    'assets/Website/Css/Overlay.css' => 'screen',
    'assets/Website/Css/Darkwave.css' => 'screen',
    'assets/Website/Css/Print.css' => 'print',
];

foreach ($files as $file => $media) {

    echo PHP_EOL . '    <link rel="stylesheet" media="' . $media . '" href="' . APP_URL . $file . '">';
}
