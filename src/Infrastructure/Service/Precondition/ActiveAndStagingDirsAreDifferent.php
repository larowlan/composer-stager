<?php declare(strict_types=1);

namespace PhpTuf\ComposerStager\Infrastructure\Service\Precondition;

use PhpTuf\ComposerStager\Domain\Service\Precondition\ActiveAndStagingDirsAreDifferentInterface;
use PhpTuf\ComposerStager\Domain\Service\Translation\TranslationInterface;
use PhpTuf\ComposerStager\Domain\Value\Path\PathInterface;
use PhpTuf\ComposerStager\Domain\Value\PathList\PathListInterface;

/**
 * @internal Don't instantiate this class directly. Get it from the service container via its interface.
 *
 * phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong
 */
final class ActiveAndStagingDirsAreDifferent extends AbstractPrecondition implements ActiveAndStagingDirsAreDifferentInterface
{
    public function getName(): string
    {
        return 'Active and staging directories are different'; // @codeCoverageIgnore
    }

    public function getDescription(): string
    {
        return 'The active and staging directories cannot be the same.'; // @codeCoverageIgnore
    }

    public function isFulfilled(
        PathInterface $activeDir,
        PathInterface $stagingDir,
        TranslationInterface $translation,
        ?PathListInterface $exclusions = null,
    ): bool {
        return $activeDir->resolved() !== $stagingDir->resolved();
    }

    protected function getFulfilledStatusMessage(): string
    {
        return 'The active and staging directories are different.';
    }

    protected function getUnfulfilledStatusMessage(): string
    {
        return 'The active and staging directories are the same.';
    }
}
