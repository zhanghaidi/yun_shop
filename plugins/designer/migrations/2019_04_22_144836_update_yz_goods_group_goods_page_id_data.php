<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Yunshop\Designer\models\GoodsGroupGoods;

class UpdateYzGoodsGroupGoodsPageIdData extends Migration
{

    protected $GoodsGroupTable = 'yz_goods_group_goods';
    protected $DesignerTable = 'yz_designer';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list = \Illuminate\Support\Facades\DB::table($this->DesignerTable)->get();

        if($list) {
            foreach ($list as $v) {//循环所有的门店装修页面
                $datas = json_decode(htmlspecialchars_decode($v['datas']), true);
                $data = collect($datas);
                foreach ($data as $item){//循环商品组里的商品
                    if ($item['temp'] == 'goods' ||  $item['temp'] == 'flashsale'){//判断是否是商品组
                        $group_goods = \Illuminate\Support\Facades\DB::table($this->GoodsGroupTable)->where(['group_id' => $item['id'] , 'uniacid' => $v['uniacid']])->get();
                        foreach ($group_goods as $items){
                            \Illuminate\Support\Facades\DB::table('yz_goods_group_goods')->where('id',$items['id'])->update([
                                'page_id' => $v['id']
                            ]);

                        }
                    }
                }
            }
        }else{
            echo "DesignerTable没有数据\n";
        }
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
