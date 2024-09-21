<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'merchant') {
            $orders = Order::with(['customer', 'menu'])
                ->whereHas('menu', function ($query) use ($user) {
                    $query->where('merchant_id', $user->id);
                })
                ->get();
        } elseif ($user->role === 'customer') {
            $orders = Order::with(['customer', 'menu'])
                ->where('customer_id', $user->customer->customer_id)
                ->get();
        } else {
            return $this->sendError('Unauthorized role.', [], 403);
        }

        return $this->sendResponse($orders, 'Orders retrieved successfully.');
    }

    public function show($id)
    {
        $user = Auth::user();

        $order = Order::find($id);

        if (!$order) {
            return $this->sendError('Order not found.', [], 404);
        }

        if ($user->role === 'merchant' && $order->menu->merchant_id !== $user->id) {
            return $this->sendError('Unauthorized to access this order.', [], 403);
        }

        if ($user->role === 'customer' && $order->customer_id !== $user->customer->customer_id) {
            return $this->sendError('Unauthorized to access this order.', [], 403);
        }

        $menuName = $order->menu->name ?? null;

        $invoice = $order->invoice;

        $response = [
            'id' => $order->id,
            'customer_id' => $order->customer_id,
            'menu_id' => $order->menu_id,
            'menu_name' => $menuName,
            'company_name' => $order->company_name,
            'quantity' => $order->quantity,
            'total_price' => $order->total_price,
            'address' => $order->address,
            'contact' => $order->contact,
            'notes' => $order->notes,
            'status' => $order->status,
            'payment_receipt' => $order->payment_receipt,
            'delivery_date' => $order->delivery_date,
            'invoice' => $invoice
        ];

        return $this->sendResponse($response, 'Order retrieved successfully.');
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'delivery_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $customer = Auth::user()->customer;
        $menu = Menu::find($request->input('menu_id'));

        if (!$menu) {
            return $this->sendError('Menu not found.', [], 404);
        }

        $total_price = $menu->price * $request->input('quantity');

        $order = Order::create([
            'customer_id' => $customer->customer_id,
            'menu_id' => $menu->id,
            'quantity' => $request->input('quantity'),
            'total_price' => $total_price,
            'address' => $customer->address,
            'contact' => $customer->contact,
            'notes' => $request->input('notes'),
            'status' => 'pending',
            'delivery_date' => $request->input('delivery_date'),
            'company_name' => $customer->company_name,
        ]);

        $this->createInvoice($order);

        return $this->sendResponse($order, 'Order created successfully.');
    }


    protected function createInvoice(Order $order, $invoiceStatus = 'unpaid')
    {
        Invoice::updateOrCreate(
            ['order_id' => $order->id],
            [
                'total_price' => $order->total_price,
                'invoice_date' => now(),
                'status' => $invoiceStatus,
            ]
        );
    }

    public function destroy($id)
    {
        $order = Order::with('invoice')->find($id);

        if (!$order) {
            return $this->sendError('Order not found.', [], 404);
        }

        if (Auth::user()->role === 'customer' && $order->customer_id !== Auth::user()->customer->customer_id) {
            return $this->sendError('Unauthorized to delete this order.', [], 403);
        }

        if ($order->status != 'pending') {
            return $this->sendError('Cannot delete order because it is already processed.', [], 403);
        }

        if ($order->invoice) {
            $order->invoice->delete();
        }

        $order->delete();

        return $this->sendResponse([], 'Order and related invoice deleted successfully.');
    }

    public function uploadPaymentReceipt(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order || $order->customer_id != Auth::user()->customer->customer_id) {
            return $this->sendError('Order not found or not authorized.', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'payment_receipt' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        if ($request->hasFile('payment_receipt')) {
            if ($order->payment_receipt) {
                Storage::delete($order->payment_receipt);
            }

            $path = $request->file('payment_receipt')->store('public/payment_receipts');
            $order->payment_receipt = $path;
            $order->save();
        }

        return $this->sendResponse($order, 'Payment receipt uploaded successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,in_process,completed,canceled',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $order = Order::with('invoice')->find($id);

        if (!$order) {
            return $this->sendError('Order not found.', [], 404);
        }

        if (Auth::user()->role !== 'merchant' || $order->menu->merchant_id !== Auth::user()->id) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $order->status = $request->input('status');
        $order->save();

        if ($order->invoice) {
            if ($order->status === 'pending') {
                $order->invoice->status = 'unpaid';
            } elseif (in_array($order->status, ['in_process', 'completed'])) {
                $order->invoice->status = 'paid';
            }
            $order->invoice->save();
        }

        return $this->sendResponse($order, 'Order status updated successfully.');
    }
}
