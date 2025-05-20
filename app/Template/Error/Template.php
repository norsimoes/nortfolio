<?php

/* ----------------------------------------------------------------------------
 * Error page template
 * ----------------------------------------------------------------------------
 */
$_view = $_view ?? '';

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>Nortfolio</title>

        <style>
            html, body { height: 100%; }
            body { background: #222228; color: #eee; margin: 0; font-family: sans-serif; }
            .wrapper { display: flex; justify-content: center; align-items: center; flex-direction: row; height: 100%; }
            .error { display: flex; justify-content: center; align-items: center; flex-direction: column; width: 50%; height: 100%; font-size: 1.8rem; }
            .error > p { margin-bottom: 0; }
            svg { width: 200px; fill: #eee; }
            .info { display: flex; align-items: center; background: #2a2a31; width: 50%; height: 100%; }
            .view { padding: 2rem; }
            .view > p { margin-top: 0.1rem; margin-bottom: 0.5rem; }
            .view > p.trace-id { margin-top: 0.1rem; margin-bottom: 0.1rem; color: #eee; }
            .view > p.trace { margin-top: 0.1rem; margin-bottom: 0.1rem; color: #888; }
            label { font-size: 0.8rem; color: #888; }
        </style>
    </head>

    <body>
        <?= $_view ?>
    </body>

</html>
