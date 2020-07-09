<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/4/12
 * Time: 9:45 AM
 */

namespace Yunshop\Designer\Backend\Modules\Page\Controllers;


use app\common\components\BaseController;
use Yunshop\Article\models\Article;

class SearchArticleController extends BaseController
{
    /**
     * 文章组件：搜索文章接口
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if ($this->articlePluginStatus()) {
            return $this->successJson('ok', $this->resultData());
        }
        return $this->errorJson('请先开启文章营销插件');
    }

    public function designerResult()
    {
        $res = [];
        if ($this->articlePluginStatus()) {

            return $this->articleList()?$this->articleList()->toArray():$res;
        }
        return $res;
    }

    /**
     * return data
     *
     * @return array
     */
    private function resultData()
    {
        return ['articleList' => $this->articleList()];
    }

    /**
     * 文章列表集 array
     *
     * @return array
     */
    private function articleList()
    {
        $articleModels = $this->articleModels();

        return $articleModels;
    }

    /**
     * 文章列表集 models
     *
     * @return \app\framework\Database\Eloquent\Collection
     */
    private function articleModels()
    {
        $articleModels = Article::uniacid();

        $member_id = \YunShop::app()->getMemberId();

        $keyword = $this->keyword();
        if ($keyword) {
            $articleModels->where('title', 'like', "%{$keyword}%");
        }

        if (app('plugins')->isEnabled('article-pay')) {
            $articleModels->with(["hasOneArticlePay"=>function ($query) {
                $query->where("status",0)->where("money",">",0)->select("article_id","money");
            }]);

            $articleModels->with(["hasOneRecord"=>function ($query) use ($member_id){
                $query->where("member_id",$member_id)->where("pay_status",1)->select("article_id","pay_status");
            }]);
        }

        $articleModels = $articleModels->where('state', 1)->with('belongsToCategory')->get();

        foreach ($articleModels as &$itme){
            $itme->title = str_replace("&quot;",'"',$itme['title']);
        }
        return $articleModels;//$articleModels->where('state', 1)->with('belongsToCategory')->get();
    }

    /**
     * 文章营销插件状态
     *
     * @return bool
     */
    private function articlePluginStatus()
    {
        return app('plugins')->isEnabled('article') ? true : false;
    }

    /**
     * 搜索关键字
     *
     * @return string
     */
    private function keyword()
    {
        return (string)request()->input('keyword');
    }
}
