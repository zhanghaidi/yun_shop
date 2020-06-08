<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use app\common\models\UniAccount;


class UpdateYzMessageTemplateData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $template_short = ['OPENTM405485000','OPENTM202425107','OPENTM409281647','OPENTM405485000'];

        $template = ['UpqqZLZlbN6FIWTVOmjcAQTQ0C2Prev92AN5ILK4wKI','1lvZE23BfsiPCxtYXn6bdHNbNuwmpKjUVINJQ_fYN5E','0HY2OwvCkQL5a9AZA1lhjOFU8VE12WBAJtZiOIHcVzI','UpqqZLZlbN6FIWTVOmjcAQTQ0C2Prev92AN5ILK4wKI'];

        $temp_all = [
            ['id'=>'UpqqZLZlbN6FIWTVOmjcAQTQ0C2Prev92AN5ILK4wKI','short'=>'OPENTM405485000'],
            ['id'=>'1lvZE23BfsiPCxtYXn6bdHNbNuwmpKjUVINJQ_fYN5E','short'=>'OPENTM202425107'],
            ['id'=>'0HY2OwvCkQL5a9AZA1lhjOFU8VE12WBAJtZiOIHcVzI','short'=>'OPENTM409281647'],
            ['id'=>'UpqqZLZlbN6FIWTVOmjcAQTQ0C2Prev92AN5ILK4wKI','short'=>'OPENTM405485000']
        ];

        $uniAccount = UniAccount::getEnable() ?: [];

        foreach ($uniAccount as $u) {

            \Setting::$uniqueAccountId = $u->uniacid;

            \YunShop::app()->uniacid = $u->uniacid;

            $default = \app\common\models\TemplateMessageDefault::uniacid()->whereIn("template_id_short",$template_short)->pluck('template_id_short');

            $default = empty($default) ? [] : $default->toArray();

            //添加默认模板信息
            $this->addDefault($template_short,$template,$default);

            //添加模板信息
            $this->addTemplate($template_short,$template,$temp_all);
        }
    }

    public function addDefault($template_short,$template,$default)
    {
        $data = [];

        foreach ($template_short as $kk=>$vv) {
            if (!in_array($vv,$default)) {
                $data[$kk]['uniacid'] = YunShop::app()->uniacid;
                $data[$kk]['template_id'] = $template[$kk];
                $data[$kk]['template_id_short'] = $vv;
                $data[$kk]['created_at'] = time();
                $data[$kk]['updated_at'] = time();
            }
        }

        if (!empty($data)) {
            \app\common\models\TemplateMessageDefault::uniacid()->insert($data);
        }
    }

    public function addTemplate($short,$template,$temp_all)
    {
        $ids = ['income_withdraw','income_withdraw_check','income_withdraw_pay','member_withdraw'];

        $data = []; //文件中的模板

        //判断数据库中模板是否存在，并与文件中的template_id对比，有则返回，无则增加
        $templateData = \app\common\models\notice\MessageTemp::uniacid()->whereIn("notice_type",$ids)->get();

        $templateData = empty($templateData) ? [] : $templateData->toArray();

        $newTemp = \app\common\modules\template\Template::current()->getNoticeItems();

        foreach($newTemp as $key => $item) {
            foreach($ids as $kk=>$vv) {
                if ($key == $vv) {
                    $data[$vv] = $item;
                }
            }
        }

        if (empty($templateData)) {
            foreach ($data as $key=>$item) {
                $data[$key]['uniacid'] = \YunShop::app()->uniacid;
                $template_id = empty($this->getTemplateId($item['template_id_short'],$temp_all)) ? "" : $this->getTemplateId($item['template_id_short'],$temp_all);
                $data[$key]['template_id'] = $template_id;
                $data[$key]['is_default'] = 1;
                $data[$key]['data'] = json_encode($item['data']);
                $data[$key]['notice_type'] = $key;
                $data[$key]['created_at'] = time();
                $data[$key]['updated_at'] = time();
                unset($data[$key]['template_id_short']);
            }
        } else {
            foreach ($templateData as $kk=>$vv){
                if (!in_array($vv['template_id'],$template)) {
                    //先删除
                    \app\common\models\notice\MessageTemp::uniacid()->where("notice_type",$vv['notice_type'])->delete();

                    foreach ($data as $key=>$item) {

                        if ($key == $vv['notice_type']) {
                            $data[$key]['uniacid'] = \YunShop::app()->uniacid;
                            $template_id = empty($this->getTemplateId($item['template_id_short'],$temp_all)) ? "" : $this->getTemplateId($item['template_id_short'],$temp_all);
                            $data[$key]['template_id'] = $template_id;
                            $data[$key]['is_default'] = 1;
                            $data[$key]['data'] = json_encode($item['data']);
                            $data[$key]['notice_type'] = $key;
                            $data[$key]['created_at'] = time();
                            $data[$key]['updated_at'] = time();
                            unset($data[$key]['template_id_short']);
                        }

                    }

                } else {
                    unset($data[$vv['notice_type']]);
                }
            }
        }

        if (!empty($data)) {
            \app\common\models\notice\MessageTemp::insert($data);
        }
    }

    public function getTemplateId($value,$temp_all){

        foreach ($temp_all as $key=>$item) {
            if ($value == $item['short']) {
                return $item['id'];
            }
        }

        return "";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
