<?php

/* ----------------------------------------------------------------------------
 * Get i18n
 * ----------------------------------------------------------------------------
 */
$i18n = $data['i18n'] ?? [];

/* ----------------------------------------------------------------------------
 * Prepare skills list
 * ----------------------------------------------------------------------------
 */
$skillTreeHtml = '';

$skillData = $data['skill-list'] ?? [];

$defaultState = [
    $i18n['Skill'][2] => ['caret' => 'caret-down', 'active' => 'active'],
    $i18n['Skill'][3] => ['caret' => 'caret-down', 'active' => 'active'],
    $i18n['Skill'][4] => ['caret' => '', 'active' => ''],
];

if (!empty($skillData)) {

    $blockHtml = '';

    foreach ($skillData as $blockKey => $skillBlockArr) {

        $typeHtml = '';

        foreach ($skillBlockArr as $typeKey => $skillTypeArr) {

            $skillHtml = '';

            foreach ($skillTypeArr as $skill) {

                /*
                 * Output skill entry
                 */
                $bgStyle = '';

                if ($skill->icon) {

                    $bgUrl = APP_URL_CDN . 'skill/' . $skill->icon;
                    $bgStyle = 'style="background: url(' . $bgUrl . ') no-repeat center center / cover"';
                }

                $tooltip = $skill->override ?: $i18n['Tooltip'][6] . ' ' . $skill->value;

                $skillHtml .= '
                <li>
                    <div class="skill-wrapper overlay-trigger" data-tooltip="' . $tooltip . '">
                        <div>
                            <div class="tree-icon" ' . $bgStyle . '></div>
                            ' . $skill->name . '
                        </div>
                        <div class="bar-wrapper">
                            <div class="bar-fg" data-width="' . 10 * $skill->value . '"></div>
                        </div>
                    </div>
                </li>
                ';
            }

            /*
             * Output skill type
             */
            $typeHtml .= '
            <li>
                <span class="caret ' . $defaultState[$typeKey]['caret'] . '">
                    ' . $typeKey . '
                </span>
                <ul class="nested ' . $defaultState[$typeKey]['active'] . ' l-3">
                    ' . $skillHtml . '
                </ul>
            </li>
            ';
        }

        /*
        * Output skill block
        */
        $blockHtml .= '
        <li>
            <span class="caret caret-down">
                ' . $blockKey . '
            </span>
            <ul class="nested active l-2">
                ' . $typeHtml . '
            </ul>
        </li>
        ';
    }

    // Output skill tree
    $skillTreeHtml .= '<ul class="l-1">' . $blockHtml . '</ul>';
}

/* ----------------------------------------------------------------------------
 * Prepare profile list
 * ----------------------------------------------------------------------------
 */
$profileTreeHtml = '';

$profileData = $data['profile-list'] ?? [];

$defaultState = [
    $i18n['Profile'][2] => ['caret' => 'caret-down', 'active' => 'active'],
    $i18n['Profile'][3] => ['caret' => 'caret-down', 'active' => 'active'],
    $i18n['Profile'][4] => ['caret' => '', 'active' => ''],
];

if (!empty($profileData)) {

    $blockHtml = '';

    foreach ($profileData as $blockKey => $profileBlockArr) {

        $typeHtml = '';

        foreach ($profileBlockArr as $typeKey => $profileTypeArr) {

            $profileHtml = '';

            foreach ($profileTypeArr as $profile) {

                /*
                 * Output profile entry
                 */
                $bgStyle = '';

                if ($profile->icon) {

                    $bgUrl = APP_URL_CDN . 'profile/' . $profile->icon;
                    $bgStyle = 'style="background: url(' . $bgUrl . ') no-repeat center center / cover"';
                }

                $name = $profile->name;
                $tooltipClass = '';
                $tooltipData = '';

                if ($profile->url) {

                    $name = '<a href="' . $profile->url . '" target="_blank" rel="noopener noreferrer" class="cv-url">' . $profile->name . '</a>';
                    $tooltipClass = 'overlay-trigger';
                    $tooltipData = 'data-tooltip="' . $profile->tooltip . '"';
                }

                $profileHtml .= '
                <li>
                    <div class="skill-wrapper ' . $tooltipClass . '"  ' . $tooltipData . '>
                        <div>
                            <div class="tree-icon" ' . $bgStyle . '></div>
                            ' . $name . '
                        </div>
                    </div>
                </li>
                ';
            }

            /*
             * Output profile type
             */
            $typeHtml .= '
            <li>
                <span class="caret ' . $defaultState[$typeKey]['caret'] . '">
                    ' . $typeKey . '
                </span>
                <ul class="nested ' . $defaultState[$typeKey]['active'] . ' l-3">
                    ' . $profileHtml . '
                </ul>
            </li>
            ';
        }

        /*
        * Output profile block
        */
        $blockHtml .= '
        <li>
            <span class="caret caret-down">
                ' . $blockKey . '
            </span>
            <ul class="nested active l-2">
                ' . $typeHtml . '
            </ul>
        </li>
        ';
    }

    // Output profile tree
    $profileTreeHtml .= '<ul class="l-1">' . $blockHtml . '</ul>';
}

/* ----------------------------------------------------------------------------
 * Prepare experience list
 * ----------------------------------------------------------------------------
 */
