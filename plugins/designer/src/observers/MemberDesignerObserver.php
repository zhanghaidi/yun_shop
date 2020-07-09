<?php
namespace Yunshop\Designer\observers;

use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;
use Yunshop\Designer\models\MemberDesigner;

class MemberDesignerObserver extends BaseObserver
{
    public function saved(Model $model)
    {
        if ($model->is_default == 1) {
            $this->updateHomePage($model);
        }
    }


    private function updateHomePage(Model $model)
    {
        $page_type = explode(',', $model->page_type);

        foreach ($page_type as $key => $value) {

            $designerModel = MemberDesigner::uniacid()
                            ->whereRaw('FIND_IN_SET(?,page_type)', [$value])
                            ->where('id', '<>', $model->id)
                            ->where('is_default',1)
                            ->first();

            if ($designerModel) {
                MemberDesigner::whereId($designerModel->id)->update(['is_default' => 0]);
            }
            unset($designerModel);
        }
    }

}
