<?php

namespace Yunshop\Poster\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Poster\models\PosterAward;

class PosterAwardController extends  BaseController
{
    /*
     * 查询奖励记录
     */
    public function index()
    {
        $pageSize = 10;
        $searchRecommender = \YunShop::request()->searchRecommender;
        $searchSubscriber = \YunShop::request()->searchSubscriber;
        $enableSearchTime = \YunShop::request()->searchTime;
        $searchTimeStart = strtotime(\YunShop::request()->time['start']);
        $searchTimeEnd = strtotime(\YunShop::request()->time['end']);
        $posterId = intval(\YunShop::request()->poster_id); //如果没有值, 则展示所有记录; 如果有值, 则展示该海报下的奖励记录

        if (empty($searchRecommender) && empty($searchSubscriber) && ($enableSearchTime == 0)){
            if ($posterId){
                $posterAwards = PosterAward::getPosterAwards($posterId)->paginate($pageSize);
            } else{
                $posterAwards = PosterAward::getPosterAwards()->paginate($pageSize);
            }
        } else {
            $posterAwards = PosterAward::searchPosterAwards(array(
                'posterId' => $posterId,
                'recommender'=> $searchRecommender,
                'subscriber' => $searchSubscriber,
                'timeStart' => $searchTimeStart,
                'timeEnd' => $searchTimeEnd,
                'searchTime' => $enableSearchTime
            ))->paginate($pageSize);
        }

        //扫描总数
        $posterAwardsSum = $posterAwards->total();

        $pager = PaginationHelper::show($posterAwards->total(), $posterAwards->currentPage(), $posterAwards->perPage());
        return view('Yunshop\Poster::admin.award',
            [
                'posterId' => $posterId,
                'posterAwards'=>$posterAwards,
                'posterAwardsSum'=>$posterAwardsSum,
                'pager' => $pager,
                'timeStart' => $enableSearchTime ? $searchTimeStart : 0,
                'timeEnd' => $enableSearchTime ? $searchTimeEnd : 0,
            ]
        )->render();
    }
}