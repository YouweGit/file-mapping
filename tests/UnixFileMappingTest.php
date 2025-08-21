<?php

/**
 * Copyright © Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace Youwe\FileMapping\Tests;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Youwe\FileMapping\UnixFileMapping;

#[CoversClass(UnixFileMapping::class)]
class UnixFileMappingTest extends TestCase
{
    /**
     * @return string[][]
     */
    public static function mappingProvider(): array
    {
        return [
            [
                'deploy.php',
                'deploy.php',
                'deploy.php'
            ],
            [
                'bitbucket-pipelines.yml{.dist,}',
                'bitbucket-pipelines.yml.dist',
                'bitbucket-pipelines.yml'
            ],
            [
                '{default/,}bitbucket-pipelines.yml{.dist,}',
                'default/bitbucket-pipelines.yml.dist',
                'bitbucket-pipelines.yml'
            ]
        ];
    }

    #[DataProvider('mappingProvider')]
    public function testMapping(
        string $mapping,
        string $expectedSource,
        string $expectedDestination
    ): void {
        $mapping = new UnixFileMapping('.', '.', $mapping);

        $this->assertEquals($expectedSource, $mapping->getRelativeSource());
        $this->assertEquals($expectedDestination, $mapping->getRelativeDestination());
    }

    public function testDirectoryResolving(): void
    {
        $fs = vfsStream::setup(
            sha1(__METHOD__),
            null,
            [
                'source' => [
                    'foo' => 'FooContents'
                ],
                'destination' => []
            ]
        );

        $mapping = new UnixFileMapping(
            $fs->getChild('source')->url(),
            $fs->getChild('destination')->url(),
            'foo'
        );

        $this->assertFileExists($mapping->getSource());
        $this->assertFileDoesNotExist($mapping->getDestination());
    }
}
