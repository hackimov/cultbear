<?php

namespace Tests\Unit;

use App\Models\Order;
use PHPUnit\Framework\TestCase;

class OrderTotalCalculationTest extends TestCase
{
    public function test_it_calculates_order_total_from_items(): void
    {
        $total = Order::calculateTotal([
            ['quantity' => 2, 'unit_price' => 1500],
            ['quantity' => 1, 'unit_price' => 900],
        ]);

        $this->assertSame(3900, $total);
    }
}
