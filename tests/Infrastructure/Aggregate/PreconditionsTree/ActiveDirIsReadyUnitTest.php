<?php declare(strict_types=1);

namespace PhpTuf\ComposerStager\Tests\Infrastructure\Aggregate\PreconditionsTree;

use PhpTuf\ComposerStager\Domain\Exception\PreconditionException;
use PhpTuf\ComposerStager\Domain\Service\Precondition\ActiveDirExistsInterface;
use PhpTuf\ComposerStager\Domain\Service\Precondition\ActiveDirIsWritableInterface;
use PhpTuf\ComposerStager\Infrastructure\Aggregate\PreconditionsTree\ActiveDirIsReady;
use PhpTuf\ComposerStager\Tests\Domain\Translation\PassthroughTranslation;
use PhpTuf\ComposerStager\Tests\Infrastructure\Service\Precondition\PreconditionTestCase;

/**
 * @coversDefaultClass \PhpTuf\ComposerStager\Infrastructure\Aggregate\PreconditionsTree\ActiveDirIsReady
 *
 * @covers ::__construct
 * @covers ::assertIsFulfilled
 * @covers ::getFulfilledStatusMessage
 * @covers ::getUnfulfilledStatusMessage
 * @covers ::isFulfilled
 *
 * @uses \PhpTuf\ComposerStager\Infrastructure\Aggregate\PreconditionsTree\AbstractPreconditionsTree
 *
 * @property \PhpTuf\ComposerStager\Domain\Service\Precondition\ActiveDirExistsInterface|\Prophecy\Prophecy\ObjectProphecy $activeDirExists
 * @property \PhpTuf\ComposerStager\Domain\Service\Precondition\ActiveDirIsWritableInterface|\Prophecy\Prophecy\ObjectProphecy $activeDirIsWritable
 * @property \PhpTuf\ComposerStager\Domain\Value\Path\PathInterface|\Prophecy\Prophecy\ObjectProphecy $activeDir
 * @property \PhpTuf\ComposerStager\Domain\Value\Path\PathInterface|\Prophecy\Prophecy\ObjectProphecy $stagingDir
 */
final class ActiveDirIsReadyUnitTest extends PreconditionTestCase
{
    protected function setUp(): void
    {
        $this->activeDirExists = $this->prophesize(ActiveDirExistsInterface::class);
        $this->activeDirIsWritable = $this->prophesize(ActiveDirIsWritableInterface::class);
        $this->translation = new PassthroughTranslation();

        parent::setUp();
    }

    protected function createSut(): ActiveDirIsReady
    {
        $stagingDirExists = $this->activeDirExists->reveal();
        $stagingDirIsWritable = $this->activeDirIsWritable->reveal();

        return new ActiveDirIsReady($stagingDirExists, $stagingDirIsWritable);
    }

    public function testFulfilled(): void
    {
        $activeDir = $this->activeDir->reveal();
        $stagingDir = $this->stagingDir->reveal();
        $exclusions = $this->exclusions;
        $this->activeDirExists
            ->assertIsFulfilled($activeDir, $stagingDir, $this->translation, $exclusions)
            ->shouldBeCalledTimes(self::EXPECTED_CALLS_MULTIPLE);
        $this->activeDirIsWritable
            ->assertIsFulfilled($activeDir, $stagingDir, $this->translation, $exclusions)
            ->shouldBeCalledTimes(self::EXPECTED_CALLS_MULTIPLE);

        $this->doTestFulfilled('The active directory is ready to use.');
    }

    public function testUnfulfilled(): void
    {
        $activeDir = $this->activeDir->reveal();
        $stagingDir = $this->stagingDir->reveal();
        $exclusions = $this->exclusions;
        $this->activeDirExists
            ->assertIsFulfilled($activeDir, $stagingDir, $this->translation, $exclusions)
            ->shouldBeCalledTimes(self::EXPECTED_CALLS_MULTIPLE)
            ->willThrow(PreconditionException::class);

        $this->doTestUnfulfilled('The active directory is not ready to use.');
    }
}
