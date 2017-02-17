<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Auth;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    /**
     * 监听用户创建钱生成账户激活令牌;
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($user){
            $user->activation_token = str_random(30);
        });
    }
    /**
     * generated vatar
     */
    public function gravatar($size=100)
    {
        $hash =md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }
    public function setPasswordAttribute($password)
    {
        $this->attributes['password']=bcrypt($password);
    }

    /**
     * @detail 用户微博
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }
    /**
     * @detail 微博动态(显示自己动态和关注的好友的动态)
     *
     */
    public function feed(){
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        array_push($user_ids,Auth::user()->id);
        return Status::whereIn('user_id',$user_ids)->with('user')->orderBy('created_at','desc');

    }

    /**
     * @detail  获取粉丝关系列表;
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }

    /**
     * @detail 获取用户关注人列表;
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followings()
    {
        return $this->belongsToMany(User::class,'followers','follower_id','user_id');
    }

    /**
     * @detail 微博关注;
     * @param $user_ids
     */
    public function follow($user_ids)
    {
        if(!is_array($user_ids))
        {
            $user_ids=compact('user_ids');
        }
        $this->followings()->sync($user_ids,false);
    }

    /**
     * @detail 取消微博关注;
     * @param $user_ids
     */
    public function unfollow($user_ids){
        if(!is_array($user_ids))
        {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }
    public function isFollowing($user_id){
        return $this->followings->contains($user_id);
    }


}
