<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;

class PaymentController extends Controller
{
 /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Payment::get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request)
    { 
        $data = $request->validated();

        $response = Payment::create($data);

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
