<?php
/****************************************************************
 * Author:  king -- LiBaoJia
 * Date:    2020/4/1 4:59 PM
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * IDE:     PhpStorm
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/
namespace app\Console\Commands;

use app\common\models\Member;
use app\common\models\MemberShopInfo;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Readers\LaravelExcelReader;
use Yunshop\Haifen\common\model\hfMember;
use Yunshop\Haifen\common\model\levelModel;
use app\common\events\member\MemberLevelUpgradeEvent;
use Yunshop\Commission\models\Agents;
use Yunshop\Merchant\common\services\CenterUpgradeService;
use Yunshop\Commission\services\UpgradeService;
use app\Jobs\DispatchesJobs;
use Yunshop\Commission\Jobs\UpgrateByRegisterJob;
use Yunshop\TeamDividend\admin\models\TeamDividendAgencyModel;
use Yunshop\TeamDividend\models\TeamDividendLevelModel;
use Yunshop\TeamDividend\models\Log;

class MigrateHFLevelExcelData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:level_excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '迁移会员levelExcel数据';

    protected $uniacid = 1;

    /**
     * Execution entrance
     */
    public function handle()
    {

        if (!app('plugins')->isEnabled('haifen')) {
            file_put_contents(base_path('migrate_hf_level_excel.txt'), "haifen插件未开启" . PHP_EOL, FILE_APPEND);
            return ;
        }

        \YunShop::app()->uniacid = 3;

        $filePath = 'storage/app/public/levelexcel/hf_user_level.xlsx';

        //$filePath = storage_path('app/public/levelexcel/hf_user_level.xls');

        $fileContent = \Excel::load($filePath);


        /**
         * @see LaravelExcelReader
         *
         * @var \PHPExcel $fileContent
         */

        $sheet = $fileContent->getActiveSheet();
        /**
         * @var \PHPExcel_Worksheet $sheet
         */
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();
        $highestColumnCount = \PHPExcel_Cell::columnIndexFromString($highestColumn);

        $row = 2;
        $values = [];
        while ($row <= $highestRow) {
            $col = 0;
            $rowValue = [];
            while ($col < $highestColumnCount) {
                $rowValue[] = (string)$sheet->getCellByColumnAndRow($col, $row)->getValue();
                ++$col;
            }
            $values[] = $rowValue;
            ++$row;
        }

        //dd($values);
        $count = count($values);
        //dd($count);
        $barOne = $this->output->createProgressBar($count);
        foreach ($values as $value) {
            $this->updateMemberLevel($value);
            $barOne->advance();
        }
        $barOne->finish();

        $this->info('Update Member Level Comment');
        $this->comment('Excel data migration completed!');
    }

    public function updateMemberLevel($value)
    {
        //根绝mobile查询会员信息
        $user = hfMember::uniacid()->whereHas("hasOneMCMember",function($query) use ($value){
            $query->where("mobile",$value[0]);
        })->first();

        $user = empty($user) ? [] : $user->toArray();

        if (empty($user)) {
            file_put_contents(base_path('migrate_hf_level_excel.txt'), "手机号:".$value[0]."对应的会员不存在" . PHP_EOL, FILE_APPEND);
            return ;
        }

        $this->docking($user['yz_uid'],$value[1],$value[0]);
        //$this->docking("84","vip","18930853129");
    }

    public function docking($uid,$level,$mobile)
    {
        $search['level'] = $level;
        $search['status'] = 1;
        $levelModel = levelModel::getSearch($search)->first();
        $levelModel = empty($levelModel) ? [] : $levelModel->toArray();

        if (empty($levelModel)) {
            //throw new ShopException('对应等级未设置');
            file_put_contents(base_path('migrate_hf_level_excel.txt'), "手机号:".$mobile."后台对应的等级不存在" . PHP_EOL, FILE_APPEND);
            return  ;
        }

        if (empty($levelModel['has_one_member_level'])) {
            //throw new ShopException('对应等级设置中的会员等级不存在');
            file_put_contents(base_path('migrate_hf_level_excel.txt'), "手机号:".$mobile."后台对应等级设置中的会员等级不存在" . PHP_EOL, FILE_APPEND);
            return  ;
        }

        //修改会员等级
        $member = MemberShopInfo::uniacid()->where("member_id",$uid)->first();

        if (empty($member)) {
            //throw new ShopException('会员不存在');
            file_put_contents(base_path('migrate_hf_level_excel.txt'), "手机号:".$mobile."会员不存在" . PHP_EOL, FILE_APPEND);
            return  ;
        }

        $is_upgrade = false;

        if ($member->level_id != $levelModel['member_level_id'] && $levelModel['has_one_member_level']['level'] > $member->level->level) {
            $is_upgrade = true;
        }

        $member->level_id = $levelModel['member_level_id'];

        //判断用户是否是推广员
        if ($member->is_agent != 1) {
            $member->is_agent = 1;
            $member->status = 2;
            $member->agent_time = time();
            \Log::info('海粉成为推广员');
        }

        if (!$member->save()) {
            //throw new ShopException('更新对应的会员等级失败');
            file_put_contents(base_path('migrate_hf_level_excel.txt'), "手机号:".$mobile."更新对应的会员等级失败" . PHP_EOL, FILE_APPEND);
            return  ;
        }

        if ($is_upgrade == true) {
            //会员等级升级触发事件
            event(new MemberLevelUpgradeEvent($member, true));
        }

        //修改分销商等级
        $this->agentLevelUpdate($uid,$member,$mobile,$levelModel);

        //修改经销商等级
        $this->teamDividendLevelUpdate($uid,$member,$mobile,$levelModel);
    }

    public function agentLevelUpdate($uid,$member,$mobile,$levelModel)
    {
        if (app('plugins')->isEnabled('commission')) {

            if (empty($levelModel['has_one_agent_level'])) {
                file_put_contents(base_path('migrate_hf_level_excel.txt'), "手机号:".$mobile."对应等级设置中的分销商等级不存在" . PHP_EOL, FILE_APPEND);
                return  ;
                //throw new ShopException('对应等级设置中的分销商等级不存在');
            }

            $agent = Agents::getAgentByMemberId($uid)->first();

            if (empty($agent)) {
                //成为分销商
                $set = \Setting::get('plugin.commission');
                $agentData = [
                    'uniacid' => \YunShop::app()->uniacid,
                    'member_id' => $uid,
                    'parent_id' => $member->parent_id,
                    'parent' => $member->relation,
                    'created_at' => time(),
                    'updated_at' => time(),
                ];

                \Log::info('分销商数据：', $agentData);
                Agents::insert($agentData);
                $this->upgrade($member,$set);
            } else {
                if ($agent->agent_level_id != $levelModel['agent_level_id']) {
                    $agent->agent_level_id = $levelModel['agent_level_id'];
                    if ($agent->save()) {
                        if (app('plugins')->isEnabled('merchant')) {
                            CenterUpgradeService::handle($uid);
                        }
                    } else {
                        file_put_contents(base_path('migrate_hf_level_excel.txt'), "手机号:".$mobile."添加对应的分销商等级失败" . PHP_EOL, FILE_APPEND);
                        return  ;
                        //throw new ShopException('添加对应的分销商等级失败');
                    }
                }
            }
        }
    }

    public function teamDividendLevelUpdate($uid,$member,$mobile,$levelModel)
    {

        if (app('plugins')->isEnabled('team-dividend')) {

            if (empty($levelModel['has_one_team_dividend_level'])) {
                file_put_contents(base_path('migrate_hf_level_excel.txt'), "手机号:".$mobile."对应等级设置中的经销商等级不存在" . PHP_EOL, FILE_APPEND);
                return ;
                // throw new ShopException('对应等级设置中的经销商等级不存在');
            }

            $dividend = TeamDividendAgencyModel::getAgencyByMemberId($uid)->first();

            if (empty($dividend)) {
                $agency_model = new TeamDividendAgencyModel();
                $agency_model->uniacid = \YunShop::app()->uniacid;
                $agency_model->uid = $uid;
                $agency_model->level = $levelModel['team_dividend_level_id'];
                $agency_model->parent_id = $member['parent_id'];
                $agency_model->relation = $member['relation'];

                if ($agency_model->save()) {
                    //激活码
                    $upgrade_set = (new \Yunshop\TeamDividend\services\UpgradeService())->getUpgradeById($levelModel['team_dividend_level_id']);

                    if ($upgrade_set) {
                        $set = \Setting::get('plugin.team_dividend');
                        if ($set['is_activation_code'] && $upgrade_set[0]->code_num) {
                            $level_list = TeamDividendLevelModel::getList()->get();
                            $code_num = unserialize($upgrade_set[0]->code_num);
                            foreach($level_list as $key => $level) {
                                if($code_num['level_'.$level->id]) {
                                    (new \Yunshop\TeamDividend\services\UpgradeService())->senCode($uid, $code_num['level_'.$level->id], $level->id, $level->level_name);
                                }
                            }
                            \Log::debug('激活码');
                        }

                    }
                } else {
                    //throw new ShopException('添加对应的经销商等级失败');
                    file_put_contents(base_path('migrate_hf_level_excel.txt'), "手机号:".$mobile."添加对应的经销商等级失败" . PHP_EOL, FILE_APPEND);
                    return ;
                }
            } else {
                $dividend_member = TeamDividendAgencyModel::getAgencyInfoByUid($uid);
                $oldLevel = $dividend_member->level;
                $dividend_member->level = $levelModel['team_dividend_level_id'];
                if ($dividend_member->save()) {
                    $this->upDividend($uid,$oldLevel,$levelModel['team_dividend_level_id']);
                } else {
                    //throw new ShopException('更新对应的经销商等级失败');
                    file_put_contents(base_path('migrate_hf_level_excel.txt'), "手机号:".$mobile."更新对应的经销商等级失败" . PHP_EOL, FILE_APPEND);
                    return ;
                }
            }
        }
    }

    public function upgrade($member, $set)
    {
        $agent = Agents::getAgentByMemberId($member['parent_id'])->first();
        $levels = UpgradeService::getLevelUpgraded();
        if (!$levels) {
            return;
        }
        \Log::debug("监听is_with",$set);
        if ($set['is_with']) {
            $job = new UpgrateByRegisterJob($member['member_id'], $levels);
            DispatchesJobs::dispatch($job,DispatchesJobs::LOW);
        } else {
            //分销商会员下线升级
            UpgradeService::member($member['member_id']);

            //分销商下线升级
            UpgradeService::agent($agent);
        }
    }

    public function upDividend($uid,$level,$newLevel)
    {
        // 经销商等级变动记录
        Log::create([
            'uid' => $uid,
            'befor_level_id' => $level,
            'up_code' => '无',
            'after_level_id' => $newLevel,
            'remark' => 'haifen修改',
            'level_ups' => []
        ]);

        $c_level = TeamDividendLevelModel::find($newLevel);
        $o_level = TeamDividendLevelModel::find($level);

        if ($c_level->level_weight > $o_level->level_weight) {
            //激活码
            $upgrade_set = (new \Yunshop\TeamDividend\services\UpgradeService())->getUpgradeById($newLevel);

            if ($upgrade_set) {
                $set = \Setting::get('plugin.team_dividend');
                if ($set['is_activation_code'] && $upgrade_set[0]->code_num) {
                    $level_list = TeamDividendLevelModel::getList()->get();
                    $code_num = $upgrade_set[0]->code_num;
                    foreach($level_list as $key => $level) {
                        if($code_num['level_'.$level->id]) {
                            (new \Yunshop\TeamDividend\services\UpgradeService())->senCode($uid, $code_num['level_'.$level->id], $level->id, $level->level_name);
                        }
                    }
                    \Log::debug('激活码');
                }
            }
        }
    }

}
