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

const attrKey = 'PMVC\Task';

// @codingStandardsIgnoreStart
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\process_worker';
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
      $amqp = \PMVC\plug('amqp', ["host" => "rabbitmq"]);
      $hello = $amqp->getDb('hello');
      $caller = $this->caller;
      $mappings = $caller->getMappings();
      $keys = $mappings->keySet();
      foreach ($keys as $key) {
        $action = $mappings->findAction($key);
        $func = $caller->getActionFunc($action);
        $attrs = $annotation->getAttrs($func);
        $oAttr = \PMVC\get($attrs['obj'], attrKey);
        switch($oAttr->type) {
          case 'daemon':
            $supervisor->daemon(function() use ($func){
              return call_user_func_array($func, [null, null]); 
            }, [], 10);
            break;
          case 'script':
            $supervisor->script($func, [null, null]);
          default:
            break;
        }
      }
      $supervisor->process();
    }
}
