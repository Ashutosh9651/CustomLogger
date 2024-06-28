<?php

namespace CustomLogger\Logger\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

class ColoredFormatter extends LineFormatter
{
    const COLOR_RESET = "\033[0m";
    const COLOR_RED = "\033[31m";
    const COLOR_YELLOW = "\033[33m";
    const COLOR_WHITE = "\033[37m";

    public function format(array $record): string
    {
        $output = parent::format($record);

        switch ($record['level']) {
            case Logger::CRITICAL:
                $color = self::COLOR_RED;
                break;
            case Logger::ERROR:
            case Logger::WARNING:
                $color = self::COLOR_YELLOW;
                break;
            default:
                $color = self::COLOR_WHITE;
                break;
        }

        return $color . $output . self::COLOR_RESET;
    }
}
