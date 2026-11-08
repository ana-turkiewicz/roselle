<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Models\Address;

class AddressPageController extends Controller
{
     public function index()
    {
        return inertia('Addresses/Index', [
            'addresses' => AddressResource::collection(
                Address::get()
            )
        ]);
    }
}
