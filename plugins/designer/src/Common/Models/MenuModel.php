<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/11
 * Time: 上午9:37
 */

namespace Yunshop\Designer\Common\Models;


use app\common\models\BaseModel;
use Yunshop\Designer\Common\Observers\MenuObserver;

/**
 * @property $menus
 * @property $params
 * @property $ingress
 * @property $is_default
 * @property $menu_name
 *
 * Class MenuModel
 * @package Yunshop\Designer\Common\Models
 */
class MenuModel extends BaseModel
{
    const INGRESS_WE_CHAT_APPLET = 'weChatApplet';


    /**
     * @var string
     */
    protected $table = 'yz_designer_menu';

    /**
     * @var array
     */
    protected $guarded = [''];

    public static function boot()
    {
        parent::boot();
        self::observe(MenuObserver::class);
    }


    public function scopeSearch($query, array $searchParams)
    {
        if ($searchParams['menu_name']) {
            $query->where('menu_name', 'like', '%' . $searchParams['menu_name'] . '%');
        }
        if ($searchParams['ingress']) {
            $query->where('ingress', $searchParams['ingress']);
        } else {
            $query->where('ingress', '');
        }
    }
}
