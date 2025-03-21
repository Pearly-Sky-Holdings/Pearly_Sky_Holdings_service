<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function getAll()
    {
        $orders = DB::table('orders')
            ->get();
        return response()->json($orders, 200);
    }

    public function search(string $input): JsonResponse
    {
        $orders = DB::table('orders')
            ->where('order_id', 'LIKE', '%' . $input . '%')
            ->orWhere('status', 'LIKE', '%' . $input . '%')
            ->orWhere('date', 'LIKE', '%' . $input . '%')
            ->get();
        return response()->json($orders, 200);
    }

    public function save(Request $request)
    {
        $this->orderService->save($request);
        $order = DB::table('orders')
            ->orderBy('created_at', 'desc')
            ->first();
        return response()->json($order, 201);
    }

    public function update(Request $request, $id)
    {
        $result = $this->orderService->update($request, $id);
        return response()->json($result, 200);
    }

    public function delete(int $id)
    {
        $order = Order::find($id);
        $order->delete();
        return response()->json($order, 200);
    }

    public function getByCustomerId(int $customerId)
    {
        $orders = DB::table('orders')
            ->where('customer_id', $customerId)
            ->get();
        return response()->json($orders, 200);
    }

    public function show(Order $order)
    {
        // Return the order details view or JSON response
        return response()->json([
            'status' => 'success',
            'order' => $order,
        ]);
    }
}
