<?php
/**
 * PMVC.
 *
 * This file only use in
 * "Global Option", "Mapping Option", "Plugin Option".
 * Other constant should put in namespace.constants.php
 *
 * PHP version 5
 *
 * @category CategoryName
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @version GIT: <git_id>
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */

/**
 * MVC.
 */

namespace {
    /* Builder */
    define('_INIT_BUILDER', '_init_builder_');

    /* Action */
    define('_FUNCTION', '_function_');
    define('_FORM', '_form_');
    define('_SCOPE', '_scope_');
    define('_VALIDATE', '_validate_');

    /* Forward */
    define('_ACTION', '_action_');
    define('_HEADER', '_header_');
    define('_PATH', '_path_');
    define('_TYPE', '_type_');
    define('_FORWARD', '_forward_');
    define('_ERROR_FORWARD', '_error_forward_');

    /* Options */
    define('_ROUTER', '_router_');
    define('_TEMPLATE_DIR', '_template_dir_');
    define('_DEFAULT_APP', '_default_app_');
    define('_DEFAULT_FORM', '_default_form_');

    /* Run */
    define('_REAL_APP', '_real_app_');
    define('_RUN_APP', '_run_app_');
    define('_RUN_APPS', '_run_apps_');
    define('_RUN_ACTION', '_run_action_');
    define('_RUN_FORM', '_run_form_');
}

namespace PMVC {
    const ACTION_FORMS = '__action_forms__';
    const ACTION_MAPPINGS = '__action_mappings__';
    const ACTION_FORWARDS = '__action_forwards__';
}
