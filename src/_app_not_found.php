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

use DomainException;

${_INIT_CONFIG
}[_CLASS] = __NAMESPACE__.'\_app_not_found';

/**
 * App Not Found Notice.
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
class _app_not_found // @codingStandardsIgnoreEnd
{
    /**
     * App not found Invoke. 
     *
     * @param array  $appAndPath Hack for pass by ref
     * @param array  $parents    Parents folders.
     * @param string $indexFile  Index file name
     * @param array  $alias      Alias map for app
     *
     * @return void
     */
    public function __invoke(
        $appAndPath,
        $parents,
        $indexFile,
        $alias
    ) {
    
        $app  =& $appAndPath['app'];
        $path =& $appAndPath['path'];
        option('set', 'httpResponseCode', 404);
        trigger_error(
            print_r(
                [
                 'Error'  => 'No app found, '.
                             'Please check following debug message.',
                 'Parent' => $parents,
                 'App'    => $app,
                 'Index'  => $indexFile,
                 'Alias'  => $alias ?: '',
                 ],
                true
            ), E_USER_WARNING
        );
        $app = $this->caller[_DEFAULT_APP];
        $path = $this->caller->getAppFile(
            $parents,
            $app,
            $indexFile,
            $alias
        );
        if (!$path) {
            throw new DomainException('Not set default app correct.');
        }
        $this->caller->setApp($app);
    }
}
