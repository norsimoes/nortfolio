<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$i18n = $i18n ?? [];
$url = $url;

/* ----------------------------------------------------------------------------
 * Panel content
 * ----------------------------------------------------------------------------
 */
if (!empty($loggedUser)) {

    $panelHtml = '
    <div class="user-menu link-menu">
        <div class="item-load link-item">
            <a href="' . $url->administration . '" class="user-item">' . $i18n['UserPanel'][1] . '</a>
        </div>
        <div class="item-load link-item">
            <a href="' . $url->root . '" class="user-item">' . $i18n['UserPanel'][2] . '</a>
        </div>
        <div class="item-load link-item">
            <a href="' . $url->logout . '" class="user-item">' . $i18n['UserPanel'][3] . '</a>
        </div>
    </div>
    ';

} else {

    $panelHtml = '
    <form id="form-login" method="post" action="' . $url->authenticate . '" data-target="' . $url->administration . '" data-validated="0" novalidate>
    
        <div class="input-wrapper item-load">
            <input class="input-field" autocomplete="off" placeholder=" " type="text" name="login-user" required>
            <span class="input-label" data-placeholder="' . $i18n['UserPanel'][4] . '"></span>
        </div>

        <div class="input-wrapper item-load">
            <input class="input-field" autocomplete="off" placeholder=" " type="password" name="login-pass" required>
            <span class="input-label" data-placeholder="' . $i18n['UserPanel'][5] . '"></span>
        </div>

        <div class="login-message"></div>

        <div class="item-load">
            <button class="form-submit">login</button>
        </div>

    </form>
    ';
}

/* ----------------------------------------------------------------------------
 * User overlay
 * ----------------------------------------------------------------------------
 */
?>
<div id="overlay-user" class="overlay">

    <div class="overlay-close"></div>

    <div class="overlay-title"><div><span><?= $i18n['Overlay'][5] ?></span></div></div>

    <div class="overlay-content">
        <div class="form-wrapper keep-open">
            <?= $panelHtml ?>
        </div>
    </div>

</div>
