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

/*
 * Get Apps Parent Folder.
 *
 * @category CategoryName
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
// @codingStandardsIgnoreStart
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\_get_apps_parent';
class _get_apps_parent // @codingStandardsIgnoreEnd
{
    /**
     * App not found invoke.
     *
     * @return string
     */
    public function __invoke()
    {
        $folder = realpath(lastSlash($this->caller[_RUN_APPS]).'../').'/';

        return $folder;
    }
}
