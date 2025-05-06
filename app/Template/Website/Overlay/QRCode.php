<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$i18n = $i18n ?? [];

/* ----------------------------------------------------------------------------
 * QRCode overlay
 * ----------------------------------------------------------------------------
 */
?>
<div id="overlay-qrcode" class="overlay">

    <div class="overlay-close"></div>

    <div class="overlay-title"><div><span><?= $i18n['Overlay'][1] ?></span></div></div>

    <div class="overlay-content">

        <img src="<?= APP_URL_ASSETS ?>Website/Img/Dot.png" data-src="<?= APP_URL_ASSETS ?>Website/Img/QRCode.png" alt="QRCode" width="1" height="1" data-tilt>

    </div>

</div>
