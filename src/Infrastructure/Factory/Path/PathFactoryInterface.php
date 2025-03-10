<?php declare(strict_types=1);

namespace PhpTuf\ComposerStager\Infrastructure\Factory\Path;

use PhpTuf\ComposerStager\Domain\Value\Path\PathInterface;

/**
 * Creates path value objects.
 *
 * @api
 */
interface PathFactoryInterface
{
    /**
     * Creates a path value object from a string.
     *
     * @param string $path
     *   The path string may be absolute or relative to the current working
     *   directory as returned by `getcwd()` at runtime, e.g., "/var/www/example"
     *   or "example". Nothing needs to actually exist at the path.
     * @param \PhpTuf\ComposerStager\Domain\Value\Path\PathInterface|null $cwd
     *   Optionally override the working directory used as the base for relative
     *   paths. Nothing needs to actually exist at the path. Therefore, it is
     *   simply assumed to represent a directory, as opposed to a file--even if
     *   it has an extension, which is no guarantee of type.
     */
    public static function create(string $path, ?PathInterface $cwd = null): PathInterface;
}
