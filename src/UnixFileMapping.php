<?php

/**
 * Copyright © Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace Youwe\FileMapping;

class UnixFileMapping implements FileMappingInterface
{
    private readonly string $source;
    private readonly string $destination;

    /**
     * @var string[]
     */
    private readonly array $options;

    /**
     * Constructor.
     *
     * @param string $sourceDirectory
     * @param string $destinationDirectory
     * @param string $mapping
     * @param string ...$options
     */
    public function __construct(
        private readonly string $sourceDirectory,
        private readonly string $destinationDirectory,
        string $mapping,
        string ...$options,
    ) {
        // Expand the source and destination.
        static $pattern    = '/({(.*?),(.*?)})/';
        $this->source      = preg_replace($pattern, '$2', $mapping);
        $this->destination = preg_replace($pattern, '$3', $mapping);

        $this->options = $options;
    }

    /**
     * Get the relative path to the source file.
     *
     * @return string
     */
    public function getRelativeSource(): string
    {
        return $this->source;
    }

    /**
     * Get the absolute path to the source file.
     *
     * @return string
     */
    public function getSource(): string
    {
        return $this->sourceDirectory
            . DIRECTORY_SEPARATOR
            . $this->source;
    }

    /**
     * Get the relative path to the destination file.
     *
     * @return string
     */
    public function getRelativeDestination(): string
    {
        return $this->destination;
    }

    /**
     * Get the absolute path to the destination file.
     *
     * @return string
     */
    public function getDestination(): string
    {
        return $this->destinationDirectory
            . DIRECTORY_SEPARATOR
            . $this->destination;
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
