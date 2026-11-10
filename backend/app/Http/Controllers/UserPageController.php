<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;

class UserPageController extends Controller
{
    public function index()
    {
        return inertia('Users/Index', [
            'users' => UserResource::collection(
                User::get()
            )
        ]);
    }
}
