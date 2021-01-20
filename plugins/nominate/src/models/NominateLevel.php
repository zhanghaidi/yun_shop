<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/15
 * Time: 4:14 PM
 */

namespace Yunshop\Nominate\models;


use app\common\models\BaseModel;

class NominateLevel extends BaseModel
{
    public $table = 'yz_nominate_level';
    public $timestamps = true;
    protected $guarded = [''];

    protected $casts = [
        'task' => 'json'
    ];

    public static function getModelByLevelId($levelId)
    {
        return self::select()->byLevelId($levelId);
    }

    public static function store($data)
    {
        $model = self::getModelByLevelId($data['level_id'])->first();
        if ($model) {
            self::edit($model, $data);
        } else {
            self::add($data);
        }
    }

    private static function add($data)
    {
        $model = new self();
        $model->fill($data);
        $model->save();
    }

    private static function edit($model, $data)
    {
        $model->fill($data);
        $model->save();
    }

    public function scopeByLevelId($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }
}