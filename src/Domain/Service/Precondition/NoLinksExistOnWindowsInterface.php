<?php declare(strict_types=1);

namespace PhpTuf\ComposerStager\Domain\Service\Precondition;

/**
 * Asserts that there are no links at all if on Windows.
 *
 * This precondition is fulfilled unless BOTH the host is Windows AND the codebase contains symlinks.
 *
 * This includes both the active and staging directories.
 *
 * It doesn't matter whether the given directories actually exist. In order to isolate failures and avoid redundancy,
 * that question is left to its own preconditions. Except in the event of an IO error (which will throw an exception
 * according to the relevant interface), this one cares about literally nothing else if it doesn't actually find a
 * link on a Windows host.
 *
 * This interface exists solely to facilitate autowiring dependencies through type hinting.
 *
 * @see https://symfony.com/doc/current/service_container/autowiring.html
 *
 * @api
 */
interface NoLinksExistOnWindowsInterface extends PreconditionInterface
{
}
