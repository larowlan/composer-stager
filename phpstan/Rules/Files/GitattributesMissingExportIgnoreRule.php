<?php declare(strict_types=1);

namespace PhpTuf\ComposerStager\PHPStan\Rules\Files;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Reflection\ReflectionProvider;
use PhpTuf\ComposerStager\PHPStan\Rules\AbstractRule;

/** Ensures that a conscious decision is made to include new repository root paths in Git archive files or not. */
final class GitattributesMissingExportIgnoreRule extends AbstractRule
{
    private const SPECIAL_PATHS = [
        '.',
        '..',
        '.DS_Store',
        '.git',
        'vendor',
    ];

    public function __construct(
        private readonly array $gitattributesExportInclude,
        ReflectionProvider $reflectionProvider,
    ) {
        parent::__construct($reflectionProvider);
    }

    public function getNodeType(): string
    {
        return FileNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $filename = explode(DIRECTORY_SEPARATOR, $scope->getFile());
        $filename = array_pop($filename);

        if ($filename !== '.gitattributes') {
            return [];
        }

        $errors = [];

        $rootPaths = scandir(self::PROJECT_ROOT);

        foreach ($rootPaths as $rootPath) {
            if (in_array($rootPath, self::SPECIAL_PATHS, true)) {
                continue;
            }

            if ($this->isIncluded($rootPath) || $this->isExcluded($rootPath)) {
                continue;
            }

            $errors[] = $this->buildErrorMessage(sprintf(
                'Repository root path "/%s" must be either defined as "export-ignore" in .gitattributes '
                . 'or declared in phpstan.neon.dist:parameters.gitattributesExportInclude',
                $rootPath,
            ));
        }

        return $errors;
    }

    /**
     * Determines whether the given filename is included in archive files, i.e.,
     * is not excluded by .gitattributes.
     */
    private function isIncluded(string $filename): bool
    {
        return in_array($filename, $this->gitattributesExportInclude, true);
    }

    /** Determines whether the given filename is excluded from archive files by .gitattributes. */
    private function isExcluded(string $filename): bool
    {
        $gitattributes = file(self::PROJECT_ROOT . '/.gitattributes');
        $gitattributes = array_map(static function ($value) {
            $value = ltrim($value, DIRECTORY_SEPARATOR);
            preg_match('/^(.*)\s*export-ignore$/', $value, $matches);

            return trim($matches[1]);
        }, $gitattributes);
        $gitattributes = array_filter($gitattributes);

        return in_array($filename, $gitattributes, true);
    }
}
