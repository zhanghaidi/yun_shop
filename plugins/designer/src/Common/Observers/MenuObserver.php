<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-10
 * Time: 15:09
 */

namespace Yunshop\Designer\Common\Observers;


use app\common\helpers\Cache;
use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;
use Yunshop\Designer\Common\Models\MenuModel;

class MenuObserver extends BaseObserver
{
    public function updating(Model $model)
    {
        /**
         * 如果是默认，更新同终端其他默认
         *
         * @var MenuModel $model
         */
        if ($model->is_default == 1) {
            MenuModel::uniacid()->where('ingress', $model->ingress)->update(['is_default' => '0']);
        }
    }

    public function saved(Model $model)
    {
        Cache::flush();
    }
}
