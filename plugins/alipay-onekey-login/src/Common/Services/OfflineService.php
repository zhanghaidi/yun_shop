<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/1/30 上午10:08
 * Email: livsyitian@163.com
 */

namespace Yunshop\Love\Common\Services;

use Illuminate\Support\Facades\Log;
use Yunshop\Love\Common\Models\MemberShop;

class OfflineService
{
    private $first_offline;

    private $second_offline;

    private $third_offline;

    private $team_offline;


    /**
     * 获取会员一级团队集合
     * @param $member_id
     * @return array
     */
    public function getFirstOffline($member_id)
    {
        return $this->setFirstOffline($member_id);
    }


    /**
     * 获取会员二级团队集合
     * @param $member_id
     * @return array
     */
    public function getSecondOffline($member_id)
    {
        return $this->setSecondOffline($member_id);
    }


    /**
     * 获取会员三级团队集合
     * @param $member_id
     * @return array
     */
    public function getThirdOffline($member_id)
    {
        return $this->setThirdOffline($member_id);
    }


    /**
     * 获取会员层级集合
     * @param $member_id
     * @param $level
     * @return array
     */
    public function getMemberLevelOffline($member_id, $level)
    {
        return $this->levelOffline($member_id, $level);
    }


    /**
     * 获取会员团队集合
     * @param $member_id
     * @return array
     */
    public function getTeamOffline($member_id)
    {
        return $this->setTeamOffline($member_id);
    }




    /**
     * 会员一级人数集合 （为获取一二三级最优查询，保留该方法）
     * @param $member_id
     * @return array
     */
    private function setFirstOffline($member_id)
    {
        $member_ids[] = $member_id;

        $this->first_offline = $this->getMemberOffline($member_ids);

        return $this->first_offline;
    }


    /**
     * 会员二级人数集合 （为获取一二三级最优查询，保留该方法）
     * @param $member_id
     * @return array
     */
    private function setSecondOffline($member_id)
    {
        !isset($this->first_offline) && $this->setFirstOffline($member_id);

        $this->second_offline = $this->getMemberOffline($this->first_offline);

        return  $this->second_offline;
    }


    /**
     * 会员三级人数集合 （为获取一二三级最优查询，保留该方法）
     * @param $member_id
     * @return array
     */
    private function setThirdOffline($member_id)
    {
        !isset($this->second_offline) && $this->setSecondOffline($member_id);

        $this->third_offline = $this->getMemberOffline($this->second_offline);

        return  $this->third_offline;
    }


    /**
     * 会员层级下线，通过 $level 获取对应层级会员ID集合
     * @param $member_id
     * @param int $level
     * @return array
     */
    private function levelOffline($member_id, $level = 0)
    {
        $i = 1;
        $assemble[] = $member_id;

        while ($i <= $level) {
            $i++;
            $assemble = $this->getMemberOffline($assemble);
        }
        return $assemble;
    }


    /**
     * 会员团队 团队下线集合
     * @param $member_id
     * @return array
     */
    private function setTeamOffline($member_id)
    {
        set_time_limit(0);
        !isset($this->third_offline) && $this->setThirdOffline($member_id);

        $result = true;

        $assemble = $this->third_offline;

        $result_assemble = array_merge($this->first_offline,$this->second_offline,$this->third_offline);

        do {
            $assemble = $this->getMemberOffline($assemble);

            $error_assemble = array_intersect($result_assemble, $assemble);

            if (!empty($error_assemble)) {
                $result = false;
                //Log::info('会员关系错误，会员ID：' . $member_id . '错误信息', print_r($error_assemble,true));
                Log::info('会员关系错误，会员ID：' . $member_id . '团队下线存在问题');
            }

            $result_assemble = array_merge($result_assemble,$assemble);

        } while (!empty($assemble) && $result);

        $this->team_offline = $result_assemble;

        return $this->team_offline;
    }



    /**
     * 查询会员（集合）下线 会员ID集合
     * @param array $member_ids
     * @return array
     */
    private function getMemberOffline(array $member_ids)
    {
        if (count($member_ids) > 10000) {

            $member_ids = array_chunk($member_ids, 10000);
            $result_assemble = [];
            foreach ($member_ids as $item) {
                $assemble = MemberShop::select('member_id')->whereIn('parent_id',$item)->get();
                $assemble = $assemble->isEmpty() ? [] : array_pluck($assemble->toArray(), 'member_id');

                $result_assemble = array_merge($result_assemble,$assemble);
            }
            return $result_assemble;
        }

        $assemble = MemberShop::select('member_id')->whereIn('parent_id',$member_ids)->get();

        return $assemble->isEmpty() ? [] : array_pluck($assemble->toArray(), 'member_id');
    }






}
