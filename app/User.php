<?php

namespace App;

use Hash;
use Request;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function signup(){
        //dd(Request::all());//当前用户发送过来的数据打印出来
        $username = Request::get('username');
        $password = Request::get('password');
        //检查用户名和密码是否为空
        if (!$username || !$password)
            return ['status' => 0, 'msg' => '用户名和密码皆不可为空'];
        //检查用户名在数据库是否存在
        $user_exists = $this
            ->where('username', $username)
            ->exists();
        if ($user_exists)
            return ['status' => 0, 'msg' => '用户名已存在'];
        //加密密码
        $hashed_password = Hash::make($password);
        //存入数据库
        $user = $this;
        $user->password = $hashed_password;
        $user->username = $username;
        if ($user->save())
            return ['status' => 1, 'id' => $user->id];
        else
            return ['status' => 0, 'msg' => 'db insert failed'];
    }
}
