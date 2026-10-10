<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions

class ExampleTest extends UnitTestCase
{
    #[DoesNotPerformAssertions]
    public function testThatTrueIsTrue(): void
    {
        self::markTestSkipped('This is just a placeholder unit test');
    }
}
