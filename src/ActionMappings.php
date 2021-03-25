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
 * PMVC ActionMappings.
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
class ActionMappings
{
    /**
     * Mappings.
     *
     * @var array
     */
    private $_mappings = [];

    /**
     * Set mappings.
     *
     * @param array $mappings mappings
     *
     * @return bool
     */
    public function set(MappingBuilder $mappings)
    {
        $this->_mappings = $mappings;

        return !empty($this->_mappings);
    }

    /**
     * Add mappings.
     *
     * @param array $mappings mappings
     *
     * @return void
     */
    public function add(MappingBuilder $mappings)
    {
        if (empty($this->_mappings)) {
            return $this->set($mappings);
        }
        $this->addByKey(ACTION_MAPPINGS, $mappings);
        $this->addByKey(ACTION_FORMS, $mappings);
        $this->addByKey(ACTION_FORWARDS, $mappings);

        return !empty($this->_mappings);
    }

    /**
     * Add mappings by key.
     *
     * @param string $key      key
     * @param array  $mappings mappings
     *
     * @return array keys
     */
    public function addByKey($key, MappingBuilder $mappings = null)
    {
        if (!is_null($mappings)) {
            $this->_mappings[$key] = array_replace(
                $this->_mappings[$key],
                $mappings[$key]
            );
        }

        return $this->_mappings[$key];
    }

    public function keySet($type = ACTION_MAPPINGS)
    {
        return array_keys($this->_mappings[$type]);
    }

    /**
     * Find an ActionMapping.
     *
     * @param string $name ActionMapping name
     *
     * @return ActionMapping
     */
    public function findAction($name)
    {
        $mapping = value($this->_mappings, [ACTION_MAPPINGS, strtolower($name)]);
        $mappingObj = new ActionMapping($mapping, $this, $name);

        return $mappingObj;
    }

    /**
     * Find a form.
     *
     * @param string $name name
     *
     * @return ActionForm
     */
    public function findForm($name)
    {
        $form = value($this->_mappings, [ACTION_FORMS, $name]);

        if (!class_exists($form[_CLASS])
            && !is_callable($form[_CLASS])
            && exists(_RUN_APP, 'plugin')
        ) {
            $func = plug(_RUN_APP)->isCallable($form[_CLASS]);
            if ($func) {
                $form[_CLASS] = $func;
            }
        }

        if (is_callable($form[_CLASS])) {
            $actionForm = call_user_func($form[_CLASS]);
        } elseif (class_exists($form[_CLASS])) {
            $actionForm = new $form[_CLASS]();
        } else {
            $actionForm = false;
        }

        return $actionForm;
    }

    /**
     * Search for forward.
     *
     * @param string $name name
     *
     * @return ActionForward
     */
    public function findForward($name)
    {
        if (empty($name)) {
            return !trigger_error(
                'ActionForward name is empty',
                E_USER_WARNING
            );
        }
        $forward = value($this->_mappings, [ACTION_FORWARDS, $name]);
        if ($forward) {
            return new ActionForward($forward);
        } else {
            return !trigger_error(
                'ActionForward not found: {'.$name.'} not exists',
                E_USER_WARNING
            );
        }
    }

    /**
     * Check if action exists.
     *
     * @param string $name name
     *
     * @return bool
     */
    public function actionExists($name)
    {
        $name = strtolower($name);

        return isset($this->_mappings[ACTION_MAPPINGS][$name]);
    }

    /**
     * Check if forward exists.
     *
     * @param string $name name
     *
     * @return bool
     */
    public function forwardExists($name)
    {
        return isset($this->_mappings[ACTION_FORWARDS][$name]);
    }

}
