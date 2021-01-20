<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/1
 * Time: 下午2:53
 */

namespace Yunshop\Mryt\store\models;


use app\common\models\BaseModel;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class StoreCategory extends BaseModel
{
    public $table = 'yz_store_category';
    public $timestamps = true;
    protected $guarded = [''];
    protected $appends = ['open_name'];

    const PAGE_SIZE = 20;

    public static function getList()
    {
        return self::select();
    }

    public static function getCategoryById($id)
    {
        return self::select()->byId($id);
    }

    public function scopeById($query, $id)
    {
        return $query->where('id', $id);
    }

    public function scopeByOpen($query, $is_open)
    {
        return $query->where('is_open', $is_open);
    }

    public function getOpenNameAttribute()
    {
        if ($this->is_open == 0) {
            $status_name = '关闭';
        } else {
            $status_name = '开启';
        }
        return $status_name;
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }

    public  function atributeNames() {
        return [
            'sort'  => '排序',
            'thumb' => '分类图片'
        ];
    }

    public  function rules()
    {
        return [
            'sort'  => 'integer',
            'thumb' => 'required'
        ];
    }

    public static function insertDefaultCategory()
    {
        $categorys = self::getList()->get();
        if ($categorys->isEmpty()) {
            self::create([
                'uniacid' => \YunShop::app()->uniacid,
                'sort'      => 1,
                'name'      => '默认分类',
                'thumb'     => '',
                'is_open'   => 1
            ]);
        }
    }
}