$experienceHtml = '';

$experienceData = $data['experience-list'] ?? [];

$lastKey = array_key_last($experienceData);

if (!empty($experienceData)) {

    foreach ($experienceData as $key => $experience) {

        $techHtml = '';

        if ($experience->tech) {

            $techHtml = addLine('',3,'// ' . $experience->tech);
        }

        $experienceHtml .= addLine('prop-text', 2,
            '<span class="prop-method">' . $experience->name . '</span> (<span class="prop-arg">' . $experience->start . '</span>, <span class="prop-arg">' . $experience->end . '</span>)');
        $experienceHtml .= addLine('prop-text', 2, '{');
        $experienceHtml .= addLine('prop-class', 3, $experience->company);
        $experienceHtml .= addLine('prop-string', 3, $experience->location);
        $experienceHtml .= addLine('prop-text', 3, $experience->description);
        $experienceHtml .= $techHtml;
        $experienceHtml .= addLine('prop-text', 2, '}');

        if ($key != $lastKey) {

            $experienceHtml .= addLine('', 2, '');
        }
    }
}

/* ----------------------------------------------------------------------------
 * Prepare education list
 * ----------------------------------------------------------------------------
 */
$educationHtml = '';

$educationData = $data['education-list'] ?? [];

$lastKey = array_key_last($educationData);

if (!empty($educationData)) {

    foreach ($educationData as $key => $education) {

        $educationHtml .= addLine('prop-text', 2,
            '<span class="prop-method">' . $education->institution . '</span> (<span class="prop-arg">' . $education->start . '</span>, <span class="prop-arg">' . $education->end . '</span>)');
        $educationHtml .= addLine('prop-text', 2, '{');
        $educationHtml .= addLine('prop-class',3, $education->course);
        $educationHtml .= addLine('prop-text',3, $education->description);
        $educationHtml .= addLine('',3, '// ' . $education->grade);
        $educationHtml .= addLine('prop-text',2, '}');

        if ($key != $lastKey) {

            $educationHtml .= addLine('', 2, '');
        }
    }
}

/* ----------------------------------------------------------------------------
 * Add line function
 * ----------------------------------------------------------------------------
 */
function addLine($class, $indent, $content): string
{
    if (empty($content)) $content = '&nbsp;';
    $content = str_replace(' *', '&nbsp;*', $content);

    $text = $indent ? '<div class="i-' . $indent . '"><span>' . $content . '</span></div>' : '<span>' . $content . '</span>';

    return '
    <div class="cv-line">
        <div class="line-number"></div>
        <div class="line-text ' . $class . '">' . $text . '</div>
    </div>
    ';
}

/* ----------------------------------------------------------------------------
 * Output HTML
 * ----------------------------------------------------------------------------
 */
?>

<section id="cv-info">

    <h1 class="d-none">Nortfolio :: Online portfolio of Nor</h1>
    <h2 class="d-none">Online portfolio for the work of Nor, full stack developer focused in PHP, MySQL, JavaScript, HTML and CSS, with a background in graphic design and music.</h2>
    <h3 class="d-none">https://nortfolio.pt</h3>

    <div id="cv-info-scroll">

        <div class="tree">
            <?= $skillTreeHtml ?>
        </div>

        <div class="tree">
            <?= $profileTreeHtml ?>
        </div>

    </div>

</section>

<section id="cv-vitae">

    <h2 class="d-none">Nortfolio :: PHP, MySQL, JavaScript, jQuery, HTML and CSS</h2>
    <h3 class="d-none">Norberto Simões :: Teacher, musician and designer by training. Self-taught programmer and graphic designer. Creative, committed and versatile.</h3>

    <div id="cv-header">
        <span class="caret caret-down"></span>CV
    </div>

    <?= addLine('', 0, '/**') ?>
    <?= addLine('', 0, ' * <span class="prop-name">NORBERTO SIMÕES...</span>') ?>
    <?= addLine('', 0, ' *') ?>
    <?= addLine('', 0, ' * ' . $i18n['Vitae'][1]) ?>
    <?= addLine('', 0, ' * ' . $i18n['Vitae'][2]) ?>
    <?= addLine('', 0, ' * ' . $i18n['Vitae'][3]) ?>
    <?= addLine('', 0, ' *') ?>
    <?= addLine('', 0, ' * <span class="prop-arg">@since</span> 1976-04-29') ?>
    <?= addLine('', 0, ' */') ?>
    <?= addLine('prop-cv', 0, 'CURRICULUM VITAE') ?>
    <?= addLine('prop-text', 0, '{') ?>
    <?= addLine('prop-text', 1, '<span class="prop-class2">' . $i18n['Vitae'][4] . '</span>()') ?>
    <?= addLine('prop-text', 1, '{') ?>
    <?= $experienceHtml ?>
    <?= addLine('prop-text', 1, '}') ?>
    <?= addLine('', 1, '') ?>
    <?= addLine('prop-text', 1, '<span class="prop-class2">' . $i18n['Vitae'][5] . '</span>()') ?>
    <?= addLine('prop-text', 1, '{') ?>
    <?= $educationHtml ?>
    <?= addLine('prop-text', 1, '}') ?>
    <?= addLine('prop-text', 0, '}') ?>
    <?= addLine('', 1, '') ?>

</section>
