<?php

/* ----------------------------------------------------------------------------
 * Get i18n
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];
$i18n = (new \Model\Core\I18nFile())->get('Core');

/* ----------------------------------------------------------------------------
 * Prepare dashboard
 * ----------------------------------------------------------------------------
 */
$moduleData = $data['module-data'] ?: [];
$moduleHtml = '';

if (!empty($moduleData)) {

    foreach ($moduleData as $module) {

        $moduleHtml .= '
        <div class="g-col">
            <a class="dash-href" href="' . $module->target . '">
                <div class="dash-card">
                    <div class="dash-card-left">
                        <div class="dash-icon fa-fw ' . $module->icon . '"></div>
                        <div class="dash-counter record-counter" title="' . $i18n['Dashboard'][1] . '">' . $module->count . '</div>
                    </div>
                    <div class="dash-card-right">
                        <div class="card-title">' . $module->name . '</div>
                        <div class="card-desc">' . $module->desc . '</div>
                    </div>
                </div>
            </a>
        </div>
        ';
    }
}

/* ----------------------------------------------------------------------------
 * Output HTML
 * ----------------------------------------------------------------------------
 */
?>

<div class="grid-3_lg-3_md-2_sm-1-equalHeight">
    <?= $moduleHtml ?>
</div>
