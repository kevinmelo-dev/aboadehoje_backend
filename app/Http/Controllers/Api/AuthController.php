<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use Hash;
use Illuminate\Support\Facades\Response;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $req = $request->all();
        $req['password'] = Hash::make($req['password']);

        // Verificação de telefone
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);
        $twilio->verify->v2->services($twilio_verify_sid)
            ->verifications
            ->create($req['phone'], "sms");

        $user = User::create($req);
        $token = $user->createToken('API Token')->plainTextToken;
        return Response::json(['success' => true, 'user' => $user, 'token' => $token], 201);
    }

    public function upload_picture(Request $request)
    {
        $user = Auth::user();
        if($request->hasFile('avatar'))
        {
            $file = $request['avatar'];
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/users/', $filename);
            $user['avatar'] = $filename;
        }
        $user->save();
        return Response::json(['success' => true, 'user' => $user], 200);
    }

    public function login(LoginUserRequest $request)
    {
        $req = $request->all();
        if (Auth::attempt($req)) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;
            return Response::json(['success' => true, 'user' => $user, 'token' => $token], 200);
        } else {
            return Response::json(['success' => false, 'error' => 'Credenciais incorretas.'], 401);
        }
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return Response::json(['success' => true], 200);
    }

    public function update(Request $request)
    {
        $req = $request->all();
        $user = Auth::user();
        $user->update($req);
        return Response::json(['success' => true, 'user' => $user], 200);
    }

    protected function verify(Request $request)
    {
        $data = $request->validate([
            'verification_code' => ['required', 'numeric'],
            'phone' => ['required', 'string'],
        ]);
        /* Get credentials from .env */
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);
        $verification = $twilio->verify->v2->services($twilio_verify_sid)
            ->verificationChecks
            ->create($data['verification_code'], array('to' => $data['phone']));
        if ($verification->valid) {
            $user = tap(User::where('phone', $data['phone']))->update(['isVerified' => true]);
            /* Authenticate user */
            return Response::json(['success' => true], 200);
        } else {
            return back()->with(['phone' => $data['phone'], 'error' => 'O código inserido é inválido.']);
        }
    }
}
