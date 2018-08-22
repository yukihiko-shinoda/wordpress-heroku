<?php
/**
 * Do not edit this file. Edit the config files found in the config/ dir instead.
 * This file is required in the root directory so WordPress can find it.
 * WP is hardcoded to look in its own directory or one directory up for wp-config.php.
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/application.php';
require_once WP_CONTENT_DIR.'/Replacement.php';
require_once WP_CONTENT_DIR.'/StrReplacement.php';
require_once WP_CONTENT_DIR.'/PregReplacement.php';

//require_once ABSPATH . 'wp-settings.php';
//require( ABSPATH . WPINC . '/load.php' );

$replacesOptionStr = [
    '<?php'   => '',
    'if ( ! $alloptions ) {' => 'if ( ! $alloptions && strcmp(\'f\', $wpdb->get_var(\'SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE  table_catalog = \\\'\' . DB_NAME . \'\\\' AND table_name = \\\'\' . $wpdb->options . \'\\\')\')) !== 0) {',
    '$serialized_value = maybe_serialize( $value );' => '$serialized_value = is_null($value) ? \'\' : maybe_serialize( $value );',
];
$replacesFunctionsStr = [
    'require( ABSPATH . WPINC . \'/option.php\' );' => new StrReplacement($replacesOptionStr, 'ABSPATH . WPINC . \'/option.php\''),
    'if ( !isset( $alloptions[\'siteurl\'] ) )' => "if ( !isset( \$alloptions['siteurl'] ) && strcmp('f', \$wpdb->get_var('SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE  table_catalog = \\'' . DB_NAME . '\\' AND table_name = \\'' . \$wpdb->options . '\\')')) === 0 )\n		\$installed = false;\n    elseif ( !isset( \$alloptions['siteurl'] ) )",
];
$replacesCommentStr = [
    '<?php'   => '',
//    'OBJECT'  => 'OBJECT_PG4WP',
];
$replacesClassWPQueryStr = [
    '<?php'                 => '',
    'RAND()'                => 'random()',
    '/RAND\(([0-9]+)\)/i'   => '/random\(([0-9]+)\)/i',
    'RAND(%s)'              => 'random(%s)',
//    'OBJECT'                => 'OBJECT_PG4WP',
];
$replacesFormattingPreg = [
    '/<\?php/' => '',
];
$replacesFormattingStr = [
    'RAND\(\s*\)' => 'random\(\s*\)',
];
$replacesFunctionsPreg = [
    '/<\?php/' => '',
];
$contentFormatting = new StrReplacement($replacesFormattingStr, new PregReplacement($replacesFunctionsPreg, 'ABSPATH . WPINC . \'/formatting.php\'', 1));
$replacesLoadStr = [
    'require_once( ABSPATH . WPINC . \'/wp-db.php\' );' => new Replacement('WP_CONTENT_DIR.\'/pg4wp_core.php\''),
    'require( ABSPATH . WPINC . \'/formatting.php\' );' => $contentFormatting,
];
$replacesLoadPreg = [
    '/<\?php/' => '',
];
$replacesPostStr = [
    '<?php'     => '',
    'RAND()'    => 'random()'
//    'OBJECT'  => 'OBJECT_PG4WP',
];
$replacesTaxonomyStr = [
    '<?php'   => '',
//    'OBJECT'  => 'OBJECT_PG4WP',
];
$replacesWpSettings = [
    '<?php'		                                            => '',
    'require( ABSPATH . WPINC . \'/comment.php\' );'        => new StrReplacement($replacesCommentStr, 'ABSPATH . WPINC . \'/comment.php\''),
    'require( ABSPATH . WPINC . \'/class-wp-query.php\' );' => new StrReplacement($replacesClassWPQueryStr, 'ABSPATH . WPINC . \'/class-wp-query.php\''),
    'require( ABSPATH . WPINC . \'/formatting.php\' );'     => $contentFormatting,
    'require( ABSPATH . WPINC . \'/functions.php\' );'      => new StrReplacement($replacesFunctionsStr, new PregReplacement($replacesFunctionsPreg,'ABSPATH . WPINC . \'/functions.php\'', 1)),
    'require( ABSPATH . WPINC . \'/load.php\' );'           => new StrReplacement($replacesLoadStr, new PregReplacement($replacesLoadPreg,'ABSPATH . WPINC . \'/load.php\'', 1)),
    'require( ABSPATH . WPINC . \'/post.php\' );'           => new StrReplacement($replacesPostStr, 'ABSPATH . WPINC . \'/post.php\''),
    'require( ABSPATH . WPINC . \'/taxonomy.php\' );'       => new StrReplacement($replacesTaxonomyStr, 'ABSPATH . WPINC . \'/taxonomy.php\''),
];
$replacementWpSettings = new StrReplacement($replacesWpSettings,'ABSPATH . \'wp-settings.php\'');

/** @noinspection PhpUnhandledExceptionInspection */
$renderReplacedCode = $replacementWpSettings->renderReplacedCode();
//throw new Exception(eval($renderReplacedCode));
eval(eval($renderReplacedCode));

if (strcmp(DB_DRIVER, 'pgsql') === 0) {
//    ↓ This has not supported mysqli_* functions yet...
//    require_once( PG4WP_ROOT.'/core.php');
    require_once( WP_CONTENT_DIR.'/pg4wp_core.php');
} // Protection against multiple loading
