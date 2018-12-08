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
 * PMVC MappingBuilder.
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
class MappingBuilder extends HashMap
{
    /**
     * Get Initial State.
     *
     * @return array
     */
    protected function getInitialState()
    {
        return [
            ACTION_FORMS    => [],
            ACTION_MAPPINGS => [],
            ACTION_FORWARDS => [],
        ];
    }

    /**
     *  Add a form to mapping.
     *
     * @param string $psFormId form id
     * @param array  $settings settings
     *
     * @return bool
     */
    public function addForm($psFormId, $settings = [])
    {
        if (!isset($this[ACTION_FORMS][$psFormId])) {
            if (!isset($settings[_CLASS])) {
                $settings[_CLASS] = $psFormId;
            }
            $this[ACTION_FORMS][$psFormId] = $settings;
        }
    }

    /**
     * Add a Action to mapping.
     *
     * @param string $psId     forward id
     * @param mixed  $settings settings
     *
     * @return bool
     */
    public function addAction($psId, $settings = [])
    {
        if (!is_array($settings) || is_callable($settings)) {
            $settings = [
                _FUNCTION => $settings,
            ];
        }
        $settings = new HashMap(
            array_replace(
                $this->getActionDefault(), $settings
            )
        );
        if (!is_null($settings[_FORM])) {
            $this->addForm($settings[_FORM]);
        }
        $lowerId = strtolower($psId);
        if (isset($this[ACTION_MAPPINGS][$lowerId])) {
            trigger_error('Action ['.$lowerId.']: already exists.', E_USER_WARNING);
        }
        $this[ACTION_MAPPINGS][$lowerId] = $settings;

        return $settings;
    }

    /**
     * Get Action Default.
     *
     * @return array
     */
    public function getActionDefault()
    {
        return [
            _FUNCTION => null,
            _FORM     => null,
            _SCOPE    => 'request',
            _VALIDATE => true,
        ];
    }

    /**
     * Add a forward to mapping.
     *
     * @param string $psId     forward id
     * @param array  $settings settings
     *
     * @return bool
     */
    public function addForward($psId, $settings)
    {
        $settings = array_replace(
            $this->getForwardDefault(), $settings
        );
        $this[ACTION_FORWARDS][$psId] = $settings;

        return true;
    }

    /**
     * Get forward default value.
     *
     * @return array
     */
    public function getForwardDefault()
    {
        return [
            _ACTION => null,
            _HEADER => null,
            _PATH   => null,
            _TYPE   => null,
        ];
    }
}
