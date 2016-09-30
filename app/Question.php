<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //创建问题
    public function add()
    {
        //检查用户是否登录
        if (!user_ins()->is_logged_in())
            return ['status' => 0, 'msg' => 'login required'];
        //检查是否存在标题
        if (!rq('title'))
            return ['status' => 0, 'msg' => 'title required'];

        $this->title = rq('title');
        $this->user_id = session('user_id');
        if (rq('desc'))
            $this->desc = rq('desc');

        return $this->save() ?
            ['status' => 1, 'id' => $this->id] :
            ['status' => 0, 'msg' => 'db insert failed'];
    }

    public function change()
    {
        //检查用户是否登录
        if (!user_ins()->is_logged_in())
            return ['status' => 0, 'msg' => 'login required'];

        //检查传参中是否有id
        if (!rq('id'))
            return ['status' => 0, 'msg' => 'id required'];

        //获取指定的model
        $question = $this->find(rq('id'));

        //判断问题是否存在
        if (!$question)
            return ['status' => 0, 'msg' => 'question not exists'];
        if ($question->user_id != session('user_id'))
            return ['status' => 0, 'msg' => 'permission denied'];

        if (rq('title'))
            $question->title = rq('title');
        if (rq('desc'))
            $question->desc = rq('desc');

        //保存数据
        return $question->save() ?
            ['status' => 1] :
            ['status' => 0, 'msg' => 'db update failed'];
    }

    //查看问题API
    public function read()
    {
        //如果有id,直接返回问题
        if (rq('id'))
            return ['status' => 1, 'msg' => $this->find(rq('id'))];
        //用于分页
        $limit = rq('limit') ?: 15;
        $skip = (rq('page') ? rq('page') - 1 : 0) * $limit;
        //构建query并返回数据
        $r = $this
            ->orderBy('created_at')
            ->limit($limit)
            ->skip($skip)
            ->get(['id', 'title', 'desc', 'user_id', 'created_at', 'updated_at'])
            ->keyBy('id');

        return ['status' => 1, 'data' => $r];
    }
}
