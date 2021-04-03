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

use PMVC\PlugIn\supervisor as sup;
use PMVC\PlugIn\supervisor\Parallel;

/*
 * Process Worker.
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

const TASK_KEY = 'PMVC\Task';
const QUEUE_KEY = 'PMVC\Queue';

// @codingStandardsIgnoreStart
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\process_worker';
class process_worker // @codingStandardsIgnoreEnd
{
    /**
     * Porcess worker invoke.
     *
     * @return ActionForward
     */
    public function __invoke()
    {
        $annotation = \PMVC\plug('annotation');
        $supervisor = \PMVC\plug('supervisor');
        $caller = $this->caller;
        $mappings = $caller->getMappings();
        $keys = $mappings->keySet();
        foreach ($keys as $key) {
            $action = $mappings->findAction($key);
            $form = $caller->processForm($action);
            $func = $caller->getActionFunc($action);
            $attrs = $annotation->getAttrs($func);
            $taskAttr = \PMVC\get($attrs['obj'], TASK_KEY);
            $queueAttr = \PMVC\get($attrs['obj'], QUEUE_KEY);
            if ($taskAttr) {
                $wrap = function () use (
                    $caller,
                    $action,
                    $form,
                    $queueAttr,
                    $func
                ) {
                    $queueDb = $this->_getQueueDb($queueAttr);
                    if ($queueAttr && $queueAttr->consumer) {
                        $form['data'] = $queueDb[null];
                    }
                    $result = call_user_func_array($func, [$action, $form]);
                    if ($queueAttr && $queueAttr->publisher && $result['ok']) {
                        $queueDb[] = $result['data'];
                    }
                };
                switch ($taskAttr->type) {
                    case sup\TYPE_DAEMON:
                        $workerGroup = $taskAttr->group;
                        $inputConcurrency = get($form, $workerGroup);
                        $concurrency =
                            !empty($inputConcurrency) &&
                            is_numeric($inputConcurrency) &&
                            $inputConcurrency > 1
                                ? $form[$workerGroup]
                                : 1;
                        for ($i = 0; $i < $concurrency; $i++) {
                            new Parallel($wrap, [
                              sup\TYPE => sup\TYPE_DAEMON,
                              sup\INTERVAL => $taskAttr->interval
                            ]);
                        }
                        break;
                    case sup\TYPE_SCRIPT:
                        new Parallel($wrap, [
                          sup\TYPE => sup\TYPE_SCRIPT,
                          sup\NAME => $action->name, 
                        ]);
                        break;  
                    default:
                        trigger_error("Wrong worker type [".$taskAttr->type."]");
                        break;
                }
            }
        }
        \PMVC\v($supervisor[sup\PARALLELS]);
        //        $supervisor->process();
    }

    /**
     * Get queue db.
     *
     * @param object $queueAttr Queue parameters.
     *
     * @return mix Db object
     */
    private function _getQueueDb($queueAttr)
    {
        $amqp = \PMVC\plug('amqp', ['host' => 'rabbitmq']);
        $queueDb = null;
        if ($queueAttr) {
            $queueDb = $amqp->getDb($queueAttr->name);
        }

        return $queueDb;
    }
}
