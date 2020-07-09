<?php

namespace app\backend\modules\goods\observers;

use Illuminate\Database\Eloquent\Model;


/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午11:24
 */
class PluginArticleObserver extends \app\common\observers\BaseObserver
{

    public function deleted(Model $model)
    {
        $this->pluginObserver('observer.plugin.article', $model, 'deleted');
    }


}