<?php

namespace PMVC;

class AnotherPlugin extends \PMVC\PlugIn
{
    /**
     * @see testFinishEventShouldRunOnlyOnce
     */
    public function onFinish()
    {
        $this['view']->process();
    }

    public function onSetConfig__real_app_()
    {
        $this['actual'] = getOption($this['assert']);
    }

    public function init()
    {
        \PMVC\plug('dispatcher')
            ->attachAfter($this, Event\FINISH);
        \PMVC\plug('dispatcher')
            ->attach($this, Event\SET_CONFIG.'_'._REAL_APP);
    }
}

