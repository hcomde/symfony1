<?php

namespace phpunit\lib\yaml;

use PHPUnit\Framework\TestCase;
use sfYamlParser;

/**
 *
 */
class sfYamlParserTest extends TestCase {

  /**
   * @param string $module
   * @param string $filename
   * @return array
   */
  private function getParsedFixture(string $module, string $filename): array {
    $sfYamlParser = new sfYamlParser();
    return $sfYamlParser->parse(file_get_contents(__DIR__ . "/fixtures/$module/$filename"));
  }

  /**
   * @return array[]
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getCommentFixtures(): array {
    return [
      'Comments at the end of a line' => [
        ['ex1' => 'foo # bar', 'ex2' => 'foo # bar', 'ex3' => 'foo # bar', 'ex4' => 'foo'],
        'commentsAtTheEndOfLine.yml',
      ],
      'Comments in the middle' => [
        ['foo' => ['bar' => 'foo']],
        'commentsInTheMiddle.yml',
      ],
      'Comments on a hash line' => [
        ['foo' => ['foo' => 'bar']],
        'commentsOnAHashLine.yml',
      ],
      'Value starting with a #' => [
        ['foo' => '#bar'],
        'valueStartingWithAHashSign.yml',
      ],
      'Document starting with a comment and a separator' => [
        ['foo' => 'bar'],
        'documentStartingWithACommentAndASeparator.yml',
      ],
    ];
  }

  /**
   * @dataProvider getCommentFixtures
   */
  public function testComments(array $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('sfComments', $filename));
  }

}
