<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function getUserProfile($username) {
        $user = Auth::user();
        $query = User::where('username', $username)->first();

        if($query) {
            if($user->username == $username) {
                $data = new ProfileResource($query);
            } else {
                $data = new UserResource($query);
            }

            return Response::json(['success' => true, 'user' => $data], 200);
        } else {
            return Response::json(['success' => false, 'error' => 'Usuário não encontrado.'], 404);
        }
    }
}
