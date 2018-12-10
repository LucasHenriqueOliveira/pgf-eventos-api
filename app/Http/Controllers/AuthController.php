<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignUpRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\CertificadoRequest;
use App\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup', 'reset', 'certificado']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Email ou senha não existe.'], 401);
        }

        if (!auth()->user()->ativo) {
            return response()->json(['error' => 'Cadastro não validado! Por favor, verifique o seu email.'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function signup(SignUpRequest $request)
    {
        //return response()->json($request);
        // User::create($request->all());
        // return $this->login($request);
        return app(\App\Http\Controllers\UsuarioController::class)->signup($request);
        //return UsuarioController::signup($request->all());
    }

    public function reset(ResetPasswordRequest $request) {
        return app(\App\Http\Controllers\ResetPasswordController::class)->sendEmail($request);
    }

    public function certificado(CertificadoRequest $request) {
        return app(\App\Http\Controllers\UsuarioController::class)->certificado($request);
    }

    public function certificado() {
        return app(\App\Http\Controllers\UsuarioController::class)->emailCertificado();
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}