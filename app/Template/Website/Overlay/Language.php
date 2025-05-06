<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$i18n = $i18n ?? [];
$url = $url;

/* ----------------------------------------------------------------------------
 * Prepare languages
 * ----------------------------------------------------------------------------
 */
$languageHtml = '';

if (!empty($languages)) {

    foreach ($languages as $language) {

        $encoded = sha1($language->language_id . APP_PASSWORD_HASH);

        $languageHtml .= '
        <div class="item-load">
            <a href="#" data-id="' . $encoded . '" class="lang-item">' . strtolower($language->local_name) . '</a>
        </div>
        ';
    }
}

/* ----------------------------------------------------------------------------
 * Language overlay
 * ----------------------------------------------------------------------------
 */
?>

<div id="overlay-language" class="overlay">

    <form id="language-form" action="<?= $url->language ?>">
        <input type="hidden" name="language" value="">
    </form>

    <div class="overlay-close"></div>

    <div class="overlay-title"><div><span><?= $i18n['Overlay'][4] ?></span></div></div>

    <div class="overlay-content">

        <div class="form-wrapper keep-open link-menu">

            <?= $languageHtml ?>

        </div>

    </div>

</div>
