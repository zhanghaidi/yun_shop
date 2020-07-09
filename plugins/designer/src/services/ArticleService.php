<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/7/3
 * Time: 14:07
 */

namespace Yunshop\Designer\services;


class ArticleService extends \Yunshop\Article\models\Article
{

    public static function getArticleByIds($ids,$member_id=0)
    {
        $articleModel = static::uniacid()->whereIn('id', $ids)->with('belongsToCategory');

        if (app('plugins')->isEnabled('article-pay')) {
            $articleModel->with(["hasOneArticlePay"=>function ($query) {
                $query->where("status",0)->where("money",">",0)->select("article_id","money");
            }]);

            $articleModel->with(["hasOneRecord"=>function ($query) use ($member_id){
                $query->where("member_id",$member_id)->where("pay_status",1)->select("article_id","pay_status");
            }]);
        }

        return $articleModel->get()->toArray();
    }

    public static function getArticleById($id,$member_id)
    {
        $articleModel = static::uniacid()->where('id', $id)->where('state',1);

        if (app('plugins')->isEnabled('article-pay')) {
            $articleModel->with(["hasOneArticlePay"=>function ($query) {
                $query->where("status",0)->where("money",">",0)->select("article_id","money");
            }]);

            $articleModel->with(["hasOneRecord"=>function ($query) use ($member_id){
                $query->where("member_id",$member_id)->where("pay_status",1)->select("article_id","pay_status");
            }]);
        }

        return $articleModel->first();
    }

}