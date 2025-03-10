<?php declare(strict_types=1);

namespace PhpTuf\ComposerStager\Domain\Service\Precondition;

/**
 * Asserts that there are no hard links in the codebase.
 *
 * Note: the target of a hard link is effectively a link just as much as the source, because it, too, represents one of
 * multiple links to the same inode. Therefore, it too must be excluded from domain operations if it is not to fail
 * this preconditions.
 *
 * This includes both the active and staging directories.
 *
 * It doesn't matter whether the given directories actually exist. In order to isolate failures and avoid redundancy,
 * that question is left to its own preconditions. Except in the event of an IO error (which will throw an exception
 * according to the relevant interface), this one cares about literally nothing else if it doesn't actually find a
 * hard link.
 *
 * This interface exists solely to facilitate autowiring dependencies through type hinting.
 *
 * @see https://symfony.com/doc/current/service_container/autowiring.html
 *
 * @api
 */
interface NoHardLinksExistInterface extends PreconditionInterface
{
}
