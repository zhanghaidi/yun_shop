<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/31 0031
 * Time: 下午 2:56
 */

namespace Yunshop\HelpCenter\api;

use app\common\components\ApiController;
// use app\frontend\modules\member\controllers\ServiceController;
use Yunshop\HelpCenter\models\HelpCenterAddModel;

class ContentController extends ApiController
{
    protected $publicAction = ['info'];

    public function info()
    {
        $set_data = HelpCenterAddModel::select('title', 'content')->uniacid()->orderBy('sort')->get()->toarray();

        foreach ($set_data as $key => &$value) {
            $value['content'] = html_entity_decode($value['content']);
        }
        return $this->successJson('ok', $set_data);

        // $customer_service = (new ServiceController())->index();
        // return $this->successJson('ok', ['set_data' => $set_data,'customer_service'=>$customer_service]);
    }
}
