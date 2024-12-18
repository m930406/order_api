<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Controllers\OrderController;
use App\Http\Requests\OrderRequest;
use App\Services\OrderService;
use Illuminate\Support\Facades\Event;
use App\Events\OrderCreated;
use Mockery;

class OrderControllerTest extends TestCase
{
    protected $orderService;
    protected $orderController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = Mockery::mock(OrderService::class);
        $this->app->instance(OrderService::class, $this->orderService);
        $this->orderController = $this->app->make(OrderController::class);
    }

    public function testStoreSuccess(): void
    {
        $request = Mockery::mock(OrderRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'order_id' => '12345',
            'product' => 'Test Product',
            'quantity' => 1
        ]);
        $request->shouldReceive('all')->andReturn([
            'order_id' => '12345',
            'product' => 'Test Product',
            'quantity' => 1
        ]);
        $request->shouldReceive('setContainer')->andReturnSelf();
        $request->shouldReceive('setRedirector')->andReturnSelf();

        Event::fake();

        $response = $this->orderController->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent(), json_encode(['message' => 'Order received']));

        Event::assertDispatched(OrderCreated::class, function ($event) use ($request) {
            return $event->orderData == $request->all();
        });
    }

    public function testStoreFailure()
    {
        $request = Mockery::mock(OrderRequest::class);
        $request->shouldReceive('validated')->andThrow(new \Exception('Validation failed'));
        $request->shouldReceive('setContainer')->andReturnSelf();
        $request->shouldReceive('setRedirector')->andReturnSelf();

        Event::fake();

        $response = $this->orderController->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent(), json_encode(['message' => 'Order failed']));
    }

    public function testShowSuccess()
    {
        $id = '12345';
        $request = OrderRequest::create('/show/' . $id, 'GET', ['id' => $id]);

        $request->setContainer(app())->setRedirector(app('redirect'));

        $orderDetail = ['order_id' => $id, 'product' => 'Test Product', 'quantity' => 1];
        $this->orderService->shouldReceive('getOrderDetail')->with($id)->andReturn($orderDetail);

        $response = $this->orderController->show($request, $id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent(), json_encode($orderDetail));
    }

    public function testShowFailure()
    {
        $id = 'invalid_id!';
        $request = OrderRequest::create('/show/' . $id, 'GET', ['id' => $id]);

        $request->setContainer(app())->setRedirector(app('redirect'));

        $response = $this->orderController->show($request, $id);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent(), json_encode(['message' => 'The id format is invalid.']));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
