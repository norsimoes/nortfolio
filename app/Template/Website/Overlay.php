<?php

/* ----------------------------------------------------------------------------
 * Website interface overlay template
 * ----------------------------------------------------------------------------
 */
$template = new \Controller\Website\Template();

$session = \Lib\Session::getInstance();

$loggedUser = $session->get('user');

$i18n = $template->getI18n();

$languages = $template->getLanguage();

$url = $template->templateUrl();

/* ----------------------------------------------------------------------------
 * Output html
 * ----------------------------------------------------------------------------
 */

require_once(__DIR__ . '/Overlay/QRCode.php');

require_once(__DIR__ . '/Overlay/Download.php');

require_once(__DIR__ . '/Overlay/Language.php');

require_once(__DIR__ . '/Overlay/User.php');
