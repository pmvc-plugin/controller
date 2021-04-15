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
 * PMVC ActionMapping.
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
class ActionMapping extends HashMap
{
    /**
     * Name.
     *
     * @var string
     */
    public $name;

    /**
     * Func.
     *
     * @var string
     */
    public $func;

    /**
     * Form.
     *
     * @var string
     */
    public $form;

    /**
     * Validate.
     *
     * @see ActionController::_processForm
     *
     * @var bool
     */
    public $validate = true;

    /**
     * Set request scope , post or get.
     *
     * @see ActionController::initActionFormValue
     *
     * @var string
     */
    public $scope;

    /**
     * Connection mappings.
     *
     * @var ActionMappings
     */
    private $_mappings;

    /**
     * ActionMapping.
     *
     * @param array          $mapping  mapping
     * @param ActionMappings $mappings mappings
     * @param string         $name     name
     */
    public function __construct(&$mapping, $mappings, $name)
    {
        $this->name = $name;
        $this->func = get($mapping, _FUNCTION, $name);
        $this->_mappings = $mappings;
        if (isset($mapping[_FORM])) {
            $this->form = $mapping[_FORM];
        }
        if (isset($mapping[_VALIDATE])) {
            $this->validate = $mapping[_VALIDATE];
        }
        if (isset($mapping[_SCOPE])) {
            $this->scope = $mapping[_SCOPE];
        }
    }

    /**
     * Check ActionForwards exists.
     *
     * @param array $name name
     *
     * @return mixed
     */
    public function offsetExists($name)
    {
        return $this->_mappings->forwardExists($name);
    }

    /**
     * Get ActionForwards from ActionMapping.
     *
     * @param array $name name
     *
     * @return mixed
     */
    public function &offsetGet($name = null)
    {
        $forward = null;
        if (!is_null($name)) {
            $forward = $this->_mappings->findForward($name);
        }

        return $forward;
    }
}
