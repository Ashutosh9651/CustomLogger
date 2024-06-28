<?php

namespace CustomLogger\Logger\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use CustomLogger\Logging\ColoredFormatter; // Adjust this if necessary

class ColoredLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('colored_log');

        $handler = new StreamHandler(storage_path('logs/colored.log'), Logger::DEBUG);
        $handler->setFormatter(new ColoredFormatter());

        $logger->pushHandler($handler);

        return $logger;
    }
}
