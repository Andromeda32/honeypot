<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse($user, 'Successfully Register', 200, $token);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->sendError('Unauthorized', [], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        // When the user loggs in, the logged in row will be set to 1 from users table
        User::where('id', $user->id)->update(['is_logged_in' => true]);

        return $this->sendResponse(null, 'Successfully Login', 200, $token);
    }


    public function logout()
    {
        Auth::user()
            ->tokens()
            ->delete();
        // When the user loggs out, the logged in row will be set to 0 from users table
        User::where('id', Auth::id())->update(['is_logged_in' => false]);

        return $this->sendResponse(null, 'Successfully Logout', 200, null);
    }

    // Get all users who's logged in row is set to 1 in users table
    public function getAllLoggedUsers()
    {
        $users = User::where('is_logged_in', 1)->get();
        return $this->sendResponse(UserResource::collection($users), 'Successfully Get All Logged Users', 200);
    }
}
