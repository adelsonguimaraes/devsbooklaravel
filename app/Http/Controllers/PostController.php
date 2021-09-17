<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Suppot\Facades\Auth;
use App\Models\Post;
use App\Models\PostLike;

class PostController extends Controller
{
    private $loggedUser;

    public function __construct () {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function like($id) {
        $array = ['error' => ''];

        // 1 verificar se o post existe
        $post = Post::find($id);
        if (!$post) {
            $array['error'] = "Post não encontrado!";
            return $array;
        }

        // 2 verificar se usuário logado deu like
        if (PostLike::where('id_post', $id)->where('id_user', $this->loggedUser['id'])->count()>0) {
            // 2.1 se sim, remover
            PostLike::where('id_post', $id)
            ->where('id_user', $this->loggedUser['id'])
            ->first()
            ->delete();

            $isLiked = false;
        }else{
            // 2.2 se não, adicionar
            $pl = new PostLike();
            $pl->id_post = $id;
            $pl->id_user = $this->loggedUser['id'];
            $pl->created_at = date('Y-m-d H:i:s');
            $pl->save();

            $isLiked = true;
        }

        $array['isLiked'] = $isLiked;
        $array['likeCount'] =  PostLike::where('id_post', $id)->count();

        return $array;
    }
}
