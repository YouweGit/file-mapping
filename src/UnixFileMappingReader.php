<?php

/**
 * Copyright © Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace Youwe\FileMapping;

use ArrayIterator;
use SplFileObject;

class UnixFileMappingReader implements FileMappingReaderInterface
{
    /**
     * @var string[]
     */
    private readonly array $mappingFilePaths;

    /**
     * @var ArrayIterator<FileMappingInterface>
     */
    private ArrayIterator $mappings;

    /**
     * Constructor.
     *
     * @param string   $sourceDirectory
     * @param string   $targetDirectory
     * @param string ...$mappingFilePaths
     */
    public function __construct(
        private readonly string $sourceDirectory,
        private readonly string $targetDirectory,
        string ...$mappingFilePaths
    ) {
        $this->mappingFilePaths = $mappingFilePaths;
    }

    /**
     * Get the mappings.
     *
     * @return ArrayIterator<FileMappingInterface>
     */
    private function getMappings(): ArrayIterator
    {
        if (!isset($this->mappings)) {
            $filePaths = [];

            foreach ($this->mappingFilePaths as $mappingFilePath) {
                $newFilePaths = iterator_to_array(new SplFileObject($mappingFilePath, 'r'));
                $filePaths    = array_merge($filePaths, $newFilePaths);
            }

            $this->mappings = new ArrayIterator(
                array_map(
                    function (string $mapping): FileMappingInterface {
                        return new UnixFileMapping(
                            $this->sourceDirectory,
                            $this->targetDirectory,
                            trim($mapping)
                        );
                    },
                    // Filter out empty lines.
                    array_filter(
                        $filePaths
                    )
                )
            );
        }

        return $this->mappings;
    }

    /**
     * Move forward to next element.
     *
     * @return void
     */
    public function next(): void
    {
        $this->getMappings()->next();
    }

    /**
     * Return the key of the current element.
     *
     * @return int
     */
    public function key(): int
    {
        return $this->getMappings()->key();
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->getMappings()->valid();
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->getMappings()->rewind();
    }

    /**
     * Get the current file mapping.
     *
     * @return FileMappingInterface
     */
    public function current(): FileMappingInterface
    {
        return $this->getMappings()->current();
    }
}
