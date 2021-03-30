<?php
/**
 * PMVC.
 *
 * PHP version 8
 *
 * @category Worker
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

#[Attribute]
/**
 * Task Attribute.
 *
 * @category Worker
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class Task
{
    public $type;
    public $interval;

    /**
     * Task construct.
     *
     * @param string $type    Task type.
     * @param array  $payload Task parameters.
     *
     * @return void
     */
    public function __construct($type, $payload = [])
    {
        $this->type = $type;
        $this->interval = \PMVC\get($payload, 'interval', 10);
    }
}
