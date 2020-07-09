<?php

namespace Yunshop\Wechat\admin\reply\controller;

use app\common\components\BaseController;
use Yunshop\Wechat\admin\reply\model\Rule;
use Yunshop\Wechat\common\model\RuleKeyword;

class KeywordsAutoReplyController extends BaseController
{

    public function index()
    {
        return view('Yunshop\Wechat::admin.reply.reply');
    }

    public function search()
    {
        //请求的类型，image,news,basic,voice,video,music中的一种，当为空时则查询所有
        $type = request('type');
        //搜索类型，规则为1，关键字为2
        $search_type = request('search_type');
        //搜索内容
        $search = request('search');
        // 分页信息
        $page = (int)request()->page ? (int)request()->page : 1;
        $result = Rule::getRuleByType($type,$search_type,$search,$page);

        return $this->successJson('success',$result['data']);
    }

    public function save()
    {
        $form = request('form_data');
        $result = Rule::saveRule($form);
        if ($result['status']) {
            return $this->successJson($result['message'],$result['data']);
        } else {
            return $this->errorJson($result['message']);
        }
    }
    public function edit()
    {
        $id = request('id');
        $data = Rule::getRuleAndKeywordsAndRepliesByRuleId($id);
        return view('Yunshop\Wechat::admin.reply.edit', ['data'=>json_encode($data)]);
    }
    public function delete()
    {
        $id = request('id');
        $result = Rule::deleteRuleById($id);
        if ($result['status']) {
            return $this->successJson($result['message'],$result['data']);
        } else {
            return $this->errorJson($result['message']);
        }
    }

    public function status()
    {
        $id = request('id');
        $status = request('status');
        $result = Rule::changeRuleStatus($id,$status);
        if ($result['status']) {
            return $this->successJson($result['message'],$result['data']);
        } else {
            return $this->errorJson($result['message']);
        }
    }

    // 默认回复，首次访问自动回复中会选择关键字，通过该方法查询关键字并返回关键字及其规则，前端选择关键字后，将关键字id传给后端
    public function getKeywords()
    {
        $search = request('search');
        $data = RuleKeyword::searchRuleKeywords($search);
        return $this->successJson('success',$data);
    }
}