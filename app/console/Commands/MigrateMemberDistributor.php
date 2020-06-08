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
use Illuminate\Console\Command;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\admin\model\MemberShopInfo;

class MigrateMemberDistributor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:member_distributor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将会员更新成为推广员';

    /**
     * Execution entrance
     */
    public function handle()
    {

        \YunShop::app()->uniacid = 2;

        //查询没有成为分销商的会员总数
        $values = MemberShopInfo::getAgentMembers();

        $values = empty($values) ? [] : $values->toArray();

        $count = count($values);

        if ($count <= 0) {
            return;
        }

        $barOne = $this->output->createProgressBar($count);
        foreach ($values as $value) {
            $this->memberDistributor($value);
            $barOne->advance();
        }
        $barOne->finish();
        $this->comment('member distributor data migration completed!');
    }

    public function memberDistributor($arrMember)
    {
        $arrAgent['uniacid'] = $arrMember['uniacid'];
        $arrAgent['parent_id'] = $arrMember['parent_id'];
        $arrAgent['member_id'] = $arrMember['member_id'];
        $arrAgent['parent'] = $arrMember['relation'];
        $arrAgent['created_at'] = $arrMember['agent_time'];
        $arrAgent['updated_at'] = $arrMember['agent_time'];
        $agent = new Agents();
        $agent->fill($arrAgent);
        $agent->save();
    }

}
