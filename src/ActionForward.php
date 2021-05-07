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
     * VIEW.
     *
     * @const VIEW
     */
    const VIEW = 'view';

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
     * Body.
     *
     * @var array
     */
    private $_body = [];

    /**
     * Client redirect.
     *
     * @var bool
     */
    private $_isClientRedirect = false;

    /**
     * View.
     *
     * @var object
     */
    private $_view;

    /**
     * Name.
     *
     * @var string
     */
    public $name;

    /**
     * Next action.
     *
     * @var string
     */
    public $action;

    /**
     * TTFB.
     *
     * @var bool
     */
    public $ttfb;

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

        // assign value
        $this->action = $forward[_ACTION];
        if (isset($forward[_TTFB])) {
            $this->ttfb = $forward[_TTFB];
        }
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
        if (self::VIEW === $type || 'redirect' === $type) {
            $c = plug('controller');
            /**
             * Get custom engine from .env
             *
             * syntax: view_engine_[app-name]
             *
             * such as.
             * view_engine_sitemap=xml
             */
            $appViewEngine = value(
                $c,
                [
                    self::VIEW,
                    'engine',
                    $c->getApp(),
                ]
            );
            if ($appViewEngine) {
                $c[_VIEW_ENGINE] = $appViewEngine;
            }
            $this->_view = plug(self::VIEW);
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
        if (self::VIEW === $this->_type) {
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
        if (self::VIEW === $this->_type) {
            return $this->_view->set($k, $v);
        } else {
            return set($this->_body, $k, $v);
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
        if (self::VIEW === $this->_type) {
            return $this->_view->get($k, $default);
        } else {
            return get($this->_body, $k, $default);
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
     * Set client redirect.
     *
     * @param string $type clinet redirect type [href|replace|false]
     *
     * @return string isClientRedirect
     */
    public function setClientRedirect($type)
    {
        switch ($type) {
        case 'href':
        case 'replace':
            $this->_isClientRedirect = $type;
            break;
        default:
            $this->_isClientRedirect = false;
        }

        return $this->_isClientRedirect;
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
         * - Option 1. Must change it before create ActionForward instance.
         * - Option 2. Or just create a new one.
         */
        $view = $this->_view;

        // <-- Handle ttfb
        // Need locate before setThemePath
        // else path will be clean by $view->enable
        if ($this->ttfb) {
            $view[_TTFB] = true;
            $view['reEnable'] = true;
            $view->disable();
        } else {
            if (!empty($view['reEnable'])) {
                unset($view['reEnable']);
                $view->enable();
            }
        }
        // end Handle ttfb-->

        if ('redirect' !== $this->_type) {
            $path = $this->getPath();
            if ($path) {
                $view->setThemePath($path);
            }
        } else {
            $view->prepend(get($this));
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
                Event\WILL_PROCESS_HEADER, true,
            ]
        );
        $c = plug('controller');
        $appTemplateDir = value(
            $c,
            [
                'template',
                'dir',
                $c->getApp(),
            ],
            $c[_TEMPLATE_DIR]
        );
        // Put after WILL_PROCESS_HEADER event for get all
        // view_config_helper values.
        $view->setThemeFolder(
            $appTemplateDir
        );

        // <!-- Get header after $view->setThemeFolder.
        if (isset($view['headers'])) {
            $this->setHeader($view['headers']);
            unset($view['headers']);
        }
        $this->_processHeader();
        // -->

        callPlugin(
            'dispatcher',
            'notify',
            [
                Event\WILL_PROCESS_VIEW, true,
            ]
        );

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
        case 'redirect':
            $this->_processHeader();
            $path = $this->getPath(true);
            if (!empty($this->_isClientRedirect)) {
                $this['clientRedirectTo'] = $path;
                $this['clientRedirectType'] = $this->_isClientRedirect;
            }
            callPlugin(
                option('get', _ROUTER),
                'go',
                [
                    $path,
                    $this->_isClientRedirect,
                ]
            );
        case self::VIEW:
            return $this->_processView();
        case 'action':
        default:
            if (exists(_RUN_APP, 'plugin')) {
                $run = plug(_RUN_APP);
                $keepForward = $run[_FORWARD];
                if (is_null($keepForward)) {
                    $keepForward = new HashMap();
                    $run[_FORWARD] = $keepForward;
                }
                $keepForward[[]] = $this->get();
            }

            return $this;
        }
    }
}
