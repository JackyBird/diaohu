<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    //增加问题API
    public function add()
    {
        //检查用户是否登录
        if (!user_ins()->is_logged_in())
            return ['status' => 0, 'msg' => 'login required'];
        //检查参数中是否有question_id和content
        if (!rq('question_id') || !rq('content'))
            return ['status' => 0, 'msg' => 'question_id and content are required'];
        //检查问题是否存在
        $question = question_ins()->find(rq('question_id'));
        if (!$question) return ['status' => 0, 'msg' => 'question not exists'];
        //检查是否重复回答
        $answered = $this
            ->where(['question_id' => rq('question_id'), 'user_id' => session('user_id')])
            ->count();

        if ($answered)
            return ['status' => 0, 'msg' => 'duplicate answers'];
        //保存数据
        $this->content = rq('content');
        $this->question_id = rq('question_id');
        $this->user_id = session('user_id');

        return $this->save() ?
            ['status' => 0, 'id' => $this->id] :
            ['status' => 0, 'msg' => 'db insert failed'];
    }

    //更新回答API
    public function change()
    {
        //检查用户是否登录
        if (!user_ins()->is_logged_in())
            return ['status' => 0, 'msg' => 'login required'];
        //检查参数中是否有question_id和content
        if (!rq('id') || !rq('content'))
            return ['status' => 0, 'msg' => 'id and content are required'];
        $answer = $this->find(rq('id'));
        //判断问题是否存在
        if (!$answer)
            return ['status' => 0, 'msg' => 'answer not exists'];
        //检查是否为问题所有者修改
        if ($answer->user_id != session('user_id'))
            return ['status' => 0, 'msg' => 'permission denied'];
        //保存修改后的content
        $answer->content = rq('content');
        return $answer->save() ?
            ['status' => 1] :
            ['status' => 0, 'msg' => 'db updated failed'];

    }
}
