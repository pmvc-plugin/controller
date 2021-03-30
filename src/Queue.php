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
 * Queue Attribute.
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
class Queue
{
    public $name;
    public $consumer;
    public $publisher;

    /**
     * Queue construct. 
     *
     * @param string $name    Queue name.
     * @param array  $payload Queue parameters.
     *
     * @return void 
     */
    public function __construct($name, $payload = [])
    {
        $this->name = $name;
        $this->consumer = in_array('consumer', $payload);
        $this->publisher = in_array('publisher', $payload);
    }
}
