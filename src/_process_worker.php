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
 * Process Worker.
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

const taskKey = "PMVC\Task";
const queueKey = "PMVC\Queue";

// @codingStandardsIgnoreStart
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . "\process_worker";
class process_worker // @codingStandardsIgnoreEnd
{
    /**
     * Porcess worker invoke.
     *
     * @return ActionForward
     */
    public function __invoke()
    {
        $annotation = \PMVC\plug("annotation");
        $supervisor = \PMVC\plug("supervisor");
        $caller = $this->caller;
        $mappings = $caller->getMappings();
        $keys = $mappings->keySet();
        foreach ($keys as $key) {
            $action = $mappings->findAction($key);
            $func = $caller->getActionFunc($action);
            $attrs = $annotation->getAttrs($func);
            $taskAttr = \PMVC\get($attrs["obj"], taskKey);
            $queueAttr = \PMVC\get($attrs["obj"], queueKey);
            if ($taskAttr) {
                $wrap = function () use ($caller, $action, $queueAttr, $taskAttr, $func) {
                    $form = $caller->processForm($action);
                    $queueDb = $this->getQueueDb($queueAttr);
                    if ($queueAttr && $queueAttr->consumer) {
                        $form["data"] = $queueDb[null];
                    }
                    $result = call_user_func_array($func, [$action, $form]);
                    if ($queueAttr && $queueAttr->publisher && $result["ok"]) {
                        $queueDb[] = $result["data"];
                    }
                };
                switch ($taskAttr->type) {
                    case "daemon":
                        $supervisor->daemon($wrap, [], $taskAttr->interval);
                        break;
                    case "script":
                        $supervisor->script($wrap, []);
                    default:
                        break;
                }
            }
        }
        $supervisor->process();
    }

    public function getQueueDb($queueAttr)
    {
        $amqp = \PMVC\plug("amqp", ["host" => "rabbitmq"]);
        $queueDb = null;
        if ($queueAttr) {
            $queueDb = $amqp->getDb($queueAttr->name);
        }
        return $queueDb;
    }
}
