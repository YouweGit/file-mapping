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
                'files' =>
                    "{foo,bar}.php\n{templates/dot,.}gitignore:merge:force\n",
                'source' => [],
                'destination' => []
            ]
        );

        $sourceDirectory = $fileSystem->getChild('source')->url();
        $destinationDirectory = $fileSystem->getChild('destination')->url();
        $mappingsReader = new UnixFileMappingReader(
            $sourceDirectory,
            $destinationDirectory,
            $fileSystem->getChild('files')->url()
        );

        /** @var FileMappingInterface[] $mappings */
        $mappings = iterator_to_array($mappingsReader);

        $this->assertIsList($mappings);
        $this->assertCount(2, $mappings);

        // Verify mapping '{foo,bar}.php'
        $this->assertInstanceOf(FileMappingInterface::class, $mappings[0]);
        $this->assertSame('foo.php', $mappings[0]->getRelativeSource());
        $this->assertSame($sourceDirectory . DIRECTORY_SEPARATOR . 'foo.php', $mappings[0]->getSource());
        $this->assertSame('bar.php', $mappings[0]->getRelativeDestination());
        $this->assertSame($destinationDirectory . DIRECTORY_SEPARATOR . 'bar.php', $mappings[0]->getDestination());
        $this->assertSame([], $mappings[0]->getOptions());

        // Verify mapping '{templates/dot,.}gitignore:merge:force'
        $this->assertInstanceOf(FileMappingInterface::class, $mappings[1]);
        $this->assertSame('templates/dotgitignore', $mappings[1]->getRelativeSource());
        $this->assertSame($sourceDirectory . DIRECTORY_SEPARATOR . 'templates/dotgitignore', $mappings[1]->getSource());
        $this->assertSame('.gitignore', $mappings[1]->getRelativeDestination());
        $this->assertSame($destinationDirectory . DIRECTORY_SEPARATOR . '.gitignore', $mappings[1]->getDestination());
        $this->assertSame(['merge', 'force'], $mappings[1]->getOptions());
    }
}
