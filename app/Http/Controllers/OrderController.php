<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Events\OrderCreated;
use App\Services\OrderService;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(OrderRequest $request)
    {
        try {
            // Validate the request parameters
            $request->validated();

            // Get the validated data
            $validated = $request->all();

            // Trigger the OrderCreated event
            event(new OrderCreated($validated));
            return response()->json(['message' => 'Order received'], 200);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return response()->json(['message' => 'Order failed'], 200);
        }
        
    }

    public function show($id)
    {
        try {
            // Validate the order id, only allow alphanumeric and underscore
            if (preg_match('/[^a-zA-Z0-9_]/', $id)) {
                throw new \Exception('Invalid order id');
            }
    
            $orderDetail = $this->orderService->getOrderDetail($id);
            return response()->json($orderDetail, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}