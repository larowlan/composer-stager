<?php declare(strict_types=1);

namespace PhpTuf\ComposerStager\Infrastructure\Aggregate\PreconditionsTree;

use PhpTuf\ComposerStager\Domain\Aggregate\PreconditionsTree\PreconditionsTreeInterface;
use PhpTuf\ComposerStager\Domain\Exception\PreconditionException;
use PhpTuf\ComposerStager\Domain\Service\Precondition\PreconditionInterface;
use PhpTuf\ComposerStager\Domain\Service\Translation\TranslationInterface;
use PhpTuf\ComposerStager\Domain\Value\Path\PathInterface;
use PhpTuf\ComposerStager\Domain\Value\PathList\PathListInterface;

/** @api */
abstract class AbstractPreconditionsTree implements PreconditionsTreeInterface
{
    /** @var array<\PhpTuf\ComposerStager\Domain\Service\Precondition\PreconditionInterface> */
    private array $children;

    /** Gets a status message for when the precondition is fulfilled. */
    abstract protected function getFulfilledStatusMessage(): string;

    /** Gets a status message for when the precondition is unfulfilled. */
    abstract protected function getUnfulfilledStatusMessage(): string;

    /**
     * The order in which children are evaluated is unspecified and should not be depended upon. There is no
     * guarantee that the order they are supplied in will have, or continue to have, any determinate effect.
     *
     * @see https://github.com/php-tuf/composer-stager/issues/75
     */
    public function __construct(PreconditionInterface ...$children)
    {
        $this->children = $children;
    }

    public function getStatusMessage(
        PathInterface $activeDir,
        PathInterface $stagingDir,
        TranslationInterface $translation,
        ?PathListInterface $exclusions = null,
    ): string {
        return $this->isFulfilled($activeDir, $stagingDir, $translation, $exclusions)
            ? $translation->translate($this->getFulfilledStatusMessage())
            : $translation->translate($this->getUnfulfilledStatusMessage());
    }

    public function isFulfilled(
        PathInterface $activeDir,
        PathInterface $stagingDir,
        TranslationInterface $translation,
        ?PathListInterface $exclusions = null,
    ): bool {
        try {
            $this->assertIsFulfilled($activeDir, $stagingDir, $translation, $exclusions);
        } catch (PreconditionException) {
            return false;
        }

        return true;
    }

    public function assertIsFulfilled(
        PathInterface $activeDir,
        PathInterface $stagingDir,
        TranslationInterface $translation,
        ?PathListInterface $exclusions = null,
    ): void {
        foreach ($this->children as $child) {
            $child->assertIsFulfilled($activeDir, $stagingDir, $translation, $exclusions);
        }
    }

    public function getLeaves(): array
    {
        $leaves = [];

        foreach ($this->children as $child) {
            if ($child instanceof PreconditionsTreeInterface) {
                $leaves[] = $child->getLeaves();

                continue;
            }

            $leaves[] = [$child];
        }

        return array_merge(...$leaves);
    }
}
