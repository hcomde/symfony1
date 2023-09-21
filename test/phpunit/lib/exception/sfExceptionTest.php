<?php

namespace phpunit\lib\exception;

use Exception;
use PHPUnit\Framework\TestCase;
use sfException;
use Throwable;

/**
 * @author h.com networkers GmbH
 */
class sfExceptionTest extends TestCase {

  /**
   * Covers getDecoratedMessage()
   */
  public function testGetDecoratedMessage(): void {
    $exception = new sfException('foobar', 42, null);
    $this->assertSame('foobar', $exception->getDecoratedMessage());
  }

  /**
   * Covers: __toString()
   */
  public function testToString(): void {
    $exception = new sfException('foobar', 42, null);
    $toStringException = (string)$exception;
    $this->assertStringContainsString('sfException [42]: foobar in ', $toStringException);
    $this->assertStringContainsString('/test/phpunit/lib/exception/sfExceptionTest.php:', $toStringException);
  }

  /**
   * Covers: ::createLogMessage()
   * @dataProvider getTestCreateLogMessageCases()
   */
  public function testCreateLogMessage(Throwable $exception, array $expectedMessageParts): void {
    $logMessage = sfException::createLogMessage($exception);
    foreach($expectedMessageParts as $expectedMessagePart) {
      $this->assertStringContainsString($expectedMessagePart, $logMessage);
    }
  }

  public function getTestCreateLogMessageCases(): array {
    return [
      'simple sfException' => [
        new sfException('foobar', 42, null),
        [
          'sfException [42]: foobar in ',
          '/test/phpunit/lib/exception/sfExceptionTest.php:',
        ]
      ],
      'sfException with parent' => [
        new sfException('foo', 42, new sfException('bar', 47)),
        [
          'sfException [42]: foo in ',
          ' || Previous: sfException [47]: bar in ',
          '/test/phpunit/lib/exception/sfExceptionTest.php:',
        ]
      ],
      'exception' => [
        new Exception('foobar', 42, null),
        [
          'Exception [42]: foobar in ',
          '/test/phpunit/lib/exception/sfExceptionTest.php:',
        ]
      ],
      'sfException/Exception mixes with multiple parents with parent' => [
        new sfException('foo', 42, new Exception('bar', 47, new sfException('baz', 0, new Exception('boo')))),
        [
          'sfException [42]: foo in ',
          ' || Previous: Exception [47]: bar in ',
          ' || Previous: sfException [0]: baz in ',
          ' || Previous: Exception [0]: boo in ',
          '/test/phpunit/lib/exception/sfExceptionTest.php:',
        ]
      ],
    ];
  }

}
