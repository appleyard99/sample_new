<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\User;
use Auth;

class FollowersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['store','destroy']);
    }

    /**
     * @detail 关注某人;
     * @param $id
     * @return mixed
     */
    public function store($id){
        $user = User::findOrFail($id);
        if(Auth::user()->id===$user->id){
            return redirect('/');
        }
        if(!Auth::user()->isFollowing($id)){//判断是否已关注过
            Auth::user()->follow($id);
        }
        return redirect()->route('users.show',$id);
    }

    /**
     * @detail 取消对某人的关注;
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if(Auth::user()->id === $user->id)
        {
            return redirect('/');
        }
        if(Auth::user()->isFollowing($id))//判断是否关注过;
        {
            Auth::user()->unfollow($id);
        }
        return redirect()->route('users.show',$id);

    }


}
