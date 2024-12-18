<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\OrderService;
use App\Models\Order;
use App\Models\OrderByCurrency;
use Mockery;

class OrderServiceTest extends TestCase
{
    protected $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }

    public function testGetOrderDetail()
    {
        $orderId = 1;
        $currency = 'USD';
        $address = json_encode(['street' => '123 Main St', 'city' => 'Anytown']);

        $orderMock = Mockery::mock(Order::class);
        $orderMock->shouldReceive('where')->with('order_id', $orderId)->andReturnSelf();
        $orderMock->shouldReceive('first')->andReturn((object) ['order_id' => $orderId, 'currency' => $currency]);
        $orderMock->shouldReceive('getAttribute')->andReturnUsing(function ($key) use ($orderId, $currency) {
            $attributes = [
                'order_id' => $orderId,
                'currency' => $currency,
            ];
            return $attributes[$key];
        });

        $orderDetailMock = Mockery::mock(OrderByCurrency::class);
        $orderDetailMock->shouldReceive('setTableCurrency')->with($currency);
        $orderDetailMock->shouldReceive('where')->with('order_id', $orderId)->andReturnSelf();
        $orderDetailMock->shouldReceive('first')->andReturn((object) ['order_id' => $orderId, 'address' => $address]);
        $orderDetailMock->shouldReceive('getAttribute')->andReturnUsing(function ($key) use ($orderId, $address) {
            $attributes = [
                'order_id' => $orderId,
                'address' => $address,
            ];
            return $attributes[$key];
        });

        // Mocking setAttribute method to avoid BadMethodCallException
        $orderDetailMock->shouldReceive('setAttribute')->andReturnUsing(function ($key, $value) {
            $this->$key = $value;
        });

        $this->orderService = Mockery::mock(OrderService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->orderService->shouldReceive('getOrder')->andReturn($orderMock);
        $this->orderService->shouldReceive('getOrderDetailByCurrency')->andReturn($orderDetailMock);

        $result = $this->orderService->getOrderDetail($orderId);

        $this->assertEquals($orderId, $result->order_id);
        $this->assertEquals(json_decode($address, true), json_decode($result->address, true));
    }

    public function testGetOrderNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Order not found');

        $orderService = Mockery::mock(OrderService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $orderService->shouldReceive('getOrder')->andThrow(new \Exception('Order not found'));

        $orderService->getOrderDetail(1);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
