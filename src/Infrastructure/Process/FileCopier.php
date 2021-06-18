<?php

namespace PhpTuf\ComposerStager\Infrastructure\Process;

use PhpTuf\ComposerStager\Domain\Output\ProcessOutputCallbackInterface;
use PhpTuf\ComposerStager\Exception\LogicException;
use PhpTuf\ComposerStager\Exception\ProcessFailedException;
use PhpTuf\ComposerStager\Infrastructure\Process\Runner\RsyncRunner;

/**
 * @internal
 */
class FileCopier
{
    /**
     * @var \PhpTuf\ComposerStager\Infrastructure\Process\Runner\RsyncRunner
     */
    private $rsync;

    public function __construct(RsyncRunner $rsync)
    {
        $this->rsync = $rsync;
    }

    /**
     * @param string $from
     * @param string $to
     * @param string[] $exclusions
     *   Paths to exclude, relative to the "from" path.
     * @param \PhpTuf\ComposerStager\Domain\Output\ProcessOutputCallbackInterface|null $callback
     *   An optional PHP callback to run whenever there is process output.
     *
     * @throws \PhpTuf\ComposerStager\Exception\ProcessFailedException
     */
    public function copy(string $from, string $to, array $exclusions = [], ?ProcessOutputCallbackInterface $callback = null): void
    {
        $command = [
            '--recursive',
            // The "--links" option is added to "copy symlinks as symlinks",
            // which is important particularly for files in vendor/bin.
            '--links',
            '--verbose',
        ];

        foreach ($exclusions as $exclusion) {
            $command[] = '--exclude=' . $exclusion;
        }

        // A trailing slash is added to the "from" directory so the CONTENTS of
        // the active directory are copied, not the directory itself.
        $command[] = rtrim($from, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $command[] = $to;

        try {
            $this->rsync->run($command, $callback);
        } catch (LogicException $e) {
            throw new ProcessFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
