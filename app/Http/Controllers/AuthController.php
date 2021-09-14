<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Suppot\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct () {
        $this->middleware('auth:api', ['except'=>['login', 'create', 'unauthorized']]);
    }
    
    public function unauthorized () {
        return response()->json(['error'=>'Não Autorizado!'], 401);
    }

    public function login(Request $request) {
        $array = ['error' => ''];

        // caputurando dados do request
        $email = $request->input('email');
        $password = $request->input('password');

        // verificando se o email e senha foram informados
        if (!$email && !$password) {
            $array['error'] = 'Dados não enviados!';
            return $array;
        }

        // verificando dados e pegando o token
        $token = auth()->attempt([
            'email' => $email,
            'password' => $password
        ]);

        if (!$token) {
            $array['error'] = "E-mail e/ou Senha inválidos!";
            return $array;
        }

        // se tudo estiver ok adicionamos o token no array de resposta
        $array['token'] = $token;
        return $array;
    }

    public function logout () {
        auth()->logout();
        return ['error'=>''];
    }

    public function refresh () {
        $token = auth()->refresh();
        return [
            'error'=>'',
            'token'=>$token
        ];
    }


    public function create(Request $request) {
        $array = ['error'=>''];

        // pegando valores da request
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $bithdate = $request->input('birthdate');

        if ($name && $email && $password && $bithdate) {
            // Validando a data de nascimento
            if (strtotime($bithdate) == false) {
                $array['error'] = "Data de nascimento inválida!";
                return $array;
            }
            // Verificando se o email já existe
            $emailExists = User::where('email', $email)->count();
            if ($emailExists > 0) {
                $array['error'] = "Email já cadastrado!;";
                return $array;
            }

            // cadastrando o usuário
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            $newUser = new User();
            $newUser->name = $name;
            $newUser->email = $email;
            $newUser->password = $hash;
            $newUser->birthdate = $bithdate;
            
            $newUser->save();

            // logando o usuário
            $token = auth()->attempt([
                'email'=> $email,
                'password'=> $password
            ]);
            if (!$token) {
                $array['error'] = 'Ocorreu um erro!';
                return $array;
            }

            // Adicionando o token no retorno
            $array['token'] = $token;

        }else{
            $array['error'] = "Não enviou todos os campos;";
            return $array;
        }

        return $array;
    }
}
