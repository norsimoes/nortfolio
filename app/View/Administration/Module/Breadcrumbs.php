<?php

/* ----------------------------------------------------------------------------
 * Get data
 * ----------------------------------------------------------------------------
 */
$data = $data ?? [];

/* ----------------------------------------------------------------------------
 * Prepare breadcrumbs
 * ----------------------------------------------------------------------------
 */
$breadcrumbsHtml = '';

foreach ($data as $crumb) {

    if ($crumb->url) {

        $breadcrumbsHtml .= '<a class="breadcrumb-item" href="' . $crumb->url . '" title="' . $crumb->call_sign . '">' . $crumb->name . '</a>';

    } else {

        $breadcrumbsHtml .= '<span class="breadcrumb-item active" title="' . $crumb->call_sign . '">' . $crumb->name . '</span>';
    }
}

/* ----------------------------------------------------------------------------
 * Output HTML
 * ----------------------------------------------------------------------------
 */
?>

<div class="inner-breadcrumb">
    <div class="fa fa-chevron-right"></div>
    <?= $breadcrumbsHtml ?>
</div>
