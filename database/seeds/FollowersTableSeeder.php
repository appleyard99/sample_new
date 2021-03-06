<?php

use Illuminate\Database\Seeder;
use App\Models\User;
class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $user = $users->first();
        $user_id = $user->id;
        //获取除了id为1的所有用户集合
        $followers = $users->slice(1);
        //获取集合的id字段;
        $follower_ids = $followers->pluck('id')->toArray();
        //关注除了id为1的所有用户
        $user->follow($follower_ids);
        //其他用户都关注id为1的用户
        foreach ($followers as $follower)
        {
            $follower->follow($user_id);
        }
    }


}
