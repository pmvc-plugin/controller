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

/**
 * Transparent.
 *
 * @param string $name filename
 * @param string $app  app name
 *
 * @return string
 */
function transparent($name, $app = null)
{
    if (is_null($app)) {
        $app = plug('controller')->getApp().'/';
    }
    $folder = getAppsParent();
    $appFile = realpath($folder.$app.$name);
    if ($appFile) {
        return $appFile;
    } else {
        return realpath($name);
    }
}

/**
 * Get site folder.
 *
 * @return string
 */
function getAppsParent()
{
    $folder = realpath(lastSlash(plug('controller')[_RUN_APPS]).'../').'/';

    return $folder;
}

/**
 * Set App Folder.
 *
 * @param array $folders folders
 * @param array $alias   alias
 *
 * @return mixed
 */
function setAppFolders($folders, $alias = [])
{
    return folders(_RUN_APP, $folders, $alias, true);
}

/**
 * Add App Folder.
 *
 * @param array $folders folders
 * @param array $alias   alias
 *
 * @return mixed
 */
function addAppFolders($folders, $alias = [])
{
    return folders(_RUN_APP, $folders, $alias);
}
