<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Suppot\Facades\Auth;
use App\Models\User;

class SearchController extends Controller
{
    private $loggedUser;

    public function __construct () {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function search(Request $request) {
        // GET api/search (txt)
        $array = ['error'=>'', 'users'=> []];

        $txt = $request->input('txt');
        if (!$txt) {
            $array['error'] = "Digite alguma coisa para buscar.";
            return $array;
        }

        // busca de usuário
        $userList = User::select('id', 'name', 'avatar')->where('name', 'like', '%'.$txt.'%')->get();

        $array['users'][] = $userList;

        return $array;
    }
}
