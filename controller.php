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

use DomainException;

l(__DIR__.'/src/Constants.php');
l(__DIR__.'/src/Action.php');
l(__DIR__.'/src/ActionForm.php');
l(__DIR__.'/src/ActionForward.php');
l(__DIR__.'/src/ActionMapping.php');
l(__DIR__.'/src/ActionMappings.php');
l(__DIR__.'/src/MappingBuilder.php');
l(__DIR__.'/src/Request.php');
l(__DIR__.'/src/RouterInterface.php');
${_INIT_CONFIG
}[_CLASS] = __NAMESPACE__.'\controller';

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
// @codingStandardsIgnoreStart
class controller extends PlugIn // @codingStandardsIgnoreEnd
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
     * Finish flag.
     *
     * @var bool
     */
    private $_isFinish;

    /**
     * ActionController construct with the options.
     *
     * {Controller} -> plugapp -> process -> execute -> _processForm ->
     * _processValidate -> _processAction -> processForward -> _finish
     */
    public function __construct()
    {
        $this->addAppFolders([__DIR__.'/../../pmvc-app']);
        $this->_mappings = new ActionMappings();
        $this->_request = new Request();
    }

    /**
     * Plug App.
     *
     * Controller -> {plugapp} -> process -> execute -> _processForm ->
     * _processValidate -> _processAction -> processForward -> _finish
     *
     * @param array  $folders   defaultAppFolder
     * @param array  $appAlias  appAlias
     * @param string $indexFile index.php
     *
     * @return bool Check is plug success or failed.
     */
    public function plugApp(
        array $folders = [],
        array $appAlias = [],
        $indexFile = 'index'
    ) {
        if (exists(_RUN_APP, 'plugin')) {
            return !trigger_error(
                'APP was pluged.',
                E_USER_WARNING
            );
        }
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\MAP_REQUEST, true,
            ]
        );
        $this->_handleAlias($appAlias);
        if (empty($folders) && $this[_RUN_APPS]) {
            $folders = toArray($this[_RUN_APPS]);
        }
        $folders = $this->addAppFolders($folders);
        $parents = $folders['folders'];
        $path = $this->getAppFile(
            $parents,
            $indexFile
        );
        if (!$path) {
            $path = $this->app_not_found(
                $parents,
                $indexFile,
                $folders
            );
        }
        $parent = realpath(dirname(dirname($path)));
        $this[_RUN_APPS] = $parent;
        $appPlugin = plug(
            _RUN_APP,
            [
                _PLUGIN_FILE => $path,
            ]
        );
        addPlugInFolders([$parent.'/'.$this[_REAL_APP].'/plugins']);
        $names = explode('_', $this[_REAL_APP]);
        set(
            $appPlugin,
            array_replace(
                value(
                    option('get', $names[0], []),
                    array_slice($names, 1),
                    []
                ),
                value(
                    option('get', 'PW', []),
                    $names,
                    []
                )
            )
        );
        if (isset($appPlugin[_INIT_BUILDER])) {
            $builder = $this->addMapping(
                $appPlugin[_INIT_BUILDER]
            );
            unset($appPlugin[_INIT_BUILDER]);

            return $builder;
        } else {
            return true;
        }
    }

    /**
     * Process the request.
     *
     * Controller -> plugapp -> {process} -> execute -> _processForm ->
     * _processValidate -> _processAction -> processForward -> _finish
     *
     * @param MappingBuilder $builder Get mappings
     *
     * @return mixed
     */
    public function process(MappingBuilder $builder = null)
    {
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\MAP_REQUEST, true,
            ]
        );
        if (callPlugin('dispatcher', 'stop')) {
            // Stop for authentication plugin verify failed
            return;
        }
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

    /**
     * Execute mapping.
     *
     * Controller -> plugapp -> process -> {execute} -> _processForm ->
     * _processValidate -> _processAction -> processForward -> _finish
     *
     * @param string $index pass run action
     *
     * @return ActionMapping
     */
    public function execute($index)
    {
        if (!$this->_mappings->actionExists($index)) {
            return !trigger_error(
                'No mappings found for action: ['.$index.']',
                E_USER_WARNING
            );
        }
        $actionMapping = $this->_mappings->findAction($index);
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
     * Process form to handle user input.
     *
     * Controller -> plugapp -> process -> execute -> {_processForm} ->
     * _processValidate -> _processAction -> processForward -> _finish
     *
     * @param ActionMapping $actionMapping actionMapping
     *
     * @return ActionForm
     */
    private function _processForm($actionMapping)
    {
        if (empty($actionMapping->form)) {
            $actionForm = $this[_RUN_FORM];
            if (empty($actionForm)) {
                $defaultForm = option(
                    'get',
                    _DEFAULT_FORM,
                    __NAMESPACE__.'\ActionForm'
                );
                $actionForm = new $defaultForm();
            }
        } else {
            $actionForm = $this->_mappings->findForm(
                $actionMapping->form
            );
            if (empty($actionForm)) {
                throw new DomainException(
                    'ActionForm: ['.
                    $actionMapping->form.
                    '] not exists.'
                );
            }
        }

        //add request parameters
        $this->_initActionFormValue($actionForm, $actionMapping);

        return $actionForm;
    }

    /**
     * Call the validate() with ActionForm.
     *
     * Controller -> plugapp -> process -> execute -> _processForm ->
     * {_processValidate} -> _processAction -> processForward -> _finish
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
     * Process action.
     *
     * Controller -> plugapp -> process -> execute -> _processForm ->
     * _processValidate -> {_processAction} -> processForward -> _finish
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

        return call_user_func_array(
            $this->_getActionFunc($actionMapping),
            [$actionMapping, $actionForm]
        );
    }

    /**
     * Process forward.
     *
     * Controller -> plugapp -> process -> execute -> _processForm ->
     * _processValidate -> _processAction -> {processForward} -> _finish
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
     * Controller -> plugapp -> process -> execute -> _processForm ->
     * _processValidate -> _processAction -> processForward -> {_finish}
     *
     * @return void
     */
    private function _finish()
    {
        if ($this[Event\FINISH] || $this->_isFinish) {
            return;
        }
        $this->_isFinish = true;

        /*Only parse user error, not contain system and app errors*/
        $errorForward = $this->getErrorForward();
        if ($errorForward) {
            $this->processForward($errorForward);
        }

        /* <!-- Need located after processForward to avoid json view trigger twice*/
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\FINISH,
                true,
            ]
        );
        // Need located after callPlugin to avoid unexpedted trigger.
        option('set', Event\FINISH, true);
        /* --> */
    }

    /**
     * Destruct.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->_finish();
    }

    /**
     * <!-- Start Sub function.
     */

    /**
     * Handle Alias.
     *
     * @param array $alias Assign form plugApp
     *
     * @return void
     */
    private function _handleAlias(array $alias)
    {
        $folders = $this->addAppFolders([], $alias);
        $alias = $folders['alias'];
        $app = $this->getApp();
        if (!empty($alias[$app])) {
            $this[_REAL_APP] = $alias[$app];
        } else {
            $this[_REAL_APP] = $app;
            // Get default after dimension back
            $this[_REAL_APP] = $this->getApp();
        }
    }

    /**
     * Get app file.
     *
     * @param string $parents   Multiple app folder
     * @param string $indexFile index.php
     *
     * @return mixed
     */
    public function getAppFile($parents, $indexFile)
    {
        $file = $this[_REAL_APP].'/'.$indexFile.'.php';

        return find($file, $parents);
    }

    /**
     * Add mapping.
     *
     * @param mixed $mappings mappings
     *
     * @return bool
     */
    public function addMapping(MappingBuilder $mappings)
    {
        return $this->_mappings->add($mappings);
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
     * Get action call.
     *
     * @param ActionMapping $actionMapping actionMapping
     *
     * @return callable
     */
    private function _getActionFunc(ActionMapping $actionMapping)
    {
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

        return $func;
    }

    /**
     * <!-- Start public get/set function.
     */

    /**
     * Init Error Action Forward.
     *
     * @return ActionForward
     */
    public function getErrorForward()
    {
        $allErrors = $this[ERRORS];
        if (empty($allErrors[USER_LAST_ERROR])) {
            return false;
        }

        return $this->process_error($allErrors);
    }

    /**
     * Get Mapping.
     *
     * @return mixed
     */
    public function getMappings()
    {
        return $this->_mappings;
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
     * Get apps parent.
     *
     * @return mixed
     */
    public function getAppsParent()
    {
        $folder = $this[_RUN_APPS];
        $i = strrpos($folder, '/vendor/');
        if ($i !== false) {
            $folder = substr($folder, 0, $i);
        }

        return realpath(lastSlash($folder).'../').'/';
    }

    /**
     * Get app.
     *
     * @return string
     */
    public function getApp()
    {
        return $this[_RUN_APP] ?: $this[_DEFAULT_APP];
    }

    /**
     * Set app.
     *
     * @param string $app app
     *
     * @return string
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
        if (!$this->_mappings->actionExists($action)) {
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
    public function &offsetGet($k = null)
    {
        return option('get', $k);
    }

    /**
     * Contains key.
     *
     * @param string $k key
     *
     * @return bool
     */
    public function offsetExists($k)
    {
        return !empty(option('get', $k));
    }

    /**
     * Add App Folder.
     *
     * @param array $folders folders
     * @param array $alias   alias
     *
     * @return mixed
     */
    public function addAppFolders(array $folders, array $alias = [])
    {
        dev(
            /**
             * Dev.
             *
             * @help Debug for PMVC add app folder.
             */
            function () use ($folders, $alias) {
                $trace = plug('debug')->parseTrace(debug_backtrace(), 12);

                return [
                    'previous' => folders(_RUN_APP),
                    'folders'  => $folders,
                    'alias'    => $alias,
                    'trace'    => $trace,
                ];
            },
            'app-folder'
        );

        return folders(_RUN_APP, $folders, $alias);
    }
}
