<?php declare(strict_types = 1);

namespace Attitude\CLILogger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

final class Logger implements LoggerInterface {
  use LoggerTrait;

  public function log($level, $message, array $context = array()): void {
    $keys = array_filter(array_keys($context), 'is_string');
    $length = match(count($keys) > 0) {
      true => max(array_map('strlen', $keys)),
      false => 0,
    };

    $contextFormatted = '';

    foreach ($context as $key => $value) {
      if (is_int($key)) {
        $contextFormatted .= json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";
      } else {
        $contextFormatted .= "\033[90m".str_pad("{$key}:", $length + 2)."\033[0m".json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";
      }
    }

    $opening = match($level) {
      LogLevel::EMERGENCY => "\033[41;37m",
      LogLevel::ALERT => "\033[41;37m",
      LogLevel::CRITICAL => "\033[41;37m",
      LogLevel::ERROR => "\033[31m",
      LogLevel::WARNING => "\033[33m",
      LogLevel::NOTICE => "\033[36m",
      LogLevel::INFO => "\033[32m",
      LogLevel::DEBUG => "\033[34m",
      default => "\033[39m",
    };

    $closing = "\033[0m";

    echo sprintf("{$opening}[%s]{$closing} \033[1m%s\033[0m\n%s\n", strtoupper($level), $message, $contextFormatted);
  }
}
