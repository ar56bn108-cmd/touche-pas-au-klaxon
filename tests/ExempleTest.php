<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ExempleTest extends TestCase
{
    public function testDeuxPlusDeuxEgaleQuatre(): void
    {
        self::assertSame(4, 2 + 2);
    }
}