<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-03-16
 * Time: 16:59
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Supplier\common\models;


use app\common\models\BaseModel;

class InsuranceCompany extends BaseModel
{
    public $table = 'yz_insurance_company';
    public $guarded = [''];

    public static function search($search)
    {
        $model = self::uniacid();

        if (!empty($search['name'])) {
            $model->where('name', 'like', '%' . $search['name'] . '%');
        }

        return $model;
    }
}