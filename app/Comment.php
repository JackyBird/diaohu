<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //添加评论API
    public function add()
    {
        //检查用户是否登录
        if (!user_ins()->is_logged_in())
            return ['status' => 0, 'msg' => 'login required'];
        //检查是否有评论
        if (!rq('content'))
            return ['status' => 0, 'msg' => 'content required'];
        //检查评论对象合法性
        if (
            (!rq('question_id') && !rq('answer_id')) || //none
            (rq('question_id') && rq('answer_id')) //all
        )
            return ['status' => 0, 'msg' => 'question_id or answer_id is required'];
        //检查给问题评论还是回答评论
        if (rq('question_id')) {
            $question = question_ins()->find(rq('question_id'));
            if (!$question)
                return ['status' => 0, 'msg' => 'question not exists'];
            $this->question_id = rq('question_id');
        } else {
            $answer = answer_ins()->find(rq('answer_id'));
            if (!$answer)
                return ['status' => 0, 'msg' => 'answer not exists'];
            $this->answer_id = rq('answer_id');
        }
        //检查是否在回复再评论
        if (rq('reply_to')) {
            $target = $this->find(rq('reply_to'));
            //检查目标评论是否存在
            if (!$target)
                return ['status' => 0, 'msg' => 'target comment not exists'];
            //检查是否回复自己的评论
            if ($target->user_id== session('user_id'))
                return ['status' => 0, 'msg' => 'can not reply to yourself'];
            $this->reply_to = rq('reply_to');
        }
        //保存数据
        $this->content = rq('content');
        $this->user_id = session('user_id');
        return $this->save() ?
            ['status' => 1, 'id' => $this->id] :
            ['status' => 0, 'msg' => 'db insert failed'];
    }
}
