<?php declare(strict_types=1);

namespace PhpTuf\ComposerStager\PHPStan\Rules\Methods;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PhpTuf\ComposerStager\PHPStan\Rules\AbstractRule;

/** Forbids throwing third party exceptions from public methods. */
final class ForbiddenThrowsRule extends AbstractRule
{
    public function getNodeType(): string
    {
        return InClassMethodNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $method = $this->getMethodReflection($scope);

        if (!$method->isPublic()) {
            return [];
        }

        $throwType = $method->getThrowType();

        if ($throwType === null) {
            return[];
        }

        $errors = [];

        foreach ($throwType->getReferencedClasses() as $exception) {
            $class = $this->reflectionProvider->getClass($exception);

            if ($this->isProjectClass($class)) {
                continue;
            }

            $errors[] = $this->buildErrorMessage(sprintf(
                'Built-in or third party exception "\%s" cannot be thrown from public methods. '
                . 'Catch it and throw the appropriate ComposerStager exception instead',
                $exception,
            ));
        }

        return $errors;
    }
}
