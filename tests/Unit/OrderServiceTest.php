<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    public function testGetOrderDetailOrderNotFound()
    {
        // 模擬查詢生成器
        $queryBuilderMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // 配置查詢生成器的 where 和 first 方法
        $queryBuilderMock->method('where')->willReturnSelf();
        $queryBuilderMock->method('first')->willReturn(null);

        // 模擬 Order 模型的 newQuery 方法返回查詢生成器
        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderMock->method('newQuery')->willReturn($queryBuilderMock);

        // 使用模擬的 Order 模型實例化 OrderService
        $orderService = new OrderService($orderMock);

        // 測試 getOrderDetail 方法
        $this->expectException(ModelNotFoundException::class);
        $orderService->getOrderDetail('nonexistent_order_id');
    }
}