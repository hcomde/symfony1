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
   * @return array|string|null
   */
  private function getParsedFixture(string $module, string $filename): array|null|string {
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

  /**
   * @return array[]
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getMergeKeyFixtures(): array {
    return [
      'Simple In Place Substitution' => [
        [
          'foo' => [
            'a' => 'Steve',
            'b' => 'Clark',
            'c' => 'Brian',
          ],
          'bar' => [
            'a' => 'Steve',
            'b' => 'Clark',
            'c' => 'Brian',
            'x' => 'Oren',
          ],
          'foo2' => [
            'a' => 'Ballmer',
          ],
          'ding' => [
            'fi',
            'fei',
            'fo',
            'fam',
          ],
          'check' => [
            'a' => 'Steve',
            'b' => 'Clark',
            'c' => 'Brian',
            'fi',
            'fei',
            'fo',
            'fam',
            'isit' => 'tested',
          ],
          'head' => [
            'a' => 'Ballmer',
            'b' => 'Clark',
            'c' => 'Brian',
            'fi', 'fei',
            'fo', 'fam',
          ],
        ],
        'simpleInPlaceSubstitution.yml',
      ],
    ];
  }

  /**
   * @dataProvider getMergeKeyFixtures
   *
   * @param array  $expectedResult
   * @param string $filename
   */
  public function testMergeKey(array $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('sfMergeKey', $filename));
  }

  /**
   * @return array[]
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getQuotesFixtures(): array {
    return [
      'Some characters at the beginning of a string must be escaped' => [
        [
          'foo' => '| bar',
        ],
        'charactersAtBeginningOfString.yml',
      ],
      'A key can be a quoted string' => [
        [
          'foo1' => 'bar',
          'foo2' => 'bar',
          'foo " bar' => 'bar',
          'foo \' bar' => 'bar',
          'foo3: ' => 'bar',
          'foo4: ' => 'bar',
          'foo5' => [
            'foo " bar: ' => 'bar',
            'foo \' bar: ' => 'bar',
          ],
        ],
        'keyQuotedString.yml',
      ],
    ];
  }

  /**
   * @dataProvider getQuotesFixtures
   *
   * @param array  $expectedResult
   * @param string $filename
   */
  public function testQuotes(array $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('sfQuotes', $filename));
  }

  /**
   * @return array
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getGeneralFixtures(): array {
    return [
      'Multiple quoted string on one line' => [
        [
          'stripped_title' => [
            'name' => 'foo bar',
            'help' => 'bar foo',
          ],
        ],
        'multipleQuotedString.yml',
      ],
      'Empty sequence' => [
        [
          'foo' => [],
        ],
        'emptySequence.yml',
      ],
      'Inline string parsing' => [
        [
          'test' => [
            'complex: string',
            'another [string]',
          ],
        ],
        'inlineStringParsing.yml',
      ],
      'Boolean values' => [
        [
          false,
          false,
          false,
          false,
          true,
          true,
          true,
          true,
          null,
          null,
          'false',
          '-',
          'off',
          'no',
          'true',
          '+',
          'on',
          'yes',
          'null',
          '~',
        ],
        'booleanValues.yml',
      ],
      'Ip addresses' => [
        [
          'foo' => '10.0.0.2',
        ],
        'ipAddresses.yml',
      ],
      'A sequence with an embedded mapping' => [
        [
          'foo',
          [
            'bar' => [
              'bar' => 'foo',
            ],
          ],
        ],
        'embeddedMapping.yml',
      ],
      'A sequence with an unordered array' => [
        [
          1 => 'foo',
          0 => 'bar',
        ],
        'unorderedArray.yml',
      ],
      'Octal value as in spec example 2.19, octal value is converted' => [
        [
          'foo' => 83,
        ],
        'octalValue.yml',
      ],
      'Octal notation in a string must remain a string' => [
        [
          'fam' => '8901\n',
          'foo' => '0123',
          'bar' => '2345',
        ],
        'octalStrings.yml',
      ],
      'Simple array' => [
        [
          'foo',
          'bar',
        ],
        'simpleArray.yml',
      ],
    ];
  }

  /**
   * @dataProvider getGeneralFixtures
   *
   * @param array  $expectedResult
   * @param string $filename
   */
  public function testGeneral(array $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('sfGeneral', $filename));
  }

  /**
   * @return array
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getAnchorAliasFixtures(): array {
    return [
      'Simple Alias Example' => [
        ['Steve', 'Clark', 'Brian', 'Oren', 'Steve'],
        'simpleAliasExample.yml',
      ],
      'Alias of a Mapping' => [
        [['Meat' => 'pork', 'Starch' => 'potato'], 'banana', ['Meat' => 'pork', 'Starch' => 'potato']],
        'aliasOfAMapping.yml',
      ],
    ];
  }

  /**
   * @dataProvider getAnchorAliasFixtures
   *
   * @param array  $expectedResult
   * @param string $filename
   */
  public function testAnchorAlias(array $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('ytsAnchorAlias', $filename));
  }

  /**
   * @return array
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getBasicTestFixtures(): array {
    return [
      'Simple Sequence' => [
        [
          'apple',
          'banana',
          'carrot',
        ],
        'simpleSequence.yml',
      ],
      'Nested Sequence' => [
        [
          [
            'foo',
            'bar',
            'baz',
          ],
        ],
        'nestedSequence.yml',
      ],
      'Mixed Sequences' => [
        [
          'apple',
          [
            'foo',
            'bar',
            'x123',
          ],
          'banana',
          'carrot',
        ],
        'mixedSequences.yml',
      ],
      'Deeply Nested Sequences' => [
        [
          [
            [
              'uno',
              'dos',
            ],
          ],
        ],
        'deeplyNestedSequences.yml',
      ],
      'Simple Mapping' => [
        [
          'foo' => 'whatever',
          'bar' => 'stuff',
        ],
        'simpleMapping.yml',
      ],
      'Sequence in a Mapping' => [
        [
          'foo' => 'whatever',
          'bar' => [
            'uno',
            'dos',
          ],
        ],
        'sequenceInAMapping.yml',
      ],
      'Nested Mappings' => [
        [
          'foo' => 'whatever',
          'bar' => [
            'fruit' => 'apple',
            'name' => 'steve',
            'sport' => 'baseball',
          ],
        ],
        'nestedMapping.yml',
      ],
      'Mixed Mapping' => [
        [
          'foo' => 'whatever',
          'bar' => [
            [
              'fruit' => 'apple',
              'name' => 'steve',
              'sport' => 'baseball',
            ],
            'more',
            [
              'python' => 'rocks',
              'perl' => 'papers',
              'ruby' => 'scissorses',
            ],
          ],
        ],
        'mixedMapping.yml',
      ],
      'Mapping-in-Sequence Shortcut' => [
        [
          [
            'work on YAML.py' => [
              'work on Store',
            ],
          ],
        ],
        'mappingInSequenceShortcut.yml',
      ],
      'Sequence-in-Mapping Shortcut' => [
        [
          'allow' => [
            'localhost',
            '%.sourceforge.net',
            '%.freepan.org',
          ],
        ],
        'sequenceInMappingShortcut.yml',
      ],
    ];
  }

  /**
   * @dataProvider getBasicTestFixtures
   *
   * @param array  $expectedResult
   * @param string $filename
   */
  public function testBasicTests(array $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('ytsBasicTests', $filename));
  }

  /**
   * @return array
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getBlockMappingFixtures(): array {
    return [
      'One Element Mapping' => [
        ['foo' => 'bar'],
        'oneElementMapping.yml',
      ],
      'Multi Element Mapping' => [
        [
          'red' => 'baron',
          'white' => 'walls',
          'blue' => 'berries',
        ],
        'multiElementMapping.yml',
      ],
      'Values aligned' => [
        [
          'red' => 'baron',
          'white' => 'walls',
          'blue' => 'berries',
        ],
        'valuesAligned.yml',
      ],
      'Colons aligned' => [
        [
          'red' => 'baron',
          'white' => 'walls',
          'blue' => 'berries',
        ],
        'colonsAligned.yml',
      ],
    ];
  }

  /**
   * @dataProvider getBlockMappingFixtures
   *
   * @param array  $expectedResult
   * @param string $filename
   */
  public function testBlockMapping(array $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('ytsBlockMapping', $filename));
  }

  /**
   * @return array
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getDocumentSeparatorFixtures(): array {
    return [
      'YAML Header' => [
        ['foo' => 1, 'bar' => 2],
        'yamlHeader.yml',
      ],
      'Red Herring Document Separator' => [
        ['foo' => "---\n"],
        'redHerringDocumentSeparator.yml',
      ],
      'Multiple Document Separators in Block' => [
        [
          'foo' => "---\nfoo: bar\n---\nyo: baz\n",
          'bar' => "fooness\n",
        ],
        'multipleDocumentSeparatorsInBlock.yml',
      ],
    ];
  }

  /**
   * @dataProvider getDocumentSeparatorFixtures
   *
   * @param array  $expectedResult
   * @param string $filename
   */
  public function testDocumentSeparator(array $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('ytsDocumentSeparator', $filename));
  }

  /**
   * @return array[]
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getErrorFixtures(): array {
    return [
      'Not indenting enough' => [
        ['foo' => null, 'firstline' => 1, 'secondline' => 2],
        'notIdentingEnough.yml',
      ],
    ];
  }

  /**
   * @dataProvider getErrorFixtures
   *
   * @param array  $expectedResult
   * @param string $filename
   */
  public function testError(array $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('ytsError', $filename));
  }

  /**
   * @return array[]
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getFlowCollectionFixtures(): array {
    return [
      'Simple Inline Array' => [
        ['seq' => ['a', 'b', 'c']],
        'simpleInlineArray.yml',
      ],
      'Simple Inline Hash' => [
        ['hash' => ['name' => 'Steve', 'foo' => 'bar']],
        'simpleInlineHash.yml',
      ],
      'Multi-line Inline Collections' => [
        [
          'languages' => ['Ruby', 'Perl', 'Python'],
          'websites' => [
            'YAML' => 'yaml.org',
            'Ruby' => 'ruby-lang.org',
            'Python' => 'python.org',
            'Perl' => 'use.perl.org',
          ],
        ],
        'multilineInlineCollections.yml',
      ],
    ];
  }

  /**
   * @dataProvider getFlowCollectionFixtures
   *
   * @param array  $expectedResult
   * @param string $filename
   */
  public function testFlowCollections(array $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('ytsFlowCollections', $filename));
  }

  /**
   * @return array
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getFoldedScalarsFixtures(): array {
    return [
      'Single ending newline' => [
        ['this' => "Foo\nBar\n"],
        'singleEndingNewline.yml',
      ],
      'The \'+\' indicator' => [
        [
          'normal' => "extra new lines not kept\n",
          'preserving' => "extra new lines are kept\n\n\n",
          'dummy' => 'value',
        ],
        'plusIndicator.yml',
      ],
      'Three trailing newlines in literals' => [
        [
          'clipped' => "This has one newline.\n",
          'same as "clipped" above' => "This has one newline.\n",
          'stripped' => 'This has no newline.',
          'same as "stripped" above' => 'This has no newline.',
          'kept' => "This has four newlines.\n\n\n\n",
          'same as "kept" above' => "This has four newlines.\n\n\n\n",
        ],
        'threeTrailingNewlinesInLiterals.yml',
      ],
      'Folded Block in a Sequence' => [
        [
          'apple',
          'banana',
          "can't you see the beauty of yaml? hmm\n",
          'dog',
        ],
        'foldedBlockInASequence.yml',
      ],
      'Folded Block as a Mapping Value' => [
        [
          'quote' => "Mark McGwire's year was crippled by a knee injury.\n",
          'source' => 'espn',
        ],
        'foldedBlockAsAMappingValue.yml',
      ],
      'Three trailing newlines in folded blocks' => [
        [
          'clipped' => "This has one newline.\n",
          'same as "clipped" above' => "This has one newline.\n",
          'stripped' => 'This has no newline.',
          'same as "stripped" above' => 'This has no newline.',
          'kept' => "This has four newlines.\n\n\n\n",
          'same as "kept" above' => "This has four newlines.\n\n\n\n",
        ],
        'threeTailingNewlinesInFoldedBlocks.yml',
      ],
    ];
  }

  /**
   * @dataProvider getFoldedScalarsFixtures
   *
   * @param array  $expectedResult
   * @param string $filename
   */
  public function testFoldedScalars(array $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('ytsFoldedScalars', $filename));
  }

  /**
   * @return array
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getNullsAndEmptiesFixtures(): array {
    return [
      'Empty Sequence' => [
        ['empty' => []],
        'emptySequence.yml',
      ],
      'Empty Mapping' => [
        ['empty' => []],
        'emptyMapping.yml',
      ],
      'Empty Sequence as Entire Document' => [
        [],
        'emptySequenceAsEntireDocument.yml',
      ],
      'Empty Mapping as Entire Document' => [
        [],
        'emptyMappingAsEntireDocument.yml',
      ],
      'Null As Document' => [
        null,
        'nullAsDocument.yml',
      ],
      'Empty string' => [
        null,
        'emptyString.yml',
      ],
    ];
  }

  /**
   * @dataProvider getNullsAndEmptiesFixtures
   *
   * @param array|string|null $expectedResult
   * @param string            $filename
   */
  public function testNullsAndEmpties(null|array|string $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('ytsNullsAndEmpties', $filename));
  }

  /**
   * @return array
   * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
   */
  public function getTypeTransfersFixtures(): array {
    return [
      'Strings' => [
        'String',
        'strings.yml',
      ],
      'String characters' => [
        [
          "What's Yaml?",
          "It's for writing data structures in plain text.",
          "And?",
          "And what? That's not good enough for you?",
          "No, I mean, \"And what about Yaml?\"",
          "Oh, oh yeah. Uh.. Yaml for Ruby.",
        ],
        'stringCharacters.yml',
      ],
      'Indicator in Strings' => [
        [
          'the colon followed by space is an indicator' => 'but is a string:right here',
          'same for the pound sign' => 'here we have it#in a string',
          'the comma can, honestly, be used in most cases' => ['but not in', 'inline collections'],
        ],
        'indicatorInStrings.yml',
      ],
      'Forcing Strings' => [
        [
          'date string' => '2001-08-01',
          'number string' => '192',
        ],
        'forcingStrings.yml',
      ],
      'Single-quoted Strings' => [
        [
          'all my favorite symbols' => '#:!/%.)',
          'a few i hate' => '&(*',
          'why do i hate them?' => 'it\'s very hard to explain',
          'entities' => '&pound; me',
        ],
        'singleQuotedStrings.yml',
      ],
      'Double-quoted Strings' => [
        [
          'i know where i want my line breaks' => "one here\nand another here\n",
        ],
        'doubleQuotedStrings.yml',
      ],
      'Nullable' => [
        [
          'name' => 'Mr. Show',
          'hosted by' => 'Bob and David',
          'date of next season' => null,
        ],
        'nullable.yml',
      ],
      'Boolean' => [
        [
          'Is Gus a Liar?' => true,
          'Do I rely on Gus for Sustenance?' => false,
        ],
        'boolean.yml',
      ],
      'Integers' => [
        [
          'zero' => 0,
          'simple' => 12,
          'one-thousand' => 1000,
          'negative one-thousand' => -1000,
        ],
        'integers.yml',
      ],
      'Integers as Map Keys' => [
        [
          1 => 'one',
          2 => 'two',
          3 => 'three',
        ],
        'integerMapKeys.yml',
      ],
      'Floats' => [
        [
          'a simple float' => 2.0,
          'larger float' => 1000.09,
          'scientific notation' => 1000.09,
        ],
        'floats.yml',
      ],
    ];
  }

  /**
   * @dataProvider getTypeTransfersFixtures
   *
   * @param array|string $expectedResult
   * @param string       $filename
   */
  public function testTypeTransfers(array|string $expectedResult, string $filename): void {
    $this->assertSame($expectedResult, $this->getParsedFixture('ytsTypeTransfers', $filename));
  }
}
