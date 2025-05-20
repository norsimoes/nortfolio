<?php
/**
 * ----------------------------------------------------------------------------
 * Administration interface modal template
 * ----------------------------------------------------------------------------
 */
$i18nCore = $this->_app->i18nCore;

/* ----------------------------------------------------------------------------
 * Modal close button
 * ----------------------------------------------------------------------------
 */
$modalCloseButtonHtml = '
<button type="button" class="modal-close cursor-pointer" data-bs-dismiss="modal" title="' . $i18nCore['Common'][5] . '">
    <span class="fa fa-fw fa-times-circle"></span>
</button>
';

/* ----------------------------------------------------------------------------
 * Alert close button
 * ----------------------------------------------------------------------------
 */
$closeButtonHtml = '
<button type="button" class="close cursor-pointer">
	<span aria-hidden="true" class="fa fa-times-circle"></span>
</button>
';

/* ----------------------------------------------------------------------------
 * Prepare success messages
 * ----------------------------------------------------------------------------
 */
$success = $this->_app->session->getMessage('success');

$successHtml = '';

if (!empty($success)) {

    foreach ($success as $msg) {

        $successHtml .= '<div class="alert alert-success"><span>' . $msg . '</span>' . $closeButtonHtml . '</div>';
    }

    $this->_app->session->clearMessage('success');
}

/* ----------------------------------------------------------------------------
 * Prepare error messages
 * ----------------------------------------------------------------------------
 */
$error = $this->_app->session->getMessage('error');

$errorHtml = '';

if (!empty($error)) {

    foreach ($error as $msg) {

        $errorHtml .= '<div class="alert alert-error"><span>' . $msg . '</span>' . $closeButtonHtml . '</div>';
    }

    $this->_app->session->clearMessage('error');
}

/* ----------------------------------------------------------------------------
 * Prepare information messages
 * ----------------------------------------------------------------------------
 */
$info = $this->_app->session->getMessage('info');

$infoHtml = '';

if (!empty($info)) {

    foreach ($info as $msg) {

        $infoHtml .= '<div class="alert alert-info"><span>' . $msg . '</span>' . $closeButtonHtml . '</div>';
    }

    $this->_app->session->clearMessage('info');
}

/* ----------------------------------------------------------------------------
 * Output HTML
 * ----------------------------------------------------------------------------
 */
?>

<div id="admin-alerts" data-position="bottom-left">
    <?= $successHtml . $errorHtml . $infoHtml ?>
</div>

<template id="alert">
    <div class="alert">
        <span class="alert-message"></span>
        <?= $closeButtonHtml ?>
    </div>
</template>

<div id="j-template-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <form method="post" action="" class="modal-dialog" role="form">
        <div class="modal-content">
            <div class="modal-header">
                <div id="j-template-title" class="modal-title"> </div>
                <?= $modalCloseButtonHtml ?>
            </div>
            <div id="j-template-wrapper"></div>
        </div>
    </form>
</div>

<div id="j-confirmation-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <form method="post" action="" class="modal-dialog" role="form" style="width: 500px">
        <div class="modal-content">
            <div class="modal-body text-center mt-3">
                <div id="j-icon-wrapper" class="d-none fa-stack fa-2x mb-3"><i id="j-circle"></i><i id="j-icon"></i></div>
                <div id="j-title" class="d-none mb-3"></div>
                <div id="j-text" class="d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" id="j-confirm" class="d-none btn btn-sm mr-0"></button>
                <button type="button" id="j-cancel" class="d-none btn btn-sm btn-secondary" data-bs-dismiss="modal"></button>
            </div>
        </div>
    </form>
</div>

<div id="j-translation-modal" class="modal fade">
    <form id="j-translation-form" action="" class="modal-dialog" style="width: 800px">
        <div class="modal-content">
            <div class="modal-header">
                <div id="j-translation-title" class="modal-title">translate the field <span class="field-name text-white"></span></div>
                <button type="button" class="modal-close cursor-pointer" data-bs-dismiss="modal">
                    <span class="fa fa-fw fa-times-circle"></span>
                </button>
            </div>
            <div class="modal-body">
                <div id="j-translation-wrapper"></div>
            </div>
            <div class="modal-footer pt-0">
                <button type="submit" class="btn btn-sm btn-primary me-1 cursor-pointer">
                    <?= $i18nCore['Common'][6] ?>
                </button>
                <a data-bs-dismiss="modal" class="btn btn-sm btn-secondary cursor-pointer">
                    <?= $i18nCore['Common'][3] ?>
                </a>
            </div>
        </div>
    </form>
</div>
