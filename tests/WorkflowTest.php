<?php

declare(strict_types=1);

namespace Creatortsv\Workflow\Tests;

use Creatortsv\Workflow\Runner\WorkflowRunner;
use Creatortsv\Workflow\Tests\Proto\TestIncrementStage;
use Creatortsv\Workflow\Workflow;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class WorkflowTest extends TestCase
{
    public function test__construct(): Workflow
    {
        $workflow = new Workflow(
            fn (int $context): int => $context + 1,
            fn (int $context): int => $context * 2,
        );

        $this->assertCount(2, $workflow->getStages());

        return $workflow;
    }

    /**
     * @depends test__construct
     */
    public function testSetStages(Workflow $workflow): Workflow
    {
        $stages = [...$workflow->getStages(), new TestIncrementStage()];

        $workflow->setStages(...$stages);

        $this->assertCount(3, $workflow->getStages());

        return $workflow;
    }

    /**
     * @depends testSetStages
     */
    public function testFresh(Workflow $workflow): Workflow
    {
        $workflow = $workflow->fresh(...array_slice($workflow
            ->getStages()
            ->getArrayCopy(), 1));

        $this->assertCount(2, $workflow->getStages());

        return $workflow;
    }

    /**
     * @depends testFresh
     * @throws ReflectionException
     */
    public function testMakeRunner(Workflow $workflow): void
    {
        $this->assertInstanceOf(WorkflowRunner::class, $workflow->makeRunner());
    }
}