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

l(__DIR__.'/src/Constants.php');
l(__DIR__.'/src/util_mvc.php');
l(__DIR__.'/src/Action.php');
l(__DIR__.'/src/ActionForm.php');
l(__DIR__.'/src/ActionForward.php');
l(__DIR__.'/src/ActionMapping.php');
l(__DIR__.'/src/ActionMappings.php');
l(__DIR__.'/src/MappingBuilder.php');
l(__DIR__.'/src/Request.php');
l(__DIR__.'/src/RouterInterface.php');
setAppFolders([__DIR__.'/../../pmvc-app']);
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\controller';

/**
 * PMVC Action.
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
class controller extends \PMVC\PlugIn
{
    /**
     * Mapping.
     *
     * @var ActionMappings
     */
    private $_mappings;
    /**
     * Request.
     *
     * @var HttpRequestServlet
     */
    private $_request;

    /**
     * ActionController construct with the options.
     */
    public function __construct()
    {
        $this->_request = new Request();
        $this->_mappings = new ActionMappings();
    }

    /**
     * Set option (Will trigger Event).
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return void
     */
    public function offsetSet($k, $v = null)
    {
        option('set', $k, $v);
        callPlugin(
            'dispatcher',
            'set',
            [
                Event\SET_CONFIG,
                $k,
            ]
        );
    }

    /**
     * Get Option.
     *
     * @param mixed $k key
     *
     * @return mixed
     */
    public function &offsetGet($k)
    {
        return option('get', $k);
    }

    /**
     * Plug App.
     *
     * @param array  $folders   defaultAppFolder
     * @param array  $appAlias  appAlias
     * @param string $indexFile index.php
     *
     * @return mixed
     */
    public function plugApp(array $folders = [], array $appAlias = [], $indexFile = 'index')
    {
        if (exists(_RUN_APP, 'plugin')) {
            return !trigger_error('APP was pluged.', E_USER_WARNING);
        }
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\MAP_REQUEST, true,
            ]
        );
        if (empty($folders)) {
            $folders = [$this[_RUN_APPS]];
        }
        $folders = \PMVC\addAppFolders($folders, $appAlias);
        $alias = $folders['alias'];
        $parents = $folders['folders'];
        $app = $this->getApp();
        $path = $this->_getAppFile(
            $parents,
            $app,
            $indexFile,
            $alias
        );
        if (!$path) {
            $app = $this[_DEFAULT_APP];
            $path = $this->_getAppFile(
                $parents,
                $app,
                $indexFile,
                $alias
            );
        }
        if (!$path) {
            return !trigger_error(
                'No App found for '.print_r(
                    [
                    'Parent' => $parents,
                    'App'    => [$this->getApp(), $app],
                    'Index'  => $indexFile,
                    'Alias'  => $alias,
                    ],
                    true
                ), E_USER_WARNING
            );
        } else {
            $parent = realpath(dirname(dirname($path)));
            $this[_RUN_APPS] = $parent;
            $this->setApp($app);
            $appPlugin = plug(
                _RUN_APP,
                [
                    _PLUGIN_FILE => $path,
                ]
            );
            addPlugInFolders([$parent.'/'.$app.'/plugins']);
            $builder = $appPlugin[_INIT_BUILDER];
            unset($appPlugin[_INIT_BUILDER]);

            return $this->addMapping($builder);
        }
    }

    /**
     * Plug App.
     *
     * @param string $parents   Multiple app folder
     * @param array  $app       app name
     * @param string $indexFile index.php
     * @param string $alias     alias
     *
     * @return mixed
     */
    private function _getAppFile($parents, $app, $indexFile, $alias)
    {
        if (!empty($alias[$app])) {
            $app = $alias[$app];
        }
        $file = $app.'/'.$indexFile.'.php';

        return find($file, $parents);
    }

    /**
     * Reset mapping.
     *
     * @param mixed $mappings mappings
     *
     * @return bool
     */
    public function setMapping($mappings)
    {
        return $this->_mappings->set($mappings);
    }

    /**
     * Add mapping.
     *
     * @param mixed $mappings mappings
     *
     * @return bool
     */
    public function addMapping($mappings)
    {
        return $this->_mappings->add($mappings);
    }

    /**
     * Process the request.
     *
     * @param MappingBuilder $builder Get mappings
     *
     * @return mixed
     */
    public function process(MappingBuilder $builder = null)
    {
        if (callPlugin('dispatcher', 'stop')) {
            // Stop for authentication plugin verify failed
            return;
        }
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\MAP_REQUEST, true,
            ]
        );
        if (!is_null($builder)) {
            $this->addMapping($builder);
        }
        $forward = (object) [
            'action' => $this->getAppAction(),
        ];
        $results = [];
        while (
            isset($forward->action) &&
            $forward = $this->execute($forward->action)
        ) {
            $results[] = $this->processForward($forward);
        }
        $this->_finish();

        return $results;
    }

    public function __destruct()
    {
        $this->_finish();
    }

    /**
     * Execute mapping.
     *
     * @param string $index pass run action
     *
     * @return ActionMapping
     */
    public function execute($index)
    {
        if (!$this->_mappings->mappingExists($index)) {
            return !trigger_error(
                'No mappings found for index: '.$index,
                E_USER_WARNING
            );
        }
        $actionMapping = $this->_processMapping($index);
        $actionForm = $this->_processForm($actionMapping);
        $this[_RUN_FORM] = $actionForm;
        //validate the form if necesarry
        if ($actionMapping->validate) {
            $errorForward = $this->_processValidate($actionForm);
        }
        if (!empty($errorForward)) {
            $actionForward = $errorForward;
        } else {
            $actionForward = $this->_processAction(
                $actionMapping,
                $actionForm
            );
        }

        return $actionForward;
    }

    /**
     * ActionMapping.
     *
     * @param string $index mapping or action index
     *
     * @return ActionMapping
     */
    private function _processMapping($index)
    {
        return $this->_mappings->findMapping($index);
    }

    /**
     * ActionForm.
     *
     * @param ActionMapping $actionMapping actionMapping
     *
     * @return ActionForm
     */
    private function _processForm($actionMapping)
    {
        if (empty($actionMapping->form)) {
            $actionForm = $this[_RUN_FORM];
            if (!empty($actionForm)) {
                return $actionForm;
            }
        }
        $actionForm = $this->_mappings->findForm(
            $actionMapping->form
        );

        //add request parameters
        $this->_initActionFormValue($actionForm, $actionMapping);

        return $actionForm;
    }

    /**
     * Init Action Form Value.
     *
     * @param ActionForm    $actionForm    actionForm
     * @param ActionMapping $actionMapping actionMapping
     *
     * @return ActionForm
     */
    private function _initActionFormValue($actionForm, $actionMapping)
    {
        $scope = &$actionMapping->scope;
        $this[_SCOPE] = $actionMapping;
        if (!is_array($scope)) {
            $scope = $this->_request->keySet();
        }
        foreach ($scope as $name) {
            if (is_array($name)) {
                $actionForm[$name[1]] = $this->_request[$name[0]];
            } else {
                $actionForm[$name] = $this->_request[$name];
            }
        }
    }

    /**
     * Call the validate() by ActionForm.
     *
     * @param ActionForm $actionForm actionForm
     *
     * @return bool if good to go return false else return true to block.
     */
    private function _processValidate($actionForm)
    {
        $isValid = (string) $actionForm->validate();
        $error = $this->getErrorForward();
        if ($error) {
            return $error;
        }

        return !$isValid;
    }

    /**
     * Action for this request.
     *
     * @param ActionMapping $actionMapping actionMapping
     * @param ActionForm    $actionForm    actionForm
     *
     * @return ActionForward
     */
    private function _processAction($actionMapping, $actionForm)
    {
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\B4_PROCESS_ACTION,
                true,
            ]
        );
        $func = $actionMapping->func;
        if (!is_callable($func)) {
            if (exists(_RUN_APP, 'plugin')) {
                $func = [plug(_RUN_APP), $func];
            } else {
                return !trigger_error(
                    'parse action error, function not exists. '.
                    print_r($func, true),
                    E_USER_WARNING
                );
            }
        }

        return call_user_func_array(
            $func,
            [$actionMapping, $actionForm]
        );
    }

    /**
     * ActionForward.
     *
     * @param ActionForward $actionForward actionForward
     *
     * @return mixed
     */
    public function processForward($actionForward)
    {
        if (!is_callable([$actionForward, 'go'])) {
            return $actionForward;
        }
        $this[_FORWARD] = $actionForward;
        if (callPlugin('dispatcher', 'stop')) {
            unset($actionForward->action);

            return;
        }

        return $actionForward->go();
    }

    /**
     * Finish request and take down the controller.
     *
     * @return void
     */
    private function _finish()
    {
        if ($this[Event\FINISH]) {
            return;
        }
        /*Only parse user error, not contain system and app errors*/
        $errorForward = $this->getErrorForward();
        if ($errorForward) {
            $this->processForward($errorForward);
        }
        /*Need located after processForward to avoid json view trigger twice*/
        option('set', Event\FINISH, true);
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\FINISH,
                true,
            ]
        );
    }

    /**
     * Init Error Action Forward.
     *
     * @return ActionForward
     */
    public function getErrorForward()
    {
        $AllErrors = $this[ERRORS];
        if (empty($AllErrors[USER_LAST_ERROR])) {
            return false;
        }
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\B4_PROCESS_ERROR, true,
            ]
        );
        if (!$this->_mappings->forwardExists('error')) {
            return false;
        }
        $errorForward = $this->_mappings->findForward('error');
        $errorForward->set(
            [
                'errors'    => $AllErrors[USER_ERRORS],
                'lastError' => $AllErrors[USER_LAST_ERROR],
            ]
        );

        return $errorForward;
    }

    /**
     * Get Request.
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get Mapping.
     *
     * @return mixed
     */
    public function getMapping()
    {
        return $this->_mappings;
    }

    /**
     * GetApp.
     *
     * @return mixed
     */
    public function getApp()
    {
        return $this[_RUN_APP] ?: $this[_DEFAULT_APP];
    }

    /**
     * SetApp.
     *
     * @param string $app app
     *
     * @return mixed
     */
    public function setApp($app)
    {
        return $this[_RUN_APP] = $app;
    }

    /**
     * Get App Action.
     *
     * @return mixed
     */
    public function getAppAction()
    {
        $action = $this[_RUN_ACTION];
        if (!$this->_mappings->mappingExists($action)) {
            $action = 'index';
        }

        return $action;
    }

    /**
     * Set App Action.
     *
     * @param string $action action
     *
     * @return mixed
     */
    public function setAppAction($action)
    {
        return $this[_RUN_ACTION] = $action;
    }
}
