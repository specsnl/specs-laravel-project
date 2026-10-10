<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions

class ExampleTest extends FeatureTestCase
{
    #[DoesNotPerformAssertions]
    public function testTheApplicationReturnsASuccessfulResponse(): void
    {
        self::markTestSkipped('This is just a placeholder feature test');
    }
}
