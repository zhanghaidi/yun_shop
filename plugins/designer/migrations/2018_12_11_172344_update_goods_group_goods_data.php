<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Yunshop\Designer\models\GoodsGroupGoods;

class UpdateGoodsGroupGoodsData extends Migration
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

        if (!Schema::hasTable($this->DesignerTable)) {
            echo $this->DesignerTable." 不存在 跳过\n";
            return;
        }

        if (!Schema::hasTable($this->GoodsGroupTable)) {
            echo $this->GoodsGroupTable." 不存在 跳过\n";
            return;
        }

        $list = \Illuminate\Support\Facades\DB::table($this->DesignerTable)->get();
        $lists = collect($list);

        if($list) {
            foreach ($list as $v) {//循环所有的门店装修页面

                $datas = json_decode(htmlspecialchars_decode($v['datas']), true);
                $data = collect($datas);
                $count =  0;
                foreach ($data as $item){//循环商品组里的商品
                    ++$count;
                    if ($item['temp'] == 'goods' ||  $item['temp'] == 'flashsale'){//判断是否是商品组

                        if($data->count() == $count){//给商品组最后一个加上标识符
                            $data['Identification'] = 1;
                        }else{
                            $data['Identification'] = 0;
                        }
                        foreach ($item['data'] as $items){
                            \Illuminate\Support\Facades\DB::table('yz_goods_group_goods')->insert([
                               'group_goods_id' => $items['id'],
                               'uniacid' => $v['uniacid'],
                               'group_id' => $item['id'],
                               'goods_id' => $items['goodid'],
                               'goods' => serialize($items),
                               'group_type' => $v['page_type'],
                               'Identification' => $data['Identification'],
                               'temp' => $item['temp']
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
