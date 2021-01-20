<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/10/28
 * Time: 3:45 PM
 */

namespace Yunshop\Mryt\services;


use app\common\models\Income;

class IncomeService
{
    private $model;
    private $typeName;

    public function __construct($model, $type_name)
    {
        $this->model = $model;
        $this->typeName = $type_name;
    }

    public function handle()
    {
        $class = get_class($this->model);
        $income_data = [
            'uniacid'           => $this->model->uniacid,
            'member_id'         => $this->model->uid,
            'incometable_type'  => $class,
            'incometable_id'    => $this->model->id,
            'type_name'         => $this->typeName,
            'amount'            => $this->model->amount,
            'status'            => 0,
            'pay_status'        => 0,
            'detail'            => '',
            'create_month'      => date('Y-m', time())
        ];
        Income::create($income_data);
    }
}