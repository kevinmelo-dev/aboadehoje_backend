<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use Hash;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    use ApiResponser;

    public function register(RegisterUserRequest $request)
    {
        $req = $request->all();

        // Manipula senha
        $req['password'] = Hash::make($req['password']);

        // Manipula avatar
        if($request->hasFile('avatar'))
        {
            $file = $req['avatar'];
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/users/', $filename);
            $req['avatar'] = $filename;
        }

        // Manipula número de telefone
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);
        $twilio->verify->v2->services($twilio_verify_sid)
            ->verifications
            ->create($req['phone'], "sms");

        $user = User::create($req);

        return $this->success(
            [
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken
            ],
            "Usuário registrado com sucesso.",
            201
        );

    }

    public function login(LoginUserRequest $request)
    {
        $req = $request->all();

        if (Auth::attempt($req)) {
            $user = Auth::user();
            return $this->success(
                [
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken
                ],
                "Usuário logado com sucesso.",
                200
            );
        }

        else {
            return $this->error('Credenciais incorretas.', 401);
        }

    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return $this->success(
            [
            ],
            "Usuário desconectado com sucesso.",
            200
        );
    }

    public function update(Request $request)
    {
        $req = $request->all();
        $user = Auth::user();
        if($request->hasFile('avatar'))
        {
            $file = $req['avatar'];
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/users/', $filename);
            $req['avatar'] = $filename;
        }

        $user->update($req);


        return $this->success(
            [
                'user' => $user,
            ],
            "Usuário atualizado com sucesso.",
            200
        );
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
            return $this->success(
                "Número de telefone verificado com sucesso.",
                200
            );
        }
        return back()->with(['phone' => $data['phone'], 'error' => 'O código inserido é inválido.']);
    }
}
