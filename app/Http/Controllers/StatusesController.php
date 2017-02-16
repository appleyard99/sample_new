<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\status;
use Auth;

class StatusesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['only'=>['store','destory']]);
    }
    /**
     * @detail 当前用户创建微博;
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:140'
        ]);

        Auth::user()->statuses()->create([
            'content' => $request->content
        ]);
        return redirect()->back();
    }

    /**
     * @detail 删除微博;
     * @param $id 要删除的微博id;
     * @return mixed
     */
    public function destroy($id)
    {
        $status = Status::findOrFail($id);
        $this->authorize('destory',$status);
        $status->delete();
        session()->flash('success','微博已被成功删除!');
        return redirect()->back();
    }
}
