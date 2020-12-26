<?php

namespace Yunshop\Examination\models;

use app\common\models\BaseModel;

class QuestionSortModel extends BaseModel
{
    public $table = 'yz_exam_question_sort';

    public function getList(int $serviceId)
    {
        $listRs = (new self)->where('uniacid', $serviceId)
            ->orderBy('order', 'desc')
            ->orderBy('id', 'desc')->get()->toArray();
        return $listRs;
    }

    public function getOrderList(int $serviceId)
    {
        $listRs = (new self)->where('uniacid', $serviceId)
            ->orderBy('order', 'desc')
            ->orderBy('id', 'desc')->get()->toArray();
        self::getNodeTree($listRs, $tree);
        return $tree;
    }

    public static function getNodeTree(&$list, &$tree, $pid = 0)
    {
        foreach ($list as $k => $v) {
            if ($pid == $v['pid']) {
                $tree[$v['id']] = $v;
                unset($list[$k]);
                self::getNodeTree($list, $tree[$v['id']]['children'], $v['id']);
            }
        }
    }

    public function paintTree(array $list, string $symbolFirst = ' &nbsp; |__ ', string $symbolSecond = " &nbsp; &nbsp; |__ ")
    {
        foreach ($list as $k1 => $v1) {
            if (!isset($v1['children'])) {
                continue;
            }

            foreach ($v1['children'] as $k2 => $v2) {
                $list[$k1]['children'][$k2]['name'] = $symbolFirst . $v2['name'];

                if (!isset($v2['children'])) {
                    continue;
                }

                foreach ($v2['children'] as $k3 => $v3) {
                    $list[$k1]['children'][$k2]['children'][$k3]['name'] = $symbolSecond . $v3['name'];
                }
            }
        }
        return $list;
    }
}
