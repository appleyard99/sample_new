<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',[
            'only'=>['edit','update','destroy']
        ]);//调用中间件指定验证的方法
        $this->middleware('guest',['only'=>['create']
        ]);
    }

    /**
     * @return mixed
     * @detail 显示所有用户信息;
     */
    public function index()
    {
        $users = User::paginate(30);
        return view('users.index',compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }
    public function show($id){
        $user = User::findOrFail($id);
        return view('users.show',compact('user'));
    }
    public function store(Request $request)
    {
        $this->validate($request,['name'=>'required|max:50',
                                 'email'=>'required|email|unique:users|max:255',
                                 'password'=>'required|confirmed']);

        $user=User::create(['name'=>$request->name,
                            'email'=>$request->email,
                            'password'=>$request->password]);
        //Auth::login($user);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success','欢迎,您将在这里开启一段新的旅程!');
        return redirect('/');
    }

    /**
     * @param $id 用户id
     * @return mixed
     * @detail 用户页面编辑
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }
   /**
    * @detail 更新用户信息
    */
    public function update($id,Request $request)
    {
        $this->validate($request,['name'=>'required|max:50',
                                    'password'=>'confirmed|min:6']);
        $user = User::findOrFail($id);
        $this->authorize('update',$user);

        $data = [];
        $data = array_filter(['name'=>$request->name,'password'=>$request->password]);
        $user->update($data);

        session()->flash('sucess','个人资料更新成功!');

        return redirect()->route('users.show',$id);
    }
    /**
     * @detail 删除用户;
     */
    public function destroy($id)
    {
        $user =  User::findOrFail($id);
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','成功删除用户!');
        return back();
    }
    /**
     * @detail 发送邮件;
     */
    public function sendEmailConfirmationTo($user){
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'apple_mgg@sina.com';
        $name ='Apple';
        $to = $user->email;
        $subject = '感谢注册,请确认您的邮箱!';

        Mail::send($view,$data,function($message) use($from,$name,$to,$subject){
            $message->from($from,$name)->to($to)->subject($subject);
        });

    }
    /**
     * @邮箱激活;
     */
    public function confirmEmail($token){

        $user = User::where('activation_token',$token)->firstOrFail();
        $user->activated =true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success','恭喜你,激活成功!');
        return redirect()->route('users.show',[$user]);

    }

}
