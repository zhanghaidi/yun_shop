<?php

namespace Yunshop\VideoDemand\services;


use Yunshop\VideoDemand\models\LecturerRewardLogModel;

class LecturerRewardLogService
{

    public static function getLecturerReward($data)
    {
        foreach ($data as &$item) {
            $item->statement = LecturerRewardLogModel::getRewardLogByLecturerId($item->id)->where('status', '1')->sum('amount');
            $item->not_statement = LecturerRewardLogModel::getRewardLogByLecturerId($item->id)->where('status', '0')->sum('amount');
        }
        return $data;
    }

    public static function getRewardTypeName($data)
    {
        switch ($data->reward_type) {
            case 0:
                return '讲师分红';
                break;
            case 1:
                return '打赏佣金';
                break;
        }
    }

    public static function getStatusName($data)
    {
        switch ($data->status) {
            case 0:
                return '未结算';
                break;
            case 1:
                return '已结算';
                break;
        }
    }

}