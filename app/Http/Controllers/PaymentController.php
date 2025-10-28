<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;

// class PaymentController extends Controller
// {
//     // Protect all routes with JWT auth
//     public function __construct()
//     {
//         $this->middleware('auth:api');
//     }

//     public function generatePayment(Request $request)
//     {
//         $user = auth()->user(); // Get logged-in user

//         $request->validate([
//             'amount' => 'required|numeric|min:1'
//         ]);

//         $amount = $request->amount;

       
//         return response()->json([
//             'user_id' => $user->id,
//             'amount' => $amount,
//             'aba_response' => [
//                 'status' => 'success',
//                 'transactionID' => 'TEST-' . time()
//             ]
//         ]);
//     }
// }


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    // Protect all payment routes with JWT auth
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Main function: Make a payment request
     */
    public function makePayment(Request $request)
    {
        $user = auth()->user(); // Get logged-in user

        // Validate the request input
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|string|in:qr,cash_card,cash_on_delivery'
        ]);

        $amount = $request->amount;
        $paymentType = $request->payment_type;

        // Simulate response based on payment type
        $response = [];

        if ($paymentType === 'qr') {
            $response = [
                'status' => 'success',
                'transactionID' => 'QR-' . time(),
                'qr_code_url' => 'https://example.com/qr/' . uniqid(),
                'message' => 'QR payment generated successfully'
            ];
        } elseif ($paymentType === 'cash_card') {
            $response = [
                'status' => 'success',
                'transactionID' => 'CARD-' . time(),
                'message' => 'Payment by cash card successful'
            ];
        } elseif ($paymentType === 'cash_on_delivery') {
            $response = [
                'status' => 'pending',
                'transactionID' => 'COD-' . time(),
                'message' => 'Cash on delivery will be processed on arrival'
            ];
        }

        // Return a JSON response
        return response()->json([
            'user_id' => $user->id,
            'amount' => $amount,
            'payment_type' => $paymentType,
            'aba_response' => $response
        ]);
    }

    /**
     * 💠 1. Payment via QR (e.g., ABA Pay)
     */
    private function processQrPayment($user, $amount, $orderId)
    {
        // Example (mock response)
        // Replace with your real ABA Bank or gateway API integration later
        $response = [
            'status' => 'success',
            'transaction_id' => 'QR-' . time(),
            'qr_code_url' => 'https://example.com/qr/' . $orderId
        ];

        return response()->json([
            'method' => 'QR Payment',
            'user_id' => $user->id,
            'amount' => $amount,
            'order_id' => $orderId,
            'payment_gateway_response' => $response
        ]);
    }

    /**
     * 💳 2. Payment via Cash Card
     */
    private function processCashCardPayment($user, $amount, $orderId)
    {
        // Simulated example (integrate with Stripe, PayWay, etc.)
        $response = [
            'status' => 'success',
            'transaction_id' => 'CARD-' . time(),
            'message' => 'Card payment completed successfully'
        ];

        return response()->json([
            'method' => 'Cash Card Payment',
            'user_id' => $user->id,
            'amount' => $amount,
            'order_id' => $orderId,
            'payment_gateway_response' => $response
        ]);
    }

    /**
     * 🚚 3. Payment on Delivery (COD)
     */
    private function processCODPayment($user, $amount, $orderId)
    {
        // COD just marks payment as pending
        $response = [
            'status' => 'pending',
            'transaction_id' => 'COD-' . time(),
            'message' => 'Payment will be made upon delivery'
        ];

        return response()->json([
            'method' => 'Cash on Delivery',
            'user_id' => $user->id,
            'amount' => $amount,
            'order_id' => $orderId,
            'payment_gateway_response' => $response
        ]);
    }
}
