<?php

/* ----------------------------------------------------------------------------
 * Website interface template
 * ----------------------------------------------------------------------------
 */
$_i18n = $_i18n ?? '';
$_meta = $_meta ?? '';
$_css = $_css ?? '';
$_jsHead = $_jsHead ?? '';
$_jsBody = $_jsBody ?? '';
$_view = $_view ?? '';

?>

<!doctype html>
<html lang="<?= $_i18n ?>">
<head>
<?php

    // Meta tags
    require('Meta.php');

    // Meta tags added by the module
    echo $_meta;

    // CSS
    require('Css.php');

    // Css added by the module
    echo $_css;

    // JS head scripts added by the module
    echo $_jsHead;

?>


</head>

<body>

<?php

	// Header
    require('Header.php');

    // Start main
    echo '<div id="admin-wrapper">';

    // Menu
    require('Menu.php');

    // Start view content
    echo '<div id="admin-content">';

	// Main view
	echo preg_replace('/(?<=>)\s+(?=<)/', '', $_view);

    // End view content
	echo '</div>';

    // End main
	echo '</div>';

    // Overlay
    require('Overlay.php');

    // Modal
    require 'Modal.php';

    // JS
    require('Js.php');

    // JS added by the module
    echo $_jsBody;

?>

</body>
</html>
