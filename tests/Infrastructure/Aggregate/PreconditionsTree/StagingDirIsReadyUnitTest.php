<?php declare(strict_types=1);

namespace PhpTuf\ComposerStager\Tests\Infrastructure\Aggregate\PreconditionsTree;

use PhpTuf\ComposerStager\Domain\Exception\PreconditionException;
use PhpTuf\ComposerStager\Domain\Service\Precondition\StagingDirExistsInterface;
use PhpTuf\ComposerStager\Domain\Service\Precondition\StagingDirIsWritableInterface;
use PhpTuf\ComposerStager\Infrastructure\Aggregate\PreconditionsTree\StagingDirIsReady;
use PhpTuf\ComposerStager\Tests\Infrastructure\Service\Precondition\PreconditionTestCase;

/**
 * @coversDefaultClass \PhpTuf\ComposerStager\Infrastructure\Aggregate\PreconditionsTree\StagingDirIsReady
 *
 * @covers ::__construct
 * @covers ::assertIsFulfilled
 * @covers ::getFulfilledStatusMessage
 * @covers ::getUnfulfilledStatusMessage
 * @covers ::isFulfilled
 *
 * @uses \PhpTuf\ComposerStager\Infrastructure\Aggregate\PreconditionsTree\AbstractPreconditionsTree
 *
 * @property \PhpTuf\ComposerStager\Domain\Service\Precondition\StagingDirExistsInterface|\Prophecy\Prophecy\ObjectProphecy $stagingDirExists
 * @property \PhpTuf\ComposerStager\Domain\Service\Precondition\StagingDirIsWritableInterface|\Prophecy\Prophecy\ObjectProphecy $stagingDirIsWritable
 * @property \PhpTuf\ComposerStager\Domain\Value\Path\PathInterface|\Prophecy\Prophecy\ObjectProphecy $activeDir
 * @property \PhpTuf\ComposerStager\Domain\Value\Path\PathInterface|\Prophecy\Prophecy\ObjectProphecy $stagingDir
 */
final class StagingDirIsReadyUnitTest extends PreconditionTestCase
{
    protected function setUp(): void
    {
        $this->stagingDirExists = $this->prophesize(StagingDirExistsInterface::class);
        $this->stagingDirIsWritable = $this->prophesize(StagingDirIsWritableInterface::class);

        parent::setUp();
    }

    protected function createSut(): StagingDirIsReady
    {
        $stagingDirExists = $this->stagingDirExists->reveal();
        $stagingDirIsWritable = $this->stagingDirIsWritable->reveal();

        return new StagingDirIsReady($stagingDirExists, $stagingDirIsWritable);
    }

    public function testFulfilled(): void
    {
        $activeDir = $this->activeDir->reveal();
        $stagingDir = $this->stagingDir->reveal();
        $exclusions = $this->exclusions;
        $this->stagingDirExists
            ->assertIsFulfilled($activeDir, $stagingDir, $exclusions, $exclusions)
            ->shouldBeCalledTimes(self::EXPECTED_CALLS_MULTIPLE);
        $this->stagingDirIsWritable
            ->assertIsFulfilled($activeDir, $stagingDir, $exclusions, $exclusions)
            ->shouldBeCalledTimes(self::EXPECTED_CALLS_MULTIPLE);

        $this->doTestFulfilled('The staging directory is ready to use.');
    }

    public function testUnfulfilled(): void
    {
        $activeDir = $this->activeDir->reveal();
        $stagingDir = $this->stagingDir->reveal();
        $exclusions = $this->exclusions;
        $this->stagingDirExists
            ->assertIsFulfilled($activeDir, $stagingDir, $exclusions, $exclusions)
            ->shouldBeCalledTimes(self::EXPECTED_CALLS_MULTIPLE)
            ->willThrow(PreconditionException::class);

        $this->doTestUnfulfilled('The staging directory is not ready to use.');
    }
}
