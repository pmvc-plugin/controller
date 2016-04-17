<?php
/**
 * PMVC.
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
namespace PMVC;

/* Action */
if (defined('\PMVC\ACTION_FORMS')) {
    return;
}
const ACTION_FORMS = '__action_forms__';
const ACTION_MAPPINGS = '__action_mappings__';
const ACTION_FORWARDS = '__action_forwards__';
