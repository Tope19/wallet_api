<?php

namespace App\Http\Controllers\API\V1\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class RegisterController extends Controller
{
    protected $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }
    public function register(RegistrationRequest $request)
    {
        $validatedData = $request->validated();
        $user = $this->authService->register($validatedData);

        return response()->json([
            'data' => $user,
            'message' => 'User registered successfully'
        ], Response::HTTP_CREATED);

    }
}
