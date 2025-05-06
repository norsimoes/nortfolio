<?php

/* ----------------------------------------------------------------------------
 * Website interface header template
 * ----------------------------------------------------------------------------
 */
$template = new \Controller\Website\Template();

$moduleModel = new \Model\Core\Module();

$session = \Lib\Session::getInstance();

$loggedUser = $session->get('user');

$i18n = $template->getI18n();

$i18nIso2 = $session->getI18n('iso2');

/* ----------------------------------------------------------------------------
 * User name
 * ----------------------------------------------------------------------------
 */
$userName = $loggedUser ? strtolower($loggedUser->name) : '';

$loggedInClass = $loggedUser ? 'logged-in' : '';

/* ----------------------------------------------------------------------------
 * Output html
 * ----------------------------------------------------------------------------
 */
?>

<div id="website-header">

    <div id="header-info">

        <span class="icon-main icon-terminal"></span>

        <span class="user-name ms-3">nortfolio.pt</span>

    </div>

    <div id="header-icons">

        <span class="user-name"><?= $userName ?></span>

        <span class="icon-header icon-<?= $i18nIso2 ?> overlay-trigger" data-target="overlay-language" data-tooltip="<?= $i18n['Tooltip'][4] ?>"></span>
        <span class="icon-user-wrapper <?= $loggedInClass ?>">
            <span class="icon-header icon-user overlay-trigger <?= $loggedInClass ?>" data-target="overlay-user" data-tooltip="user panel"></span>
        </span>

    </div>

</div>
