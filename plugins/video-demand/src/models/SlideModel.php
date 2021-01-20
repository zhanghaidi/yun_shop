<?php

namespace Yunshop\VideoDemand\models;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/06
 * Time: 下午1:54
 */
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Yunshop\VideoDemand\services\SlideService;

class SlideModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_video_slide';
    public $timestamps = true;
    protected $guarded = [''];

    public $StatusService;
    protected $appends = ['status_name'];

    /**
     * @return mixed
     */
    public static function getSlide()
    {
        return self::uniacid()
            ->orderBy('display_order', 'asc');
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getSlideByid($id)
    {
        return self::find($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function deletedSlide($id)
    {
        return self::where('id', $id)
            ->delete();
    }

    /**
     * @return string
     */
    public function getStatusNameAttribute()
    {
        if (!isset($this->StatusService)) {
            $this->StatusService = SlideService::createStatusService($this);
        }
        return $this->StatusService;
    }

    /**
     *  定义字段名
     * 可使
     * @return array
     */
    public function atributeNames()
    {
        return [
            'slide_name' => '幻灯片名称',
            'display_order' => '排序',
            'thumb' => '幻灯片图片',
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'slide_name' => 'required',
            'display_order' => 'required',
            'thumb' => 'required',
        ];
    }

}