<?php

declare(strict_types=1);

namespace ApiCoreTest\Dependency\Hook;

use ApiCore\Dependency\Container;
use ApiCoreTest\Dependency\Hook\Example\AfterConstructExample;
use ApiCoreTest\Dependency\Hook\Example\BeforeConstructExample;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HookTest extends TestCase
{
    #[Test]
    public function beforeConstruct()
    {
        $exampleOnConstruct = new BeforeConstructExample();
        $this->assertEmpty($exampleOnConstruct->getValue());
        /** @var BeforeConstructExample $exampleBeforeConstruct */
        $exampleBeforeConstruct = Container::getInstance()->get(BeforeConstructExample::class);
        $this->assertEquals('Generated by beforeConstruct hook', $exampleBeforeConstruct->getValue());
    }

    #[Test]
    public function afterConstruct(): void
    {
        $exampleOnConstruct = new AfterConstructExample('Hello World');
        $this->assertEquals('Hello World', $exampleOnConstruct->getValue());
        /** @var AfterConstructExample $exampleAfterConstruct */
        $exampleAfterConstruct = Container::getInstance()->get(AfterConstructExample::class);
        $this->assertEquals('Changed after Construct hook', $exampleAfterConstruct->getValue());
    }
}
