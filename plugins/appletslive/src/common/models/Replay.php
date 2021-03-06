<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-02-28
 * Time: 12:45
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
namespace Yunshop\Appletslive\common\models;

use app\common\models\BaseModel;

class Replay extends BaseModel
{
    public $table = "yz_appletslive_replay";

    public $timestamps = false;

    /**
     * 获取关联直播间
     */
    public function liveroom()
    {
        return $this->belongsTo('Yunshop\Appletslive\common\models\LiveRoom', 'room_id');
    }
}
