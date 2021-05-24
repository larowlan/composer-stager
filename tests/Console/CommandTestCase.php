<?php

namespace PhpTuf\ComposerStager\Tests\Console;

use PhpTuf\ComposerStager\Console\Application;
use PhpTuf\ComposerStager\Tests\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @property \PhpTuf\ComposerStager\Console\GlobalOptions|\Prophecy\Prophecy\ObjectProphecy $globalOptions
 */
abstract class CommandTestCase extends TestCase
{
    use GlobalOptionsSetupTrait;

    protected const INERT_COMMAND = '--version';
    protected const STAGING_DIR = '/lorem/ipsum';

    /**
     * The command tester.
     *
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    private $commandTester;

    protected function setUp(): void
    {
        $this->setUpGlobalOptions();
    }

    /**
     * Creates a command object to test.
     *
     * @return \Symfony\Component\Console\Command\Command A command object with
     *   mocked dependencies injected.
     */
    abstract protected function createSut(): Command;

    /**
     * Executes a given command with the command tester.
     *
     * @param array $args The command arguments.
     * @param string[] $inputs An array of strings representing each input passed
     *   to the command input stream.
     */
    protected function executeCommand(
        array $args = [],
        array $inputs = []
    ): void {
        $tester = $this->getCommandTester();
        $tester->setInputs($inputs);
        $commandName = $this->createSut()::getDefaultName();
        $args = array_merge(['command' => $commandName], $args);
        $tester->execute($args);
    }

    /**
     * Gets the command tester.
     *
     * @return \Symfony\Component\Console\Tester\CommandTester A command tester.
     */
    protected function getCommandTester(): CommandTester
    {
        if ($this->commandTester !== null) {
            return $this->commandTester;
        }

        $globalOptions = $this->globalOptions->reveal();
        $application = new Application($globalOptions);

        $createdCommand = $this->createSut();
        $application->add($createdCommand);
        $foundCommand = $application->find($createdCommand->getName());

        $this->commandTester = new CommandTester($foundCommand);
        return $this->commandTester;
    }

    /**
     * Gets the display returned by the last execution of the command.
     *
     * @return string The display.
     */
    protected function getDisplay(): string
    {
        return $this->getCommandTester()->getDisplay();
    }

    /**
     * Gets the status code returned by the last execution of the command.
     *
     * @return int The exit code.
     */
    protected function getStatusCode(): int
    {
        return $this->getCommandTester()->getStatusCode();
    }
}
