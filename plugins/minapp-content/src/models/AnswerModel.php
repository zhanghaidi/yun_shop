<?php

namespace Yunshop\MinappContent\models;

use app\common\models\BaseModel;

class AnswerModel extends BaseModel
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;

    public $table = 'diagnostic_service_answer';

    // 根据条目和原始分获得转换分 算法
    public static function derivedScore($rawScore, $nums)
    {
        //原始分＝各个条目的分值相加。
        //转化分数＝［（原始分-条目数）÷（条目数×4）］×100
        //比如您在回答“阳虚质”问题时，共7个条目的问题，（1）-（7）题得分情况为：3/4/2/4/1/5/3，那么:
        //[原始分]=3+4+2+4+1+5+3=22分，
        //[转化分]=[(22-7)÷(7×4)]×100 = 15÷28×100 = 53.57
        $derivedScore = ($rawScore - $nums) / ($nums * 4) * 100;

        return intval(round($derivedScore));
    }

    // 根据字段对多维数组进行排序
    public static function arraySortByOneField(array $data, string $field, int $sort = SORT_DESC)
    {
        $field = array_column($data, $field);
        array_multisort($field, $sort, $data);
        return $data;
    }

    // 获得确定偏颇体质数组
    public static function getTrueSomatoType(array $data)
    {
        $trueSomatoTypeArr = array();
        foreach ($data as $v) {
            if ($v['status'] == 2) {
                $trueSomatoTypeArr[] = $v;
            }
        }
        return $trueSomatoTypeArr;
    }

    // 获得体质数组字段值
    public static function getSomatoTypeByField(array $data, string $field)
    {
        $typeArr = array();
        foreach ($data as $v) {
            $typeArr[] = $v[$field];
        }
        return $typeArr;
    }

    // 获得兼有偏颇体质数组
    public static function getHasSomatoType(array $data)
    {
        $hasSomatoTypeArr = array();
        foreach ($data as $v) {
            if ($v['status'] == 1) {
                $hasSomatoTypeArr[] = $v;
            }
        }
        return $hasSomatoTypeArr;
    }

    // 平和质>=60  其他8种体质转化分均﹤30分 是  $hasArr
    // 平和质>=60  其他8种体质转化分均﹤40分 基本是 $tureArr
    // 否则不是平和质
    public static function getGentleSomatoType(int $status, array $hasArr, array $tureArr)
    {
        if ($status == 0) {
            // 小于60
            return 1;
        } else {
            // 大于60
            if (!isset($hasArr[0]) && !isset($tureArr[0])) {
                //《30
                return 3;
            } elseif (!isset($tureArr[0])) {
                //《40
                return 2;
            } else {
                return 1;
            }
        }
    }
}
