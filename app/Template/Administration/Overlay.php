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

require_once(APP_PATH_TEMPLATE . 'Website/Overlay/Language.php');

require_once(APP_PATH_TEMPLATE . 'Website/Overlay/User.php');
