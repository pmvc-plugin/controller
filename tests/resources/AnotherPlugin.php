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

    public function onMapRequest()
    {
        $this['actual'] = getOption($this['assert']); 
    }

    public function init()
    {
        \PMVC\plug('dispatcher')
            ->attachAfter($this, Event\FINISH);
        \PMVC\plug('dispatcher')
            ->attach($this, Event\MAP_REQUEST);
    }
}

