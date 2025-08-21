# File mapping

A PHP package for mapping files from one location to another. It is used by the [youwe/composer-file-installer](https://github.com/YouweGit/composer-file-installer) package to move installed files according to the location mapping.

## Mapping notation

The mapping can contain `{source,destination}` placeholders, which will be replaced with the source or destination part 
relatively. A mapping string can contain multiple `{source,destination}` placeholders.

Examples mappings:

| Mapping string                   | Translates to source path  | Translates to destination path |
|----------------------------------|----------------------------|--------------------------------|
| `file.php`                       | `file.php`                 | `file.php`                     |
| `{dot,.}gitignore`               | `dotgitignore`             | `.gitignore`                   |
| `{default/,}config.yaml{.dist,}` | `default/config.yaml.dist` | `config.yaml`                  |

## Mapping file

All mappings can be stored in and read from a file in your project/library. In that case, adding options to the mapping 
should be done with a colon-separated string. The actual meaning of the options depends on the implementation actually
using the mappings, but an example of an option could be to indicate that contents should be force overwritten.

Example mapping file:
```text
file1.php
file2.php:option1:option2
{dot,.}gitignore:merge:force
```

## Usage examples

```php
<?php 

use \Youwe\FileMapping\UnixFileMapping;
use \Youwe\FileMapping\UnixFileMappingReader;

/** 
* Create a mapping.
*/
$mapping = new UnixFileMapping(
    sourceDirectory: __DIR__ . '/../folder/files',
    destinationDirectory: getcwd(),
    mapping: '{templates/dot,.}gitignore'
    'option1', 
    'option2',
);
 
 /** 
  * Or read mappings from a file
  */
$reader = new UnixFileMappingReader(
    sourceDirectory: __DIR__ . '/../folder/files',
    targetDirectory: getcwd(),
    'path/to/mapping-file-1',
    'path/to/mapping-file-2',
);
foreach ($reader as $mapping) {
    // Use the mapping 
}

/**
* Get the relative path to the source file.
*/
$mapping->getRelativeSource();

/**
* Get the absolute path to the source file.
*/
$mapping->getSource();

/**
* Get the relative path to the destination file.
*/
$mapping->getRelativeDestination();

/**
* Get the absolute path to the destination file.
*/
$mapping->getDestination();

/**
 * Get the options from this mapping 
 */
$mapping->getOptions();
```

