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
 * Process Error.
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
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\process_error';
class process_error // @codingStandardsIgnoreEnd
{
    /**
     * Porcess error invoke.
     *
     * @param array $allErrors All Errors.
     *
     * @return ActionForward
     */
    public function __invoke($allErrors)
    {
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\WILL_PROCESS_ERROR, true,
            ]
        );
        $thisForward = getOption(_ERROR_FORWARD, 'error');
        $mappings = $this->caller->getMappings();
        if (!$mappings || !$mappings->forwardExists($thisForward)) {
            return false;
        }
        $errorForward = $mappings->findForward($thisForward);
        $errorForward->set(
            [
                'errors'    => $allErrors[USER_ERRORS],
                'lastError' => $allErrors[USER_LAST_ERROR],
            ]
        );

        return $errorForward;
    }
}
