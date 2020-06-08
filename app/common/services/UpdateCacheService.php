<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-05-06
 * Time: 14:33
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

namespace app\common\services;


class UpdateCacheService
{
    public function handle()
    {
        \Artisan::call('config:cache');
        \Cache::flush();
    }
}