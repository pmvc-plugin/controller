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
    $c = plug('controller');
    if (is_null($app)) {
        $app = $c->getApp();
    }
    if (!is_null($app)) {
        $app = basename($c[_RUN_APPS]).'/'.$app.'/';
    }
    $folder = getAppsParent();
    $appFile = $folder.$app.$name;
    if (realpath($appFile)) {
        return $appFile;
    } else {
        return realpath($folder.$name);
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
