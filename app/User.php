<?php

namespace App;

use Hash;
use Request;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function has_username_and_password()
    {
        $username = rq('username');
        $password = rq('password');
        //检查用户名和密码是否为空
        if ($username && $password)
            return [$username, $password];
        return false;
    }

    //注册API
    public function signup()
    {
        $has_username_and_password = $this->has_username_and_password();
        if (!$has_username_and_password)
            return ['status' => 0, 'msg' => '用户名和密码皆不可为空'];
        $username = $has_username_and_password[0];
        $password = $has_username_and_password[1];

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

    //登录API
    public function login()
    {
        //检查用户名和密码是否存在
        $has_username_and_password = $this->has_username_and_password();
        if (!$has_username_and_password)
            return ['status' => 0, 'msg' => '用户名和密码皆不可为空'];
        $username = $has_username_and_password[0];
        $password = $has_username_and_password[1];
        //检查用户是否存在
        $user = $this->where('username', $username)->first();
        if (!$user)
            return ['status' => 0, 'msg' => '用户名不存在'];
        //检查密码是否正确
        $hashed_password = $user->password;
        if (!Hash::check($password, $hashed_password))
            return ['status' => 0, 'msg' => '密码有误'];
        //将用户信息写入session
        session()->put('username', $user->username);
        session()->put('user_id', $user->id);

        return ['status' => 1, 'id' => $user->id];
    }

    //检测用户是否登录
    public function is_logged_in()
    {
        return session('user_id') ?: false;
    }

    //登出API
    public function logout()
    {
        //删除name和id
        session()->forget('username');
        session()->forget('user_id');
        //session()->put('username', null);
        //session()->put('user_id', null);
        return ['status' => 1];
        //return redirect('/');//可跳转到首页
    }

    public function answers()
    {
        return $this
            ->belongsToMany('App\Answer')
            ->withPivot('vote')
            ->withTimestamps();
    }
}
