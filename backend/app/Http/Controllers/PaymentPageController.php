<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;

class PaymentPageController extends Controller
{
     public function index()
    {
        return inertia('Payments/Index', [
            'payments' => PaymentResource::collection(
                Payment::get()
            )
        ]);
    }
}
