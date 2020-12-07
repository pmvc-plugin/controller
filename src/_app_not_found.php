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

/*
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
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\app_not_found';
class app_not_found // @codingStandardsIgnoreEnd
{
    /**
     * App not found invoke.
     *
     * @param array  $parents   Parents folders.
     * @param string $indexFile Index file name
     * @param array  $folders   For pass alias information.
     *
     * @return void
     */
    public function __invoke(
        $parents,
        $indexFile,
        $folders
    ) {
        if (\PMVC\isDev('help')) {
            return;
        }
        $alias = $folders['alias'];
        option('set', 'httpResponseCode', 404);
        $caller = $this->caller;
        trigger_error(
            json_encode(
                [
                    'Error' => 'No app found with routrs, '.
                                'Please check following debug message.',
                    'Debug' => [
                        'Parent' => $parents,
                        'App'    => $caller[_REAL_APP],
                        'Index'  => $indexFile,
                        'Alias'  => $alias ?: '',
                    ],
                ]
            ),
            E_USER_WARNING
        );
        $caller[_REAL_APP] = $caller[_DEFAULT_APP];
        $path = $caller->getAppFile(
            $parents,
            $indexFile
        );
        if (!$path) {
            throw new DomainException(
                'Default app setting is not correct. ['.
                $caller[_DEFAULT_APP].
                ']'
            );
        }
        $caller->setApp($caller[_REAL_APP]);

        return $path;
    }
}
