<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-01-09
 * Time: 10:26
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

namespace app\common\listeners;


use app\common\services\ShopCollectService;
use Illuminate\Foundation\Bus\DispatchesJobs;

class PluginCollectListener
{
    use DispatchesJobs;

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('PluginCollect', '0 0 * * 0', function () {
                (new ShopCollectService())->handle();
                return;
            });
        });
    }

}