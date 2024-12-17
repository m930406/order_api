<?php

namespace Tests\Unit;

use App\Events\OrderCreated;
use App\Listeners\StoreOrderListener;
use Tests\TestCase;


class StoreOrderListenerTest extends TestCase
{

    public function test_handle_creates_order_and_order_by_currency_records()
    {
        // Arrange
        $orderData = [
            'order_id' => 1,
            'currency' => 'USD',
            'name' => 'John Doe',
            'address' => '123 Main St',
            'price' => 100.00,
        ];
        $event = new OrderCreated($orderData);
        $listener = new StoreOrderListener();

        // Act
        $listener->handle($event);

        // Assert
        $this->assertDatabaseHas('orders', [
            'order_id' => 1,
            'currency' => 'USD',
        ]);

        $this->assertDatabaseHas('order_by_currency_usd', [
            'order_id' => 1,
            'name' => 'John Doe',
            'address' => '123 Main St',
            'price' => 100.00,
        ]);
    }

    public function test_handle_rolls_back_transaction_on_exception()
    {
        // Arrange
        $orderData = [
            'order_id' => 1,
            'currency' => 'USD',
            'name' => 'John Doe',
            'address' => '123 Main St',
            'price' => 100.00,
        ];
        $event = new OrderCreated($orderData);
        $listener = $this->getMockBuilder(StoreOrderListener::class)
                         ->onlyMethods(['handle'])
                         ->getMock();

        $listener->expects($this->once())
                 ->method('handle')
                 ->will($this->throwException(new \Exception('Test exception')));

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test exception');

        try {
            $listener->handle($event);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('orders', [
                'order_id' => 1,
            ]);

            $this->assertDatabaseMissing('order_by_currency_usd', [
                'order_id' => 1,
            ]);

            throw $e;
        }
    }
}