<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/20
 * Time: 15:50
 */

namespace Yunshop\Article\api;


use app\common\components\ApiController;
use app\common\components\BaseController;
use Yunshop\Article\models\Article;

class ArticlePageController extends ApiController
{
    
    public function page()
    {
        $pageSize = request()->pageSize;
        $list = Article::uniacid()->where('type', '!=', 1)->where('state', '!=', 0)->with('belongsToCategory')->paginate($pageSize)->toArray();

        foreach ($list['data'] as $k => $value) {
            $list['data'][$k]['articleid'] = $value['id'];
            if ( $value['thumb']) {
                $list['data'][$k]['thumb'] = yz_tomedia($value['thumb']);
            }
        }

        if (empty($list)) {
            return $this->errorJson('沒有文章!', -1);
        }
        return $this->successJson('ok', $list);
    }

}