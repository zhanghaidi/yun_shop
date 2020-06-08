<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-05-06
 * Time: 14:39
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


use app\common\services\UpdateCacheService;
use Illuminate\Foundation\Bus\DispatchesJobs;

class UpdateCache
{
    use DispatchesJobs;

    public function handle()
    {
        (new UpdateCacheService())->handle();
    }

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Update-cache', '0 0 1 * *', function() {
                $this->handle();
                return;
            });
        });
    }
}