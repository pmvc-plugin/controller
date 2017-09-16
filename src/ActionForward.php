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
 * PMVC ActionForward.
 *
 * !!!!!!!!!!!!!!!
 * !! important !!
 * !!!!!!!!!!!!!!!
 *
 * If you change view after get forward,
 * you need reget forward again.
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
class ActionForward extends HashMap
{
    /**
     * Path.
     *
     * @var string
     */
    private $_path;

    /**
     * Type.
     *
     * @var string
     */
    private $_type;

    /**
     * Header.
     *
     * @var array
     */
    private $_header = [];

    /**
     * View.
     *
     * @var object
     */
    private $_view;

    /**
     * Lazyoutput action.
     *
     * @var string
     */
    public $action;

    /**
     * ActionForward.
     *
     * @param array $forward forward
     */
    public function __construct($forward)
    {
        parent::__construct();
        $this->setPath($forward[_PATH]);
        $this->setHeader($forward[_HEADER]);
        $this->_setType($forward[_TYPE]);
        $this->action = $forward[_ACTION];
    }

    /**
     * Set header.
     *
     * @param array $v value
     *
     * @return mixed
     */
    public function cleanHeader($v)
    {
        return clean($this->_header, $v);
    }

    /**
     * Get header.
     *
     * @return array header
     */
    public function &getHeader()
    {
        return $this->_header;
    }

    /**
     * Set header.
     *
     * @param array $v value
     *
     * @return mixed
     */
    public function setHeader($v)
    {
        if (empty($v)) {
            return;
        }

        return set($this->_header, toArray($v));
    }

    /**
     * Set type.
     *
     * @param string $type type
     *
     * @return void
     */
    private function _setType($type = null)
    {
        if ('view' === $type) {
            $c = plug('controller');
            $appViewEngine = value(
                $c['view'],
                [
                    'engine',
                    $c->getApp(),
                ]
            );
            if ($appViewEngine) {
                $c[_VIEW_ENGINE] = $appViewEngine;
            }
            $this->_view = plug('view');
        }
        $this->_type = $type;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get the path of the ActionForward.
     *
     * @param bool $bMerge merge or not
     *
     * @return string
     */
    public function getPath($bMerge = false)
    {
        $path = $this->_path;
        if ($bMerge) {
            $path = $this->buildCommand(
                $path,
                $this->get()
            );
        }

        return $path;
    }

    /**
     * Build URL from parse_url.
     *
     * @param string $url    default url
     * @param array  $params url overwrite params
     *
     * @return string
     */
    public function buildCommand($url, $params)
    {
        return callPlugin(
            option('get', _ROUTER),
            __FUNCTION__,
            [
                $url,
                $params,
            ]
        );
    }

    /**
     * Set the path of the ActionForward.
     *
     * @param string $path path
     *
     * @return void
     */
    public function setPath($path)
    {
        $this->_path = $path;
    }

    /**
     * Append.
     *
     * @param array $arr merge array
     *
     * @return array
     */
    public function append(array $arr)
    {
        if ('view' === $this->_type) {
            return $this->_view->append($arr);
        } else {
            return $this[[]] = $arr;
        }
    }

    /**
     * Set ActionFored key and value.
     *
     * @param string $k key
     * @param string $v value
     *
     * @return bool
     */
    public function set($k, $v = null)
    {
        if ('view' === $this->_type) {
            return $this->_view->set($k, $v);
        } else {
            return set($this, $k, $v);
        }
    }

    /**
     * Get.
     *
     * @param mixed $k       key
     * @param mixed $default default
     *
     * @return mixed
     */
    public function get($k = null, $default = null)
    {
        if ('view' === $this->_type) {
            return $this->_view->get($k, $default);
        } else {
            return get($this, $k, $default);
        }
    }

    /**
     * Process Header.
     *
     * @return void
     */
    private function _processHeader()
    {
        if (empty($this->_header)) {
            return;
        }
        callPlugin(
            option('get', _ROUTER),
            'processHeader',
            [
                $this->_header,
            ]
        );
        $this->_header = [];
    }

    /**
     * Process View.
     *
     * @return $this
     */
    private function _processView()
    {
        /**
         * Do you face a wrong view object?
         *
         * !! Important !!
         *
         * Please remeber if you need change view,
         * and need change it before create ActionForward instance.
         * Or just create a new one.
         */
        $view = $this->_view;
        $path = $this->getPath();
        if ($path) {
            $view->setThemePath($path);
        }
        if (exists(_RUN_APP, 'plugin')) {
            $run = plug(_RUN_APP);
            $keepForward = $run[_FORWARD];
            if (!is_null($keepForward) && count($keepForward)) {
                $view->prepend(get($keepForward));
                $run[_FORWARD] = new HashMap();
                $keepForward = null;
                unset($keepForward);
            }
        }
        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\B4_PROCESS_VIEW, true,
            ]
        );
        $c = plug('controller');
        $appTemplateDir = value(
            $c['template'],
            [
                'dir',
                $c->getApp(),
            ],
            $c[_TEMPLATE_DIR]
        );
        // Put after B4_PROCESS_VIEW event for get all
        // view_config_helper values.
        $view->setThemeFolder(
            $appTemplateDir
        );
        // Get header after $view->setThemeFolder.
        if (isset($view['headers'])) {
            $this->setHeader($view['headers']);
            unset($view['headers']);
        }
        $this->_processHeader();

        return $view->process();
    }

    /**
     * Execute ActionForward.
     *
     * @return mixed
     */
    public function go()
    {
        switch ($this->getType()) {
        case 'view':
            return $this->_processView();
        case 'redirect':
            $this->_processHeader();
            $path = $this->getPath(true);

            return callPlugin(
                option('get', _ROUTER),
                'go',
                [
                    $path,
                ]
            );
        case 'action':
        default:
            if (exists(_RUN_APP, 'plugin')) {
                $run = plug(_RUN_APP);
                $keepForward = $run[_FORWARD];
                if (is_null($keepForward)) {
                    $keepForward = new HashMap();
                    $run[_FORWARD] = $keepForward;
                }
                $keepForward[[]] = get($this);
            }

            return $this;
        }
    }
}
