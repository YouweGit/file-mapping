<?php

/**
 * Copyright © Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace Youwe\FileMapping\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use Youwe\FileMapping\FileMappingInterface;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Youwe\FileMapping\UnixFileMappingReader;

#[CoversClass(UnixFileMappingReader::class)]
class UnixFileMappingReaderTest extends TestCase
{
    public function testIteration(): void
    {
        $fileSystem = vfsStream::setup(
            sha1(__METHOD__),
            null,
            [
                'files' => '{foo,bar}.php',
                'source' => [
                    'foo.php' => 'Foo'
                ],
                'destination' => []
            ]
        );

        $mappings = new UnixFileMappingReader(
            $fileSystem->getChild('source')->url(),
            $fileSystem->getChild('destination')->url(),
            $fileSystem->getChild('files')->url(),
            $fileSystem->getChild('files')->url()
        );

        foreach ($mappings as $offset => $mapping) {
            $this->assertInstanceOf(FileMappingInterface::class, $mapping);
            $this->assertIsInt($offset);
            $this->assertFileExists($mapping->getSource());
        }
    }
}
