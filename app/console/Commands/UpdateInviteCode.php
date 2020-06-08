<?php

namespace app\Console\Commands;


use app\backend\modules\member\models\Member;
use app\common\models\AccountWechats;
use app\frontend\modules\member\models\MemberUniqueModel;
use ClassesWithParents\D;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateInviteCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:invite_code ';

    /**
     * The console command description.
     *
     * @var string
     */

    //重构数据库
    protected $description = '修改邀请码';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected $uniacid = 2;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \YunShop::app()->uniacid = 2;
        $member = \app\common\models\MemberShopInfo::uniacid()
            ->select('member_id','invite_code')
            ->whereRaw('LENGTH(invite_code)<8')
            ->get()->toArray();

        if(empty($member)){
            \Log::info('是否进来');
            return '';
        }else{
            foreach ($member as $key => $value) {
                $invite_code = self::getInviteCode();
                if($invite_code){
                    \Log::info('会员',$value['member_id']);
                    \Log::info('邀请码',$invite_code);
                    \app\common\models\MemberShopInfo::updateInviteCode($value['member_id'], $invite_code);
                }
            }
        }
    }

    public static function getInviteCode()
    {
        $invite_code = self::generateInviteCode();
        if (self::chkInviteCode($invite_code)) {
            return $invite_code;
        } else {
            while (true) {
                self::getInviteCode();
            }
        }
    }

    /**
     * 生成邀请码
     *
     * @return string
     */
    public static function generateInviteCode()
    {
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $str[rand(0, 25)]
            . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5)
            . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));
        $code = '';

        for ($f = 0; $f < 8; $f++) {
            $a = md5($rand, true);
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV';
            $g = ord($a[$f]);
            $code .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F];
        };

        return $code;
    }

    /**
     * 验证邀请码
     *
     * @param $code
     */
    public static function chkInviteCode($code)
    {
        if (!\app\common\models\MemberShopInfo::chkInviteCode($code)) {
            return true;
        }

        return false;
    }





}
