<?php

require 'lib/Phpoaipmh/Autoloader.php';
require 'lib/Psr/Autoloader.php';
require 'lib/GuzzleHttp/Autoloader.php';
//require 'lib/HalExplorer/Autoloader.php';
require 'lib/SimpleHal/Autoloader.php';

\Phpoaipmh\Autoloader::register();
\Psr\Autoloader::register();
\GuzzleHttp\Autoloader::register();
//\HalExplorer\Autoloader::register();
\Stormsys\SimpleHal\Autoloader::register();

?>