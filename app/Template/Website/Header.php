<?php

/* ----------------------------------------------------------------------------
 * Website interface header template
 * ----------------------------------------------------------------------------
 */
$template = new \Controller\Website\Template();

$session = \Lib\Session::getInstance();

$i18n = $template->getI18n();

$i18nIso2 = $session->getI18n('iso2');

$loggedUser = $session->get('user');

/* ----------------------------------------------------------------------------
 * Prepare data
 * ----------------------------------------------------------------------------
 */
$userName = $loggedUser ? strtolower($loggedUser->name) : '';

$loggedInClass = $loggedUser ? 'logged-in' : '';

/* ----------------------------------------------------------------------------
 * Output html
 * ----------------------------------------------------------------------------
 */
?>

<header id="website-header">

    <div id="header-info">

        <span class="user-name"><?= $userName ?></span>

        <span class="icon-main icon-terminal"></span>

        <div id="header-tooltip-wrapper">
            <template id="header-tooltip">
                <div class="header-tooltip"></div>
            </template>
        </div>

    </div>

    <div id="header-icons">

        <span class="icon-header icon-qrcode overlay-trigger" data-target="overlay-qrcode" data-tooltip="<?= $i18n['Tooltip'][1] ?>"></span>
        <span class="icon-header icon-download overlay-trigger" data-target="overlay-download" data-tooltip="<?= $i18n['Tooltip'][2] ?>"></span>
        <span class="icon-header icon-<?= $i18nIso2 ?> overlay-trigger" data-target="overlay-language" data-tooltip="<?= $i18n['Tooltip'][4] ?>"></span>
        <span class="icon-user-wrapper <?= $loggedInClass ?>">
            <span class="icon-header icon-user overlay-trigger <?= $loggedInClass ?>" data-target="overlay-user" data-tooltip="<?= $i18n['Tooltip'][5] ?>"></span>
        </span>

    </div>

</header>
