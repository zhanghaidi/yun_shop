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

class Room extends BaseModel
{
    public $table = "yz_appletslive_room";

    public $timestamps = false;

    public static function setDisplayStatus($room)
    {
        if (!isset($room['is_display'])) {
            $room['is_display'] = 1;
        }
        if (!isset($room['is_share'])) {
            $room['is_share'] = 1;
        }
        if ($room['is_display'] == 1) {
            if ($room['is_share'] == 1) {
                return 1;
            } else {
                return 2;
            }
        } else {
            if ($room['is_share'] == 1) {
                return 3;
            } else {
                return 4;
            }
        }
    }

    public static function getIsShareAttribute($room)
    {
        if (!isset($room['display_type'])) {
            $room['display_type'] = 1;
        }

        if ($room['display_type'] == 1 || $room['display_type'] == 3) {
            return 1;
        } else {
            return 2;
        }
    }

    public static function getIsDisplayAttribute($room)
    {
        if (!isset($room['display_type'])) {
            $room['display_type'] = 1;
        }

        if ($room['display_type'] == 1 || $room['display_type'] == 2) {
            return 1;
        } else {
            return 2;
        }
    }
}
