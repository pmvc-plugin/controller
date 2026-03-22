<?php

namespace PMVC\PlugIn\supervisor {
    if (!class_exists('PMVC\PlugIn\supervisor\Parallel')) {
        class Parallel
        {
            public function __construct($callable, $options)
            {
                call_user_func($callable);
            }
        }
    }
}

namespace {
    if (!defined('PMVC\\PlugIn\\supervisor\\TYPE_DAEMON')) {
        define('PMVC\\PlugIn\\supervisor\\TYPE_DAEMON', 'daemon');
        define('PMVC\\PlugIn\\supervisor\\TYPE_SCRIPT', 'script');
        define('PMVC\\PlugIn\\supervisor\\TYPE', 'type');
        define('PMVC\\PlugIn\\supervisor\\INTERVAL', 'interval');
        define('PMVC\\PlugIn\\supervisor\\NAME', 'name');
    }
}

namespace PMVC {
    class FakeAmqp extends PlugIn
    {
        public function getModel($name)
        {
            return null;
        }
    }
}
