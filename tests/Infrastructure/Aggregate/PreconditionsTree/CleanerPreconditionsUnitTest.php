<?php declare(strict_types=1);

namespace PhpTuf\ComposerStager\Tests\Infrastructure\Aggregate\PreconditionsTree;

use PhpTuf\ComposerStager\Domain\Aggregate\PreconditionsTree\CommonPreconditionsInterface;
use PhpTuf\ComposerStager\Domain\Aggregate\PreconditionsTree\StagingDirIsReadyInterface;
use PhpTuf\ComposerStager\Domain\Exception\PreconditionException;
use PhpTuf\ComposerStager\Infrastructure\Aggregate\PreconditionsTree\CleanerPreconditions;
use PhpTuf\ComposerStager\Tests\Infrastructure\Service\Precondition\PreconditionTestCase;

/**
 * @coversDefaultClass \PhpTuf\ComposerStager\Infrastructure\Aggregate\PreconditionsTree\CleanerPreconditions
 *
 * @covers ::__construct
 * @covers ::assertIsFulfilled
 * @covers ::getFulfilledStatusMessage
 * @covers ::getUnfulfilledStatusMessage
 * @covers ::isFulfilled
 *
 * @uses \PhpTuf\ComposerStager\Infrastructure\Aggregate\PreconditionsTree\AbstractPreconditionsTree
 *
 * @property \PhpTuf\ComposerStager\Domain\Aggregate\PreconditionsTree\CommonPreconditionsInterface|\Prophecy\Prophecy\ObjectProphecy $commonPreconditions
 * @property \PhpTuf\ComposerStager\Domain\Aggregate\PreconditionsTree\StagingDirIsReadyInterface|\Prophecy\Prophecy\ObjectProphecy $stagingDirIsReady
 * @property \PhpTuf\ComposerStager\Domain\Value\Path\PathInterface|\Prophecy\Prophecy\ObjectProphecy $activeDir
 * @property \PhpTuf\ComposerStager\Domain\Value\Path\PathInterface|\Prophecy\Prophecy\ObjectProphecy $stagingDir
 * @property \PhpTuf\ComposerStager\Domain\Value\PathList\PathListInterface $exclusions
 */
final class CleanerPreconditionsUnitTest extends PreconditionTestCase
{
    protected function setUp(): void
    {
        $this->commonPreconditions = $this->prophesize(CommonPreconditionsInterface::class);
        $this->stagingDirIsReady = $this->prophesize(StagingDirIsReadyInterface::class);

        parent::setUp();
    }

    protected function createSut(): CleanerPreconditions
    {
        $commonPreconditions = $this->commonPreconditions->reveal();
        $stagingDirIsReady = $this->stagingDirIsReady->reveal();

        return new CleanerPreconditions($commonPreconditions, $stagingDirIsReady);
    }

    public function testFulfilled(): void
    {
        $activeDir = $this->activeDir->reveal();
        $stagingDir = $this->stagingDir->reveal();
        $exclusions = $this->exclusions;
        $this->commonPreconditions
            ->assertIsFulfilled($activeDir, $stagingDir, $exclusions, $exclusions)
            ->shouldBeCalledTimes(self::EXPECTED_CALLS_MULTIPLE);
        $this->stagingDirIsReady
            ->assertIsFulfilled($activeDir, $stagingDir, $exclusions, $exclusions)
            ->shouldBeCalledTimes(self::EXPECTED_CALLS_MULTIPLE);

        $this->doTestFulfilled('The preconditions for removing the staging directory are fulfilled.');
    }

    public function testUnfulfilled(): void
    {
        $activeDir = $this->activeDir->reveal();
        $stagingDir = $this->stagingDir->reveal();
        $exclusions = $this->exclusions;
        $this->commonPreconditions
            ->assertIsFulfilled($activeDir, $stagingDir, $exclusions, $exclusions)
            ->shouldBeCalledTimes(self::EXPECTED_CALLS_MULTIPLE)
            ->willThrow(PreconditionException::class);

        $this->doTestUnfulfilled('The preconditions for removing the staging directory are unfulfilled.');
    }
}
