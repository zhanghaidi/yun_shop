<?php

namespace Yunshop\Poster\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Poster\models\PosterScan;

class PosterScanController extends  BaseController
{
    /*
     * 查询海报扫码记录
     */
    public function index()
    {
        $pageSize = 10;
        $searchRecommender = \YunShop::request()->searchRecommender;
        $searchSubscriber = \YunShop::request()->searchSubscriber;
        $enableSearchTime = \YunShop::request()->searchTime;
        $searchTimeStart = strtotime(\YunShop::request()->time['start']);
        $searchTimeEnd = strtotime(\YunShop::request()->time['end']);

        $posterId = intval(\YunShop::request()->poster_id); //如果没有值, 则展示所有记录; 如果有值, 则展示该海报下的扫描记录

        if (empty($searchRecommender) && empty($searchSubscriber) && ($enableSearchTime == 0)){
            if ($posterId){
                $posterScans = PosterScan::getDetailedPosterScan($posterId)->paginate($pageSize);
            } else{
                $posterScans = PosterScan::getDetailedPosterScan()->paginate($pageSize);
            }
        } else {
            $posterScans = PosterScan::searchPosterScan(array(
                'posterId' => $posterId,
                'recommender'=> $searchRecommender,
                'subscriber' => $searchSubscriber,
                'timeStart' => $searchTimeStart,
                'timeEnd' => $searchTimeEnd,
                'searchTime' => $enableSearchTime
            ))->paginate($pageSize);
        }

        //扫描总数
        $posterScansSum = $posterScans->total();

        $pager = PaginationHelper::show($posterScans->total(), $posterScans->currentPage(), $posterScans->perPage());
        return view('Yunshop\Poster::admin.scan',
                        [
                            'posterId' => $posterId,
                            'posterScans'=>$posterScans,
                            'posterScansSum'=>$posterScansSum,
                            'pager' => $pager,
                            'timeStart' => $enableSearchTime ? $searchTimeStart : 0,
                            'timeEnd' => $enableSearchTime ? $searchTimeEnd : 0,
                        ]
                    )->render();
    }
}