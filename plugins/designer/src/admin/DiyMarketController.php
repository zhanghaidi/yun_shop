<?php
/**
 * Created by PhpStorm.
 * User: 芸众网
 * Date: 2019/6/12
 * Time: 14:56
 */

namespace Yunshop\Designer\admin;


use app\common\components\BaseController;
use Yunshop\Designer\services\SyncDiyMarketService;
use Yunshop\Designer\models\DiyMarketSync;
use Yunshop\Designer\services\ChooseDiyMarketService;

class DiyMarketController extends BaseController
{
    private $url = 'http://gy18465381.imwork.net/designer-market/get-designer-data/';

    //diy 模板市场
    public function index()
    {
        $data = $this->getCategory();
        $data['data'] = json_encode($this->pageList());
        return view('Yunshop\Designer::admin.diy-market.index',$data)->render();
    }

    /**
     * 删除全部 
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteAll()
    {
        $result = DiyMarketSync::where('id','>',0)->delete();
        if($result){
            return $this->successJson('删除成功');
        }else{
            return $this->errorJson('删除失败');
        }

    }

    /**
     * @return array|\Illuminate\Http\JsonResponse
     */
    private function getCategory()
    {
        //数据查询去重查询
        $result = DiyMarketSync::select('type','page','category')->distinct()->get();
        if(!$result){
            return $this->errorJson('分类信息不存在');
        }
        $result = $result->toArray();
        $arr = [];
        //去重
        foreach ($result as $key => $value){
            $arr['page'][$value['page']] = $value['page'];
            $arr['type'][$value['type']] = $value['type'];
            $arr['category'][$value['category']] = $value['category'];
        }
        //转json
        foreach ($arr as $key => $value){
            $value['0'] = '全部';
            $arr[$key] = json_encode($value);
        }
        return $arr;
    }

    /**
     * 同步接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function chickSync()
    {
        $result = new SyncDiyMarketService();
        if($result == true){
            return $this->successJson('同步成功');
        }
        return $this->errorJson('同步失败');
    }

    /**
     * 检索参数
     * @return array|string
     */
    private function searchParem()
    {
        return request()->input();
    }

    private function pageParam()
    {
        return request()->page ?: 0;
    }

    private function pageList()
    {
        $pageModel = DiyMarketSync::select('id','sync_id','page','type','category','title','title','thumb_url','data','created_at');
        $search = $this->searchParem();
        if($search){
            $pageModel = $pageModel->search($search);
        }
        return $pageModel->orderBy('created_at', 'desc')->paginate('', ['*'], '', $this->pageParam())->toArray();
    }

    /**
     * 搜索
     */
    public function search()
    {
        return $this->successJson('ok',$this->pageList());
    }

    /**
     *  选取接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function choose()
    {
        //远端主键ID
        $id = request()->input('id');
        if(!$id){
            return $this->errorJson('参数错误');
        }
        $result = (new ChooseDiyMarketService($id))->handle();

        if(is_numeric($result)){
            return $this->successJson('选取成功',['id' => $result]);
        }else{
            return $this->errorJson($result->msg);
        }
    }
}
