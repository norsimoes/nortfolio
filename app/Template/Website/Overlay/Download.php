<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$i18n = $i18n ?? [];

/* ----------------------------------------------------------------------------
 * Download overlay
 * ----------------------------------------------------------------------------
 */
?>
<div id="overlay-download" class="overlay">

    <div class="overlay-close"></div>

    <div class="overlay-title"><div><span><?= $i18n['Overlay'][2] ?></span></div></div>

    <div class="overlay-content">

        <div class="form-wrapper keep-open link-menu">

            <div class="item-load">
                <a href="<?= APP_URL_ASSETS . 'Website/Pdf/CVNorEN.pdf' ?>" class="dl-item" download>english</a>
            </div>

            <div class="item-load">
                <a href="<?= APP_URL_ASSETS . 'Website/Pdf/CVNorPT.pdf' ?>" class="dl-item" download>português</a>
            </div>

        </div>

    </div>

</div>
