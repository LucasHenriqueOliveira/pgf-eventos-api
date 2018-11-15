<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mailgun\Mailgun;

class ResetPasswordController extends Controller {
    
    public function sendEmail(Request $request) {
        if (!$this->validateEmail($request->email)) {
            return $this->failedResponse();
        }
        $this->send($request->email);
        return $this->successResponse();
    }

    public function send($email) {
        $token = $this->createToken($email);
        $this->email($email, $token);
        //Mail::to($email)->send(new ResetPasswordMail($token));
    }

    public function email($email, $token){

        $url = 'https://pfe-eventos.herokuapp.com/api/users/response-password-reset/'.$token;

        $texto = '<br /> Prezado(a),';
        $texto .= '<br /><br />Clique no link abaixo para uma nova senha';
        $texto .= '<br /><br /> <a href="'. $url .'">Nova Senha</a>';
        $texto .= '<br /><br /> Att, <br />PFE INSS';
        $texto .= '<br /><br /> <h5>Não responda a este email. Os emails enviados a este endereço não serão respondidos.</h5>';


        $mg = Mailgun::create(getenv("MAILGUN_KEY"));
        $mg->messages()->send(getenv("MAILGUN_DOMAIN"), [
            'from' => "PFE INSS <postmaster@cidadaniaativa.com.br>",
            'to'      => $email,
            'subject' => 'Nova Senha',
            'html'    => $texto
        ]);
    }

    public function createToken($email) {
        $oldToken = DB::table('password_resets')->where('email', $email)->first();
        if ($oldToken) {
            return $oldToken->token;
        }
        $token = str_random(60);
        $this->saveToken($token, $email);
        return $token;
    }

    public function saveToken($token, $email) {
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }

    public function validateEmail($email) {
        return !!User::where('email', $email)->first();
    }

    public function failedResponse(){
        return response()->json([
            'error' => 'Email não encontrado.'
        ], Response::HTTP_NOT_FOUND);
    }

    public function successResponse() {
        return response()->json([
            'data' => 'Enviamos um email com a troca de senha. Por favor verifique sua caixa.'
        ], Response::HTTP_OK);
    }
}