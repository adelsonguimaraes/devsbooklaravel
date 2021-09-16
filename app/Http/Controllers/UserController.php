<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Suppot\Facades\Auth;
use App\Models\User;
use App\Models\UserRelation;
use App\Models\Post;
use Image;

class UserController extends Controller
{
    private $loggedUser;

    public function __construct () {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function update (Request $request) {
        $array = ['error' => ''];

        $name = $request->input('name');
        $email = $request->input('email');
        $birthdate = $request->input('birthdate');
        $city = $request->input('city');
        $work = $request->input('work');
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirm');

        $user = User::find($this->loggedUser['id']);

        if ($name) $user->name = $name;
        if ($email) {
            if ($email != $user->email) {
                $emailExist = User::where('email', $email)->count();
                if ($emailExist === 0) {
                    $user->email = $email;
                }else{
                    $array['error'] = 'E-mail já existe!';
                    return $array;
                }
            }
        }

        if ($birthdate) {
            if (strtotime($birthdate) === false) {
                $array['error'] = "Data de nascimento inválida!";
                return $array;
            }
        }

        if ($city) $user->city = $city;
        if ($work) $user->work = $work;

        if ($password && $password_confirm) {
            if ($password === $password_confirm) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $user->password = $hash;
            }else{
                $array['error'] = "As senhas não batem.";
                return $array;
            }
        }

        $user->save();

        return $array;
    }

    public function updateAvatar(Request $request) {
        $array = ['error' => ''];
        // escolhendo os tipos de imagens
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];
        $image = $request->file('avatar');

        if ($image) {
            if (in_array($image->getClientMimeType(), $allowedTypes)) {
                
                $filename = md5(time().rand(0,9999)).'.jpg';

                $destPath = public_path('/media/avatars');

                $img = Image::make($image->path())
                    ->fit(200, 200) // cortando a imagem no centro 200x200 e diminuindo tamanho
                    ->save($destPath.'/'.$filename);

                $user = User::find($this->loggedUser['id']);
                $user->avatar = $filename;
                $user->save();

                $array['url'] = url('/media/avatars'.$filename);
            }else{
                $array['error'] = 'Arquivo não suportado!';
                return $array;
            }
        }else{
            $array['error'] = 'Arquivo não enviado!';
            return $array;
        }


        return $array;
    }

    public function updateCover(Request $request) {
        $array = ['error' => ''];
        // escolhendo os tipos de imagens
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];
        $image = $request->file('cover');

        if ($image) {
            if (in_array($image->getClientMimeType(), $allowedTypes)) {
                
                $filename = md5(time().rand(0,9999)).'.jpg';

                $destPath = public_path('/media/covers');

                $img = Image::make($image->path())
                    ->fit(850, 310) // cortando a imagem no centro 200x200 e diminuindo tamanho
                    ->save($destPath.'/'.$filename);

                $user = User::find($this->loggedUser['id']);
                $user->cover = $filename;
                $user->save();

                $array['url'] = url('/media/covers'.$filename);
            }else{
                $array['error'] = 'Arquivo não suportado!';
                return $array;
            }
        }else{
            $array['error'] = 'Arquivo não enviado!';
            return $array;
        }


        return $array;
    }

    public function read ($id = false) {
        // GET api/user
        // GER api/user/123
        $array = ['error'=>''];

        if ($id) {
            $info = User::find($id);
            if (!$info) {
                $array['error'] = "Usuário inexistente!";
                return $array;
            }
        }else{
            $info = $this->loggedUser;
        }

        $info['avatar'] = url('media/avatars/'.$info['avatar']);
        $info['cover'] = url('media/covers/'.$info['cover']);

        // verificando se o usuário consultado é o usuário logado
        $info['me'] = $info['id'] == $this->loggedUser['id'];

        $dateFrom = new \DateTime($info['birthdate']);
        $dateTo = new \DateTime('today');
        $info['age'] = $dateFrom->diff($dateTo)->y;

        $info['followers'] = UserRelation::where('user_to', $info['id'])->count(); // seguigores
        $info['following'] = UserRelation::where('user_from', $info['id'])->count(); // seguindo

        $info['photoCount'] = Post::where('id_user', $info['id'])
        ->where('type', 'photo')
        ->count();

        $hasRelation = UserRelation::where('user_from', $this->loggedUser['id'])
        ->where('user_to', $info['id'])
        ->count();
        $info['isFollowing'] = ($hasRelation > 0) ? true : false;

        $array['data'] = $info;

        return $array;
    }
}
