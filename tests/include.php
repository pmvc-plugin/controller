<?php

namespace PMVC;

include __DIR__.'/../vendor/autoload.php';
\PMVC\Load::plug();
\PMVC\plug('controller');
l(__DIR__.'/resources/FakeView.php');
