<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/24
 * Time: 下午4:20
 */

namespace Yunshop\Mryt\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\models\Goods;
use Yunshop\Mryt\models\MrytLevelModel;
use Yunshop\Mryt\models\MrytLevelUpgradeModel;
use Yunshop\Mryt\services\CommonService;

class LevelController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $set = CommonService::getSet();
        $list = MrytLevelModel::getList()
            ->paginate($this->pageSize)
            ->toArray();


        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        $sort_url = 'plugin.mryt.admin.level.displayorder';
        $delete_msg = '确定要删除该等级吗';

        return view('Yunshop\Mryt::admin.level', [
            'list' => $list,
            'set' => $set,
            'pager' => $pager,
            'total' => $list['total'],
            'sort_url' => $sort_url,
            'delete_msg' => $delete_msg,
        ])->render();
    }

    /**
     * @return mixed|string
     * @throws \Throwable
     */
    public function add()
    {
        $set = CommonService::getSet();
        if (\Request::getMethod() == 'POST') {
            $data = \YunShop::request()->set;
            $data['uniacid'] = \YunShop::app()->uniacid;
            $data['current_md'] = '0';

            $mryt_level_model = new MrytLevelModel();

            $mryt_level_model->setRawAttributes($data);
            $validator = $mryt_level_model->validator($mryt_level_model->getAttributes());

            //验证表单
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
//                $exists = MrytLevelModel::uniacid()->where('level_weight',$mryt_level_model->level_weight)->exists();
//                if($exists) {
//                    $this->error('存在相同的等级权重');
//                } else {
                    if ($mryt_level_model->save()) {
                        $upgrade_type  = \YunShop::request()->upgrade_type;
                        $upgrade_value = \YunShop::request()->upgrade_value;

                        $upgrade = [$upgrade_type, $upgrade_value];

                        $upgrade_modle = new MrytLevelUpgradeModel();
                        $upgrade_modle->level_id = $mryt_level_model->id;
                        $upgrade_modle->uniacid  = \YunShop::app()->uniacid;
                        $upgrade_modle->parase  = serialize($upgrade);

                        $upgrade_modle->save();

                        return $this->message('等级操作成功', yzWebUrl('plugin.mryt.admin.level'), 'success');
                    } else {
                        $this->error('等级操作失败');
                    }
//                }
            }
        }

        $level_list = MrytLevelModel::getList()->get();
        return view('Yunshop\Mryt::admin.level_add', [
            'level_list' => $level_list,
            'set' => $set,
        ])->render();
    }

    /**
     * @return mixed|string
     * @throws \Throwable
     */
    public function edit()
    {
        $set = CommonService::getSet();

        $id = \YunShop::request()->id ? intval(\YunShop::request()->id) : 0;

        if ($id == 0 || !is_int($id)) {
            return $this->message('参数错误', '', 'error');
        }

        $level_model = MrytLevelModel::getLevelById($id);
        $upgrade_modle = MrytLevelUpgradeModel::getUpgradeByLevelId($id);
        $level_list    = MrytLevelModel::getList()->get();

        $parase = unserialize($upgrade_modle->parase);

        $upgrade_data = Goods::getGoodsById($parase[1]['goods']);

        if (\Request::getMethod() == 'POST') {
            $data = \YunShop::request()->set;

            $level_model->setRawAttributes($data);
            $mryt_level_validator = $level_model->validator($level_model->getAttributes());

            if ($mryt_level_validator->fails()) {
                $this->error($mryt_level_validator->messages());
            } else {
                if ($level_model->save()) {
                    //升级条件
                    if ($upgrade_modle) {
                        $upgrade_type  = \YunShop::request()->upgrade_type;
                        $upgrade_value = \YunShop::request()->upgrade_value;

                        $upgrade = [$upgrade_type, $upgrade_value];

                        $upgrade_modle->parase    = serialize($upgrade);

                        $upgrade_modle->save();
                    }

                    return $this->message('等级操作成功', yzWebUrl('plugin.mryt.admin.level'), 'success');
                } else {
                    $this->error('等级操作失败');
                }
            }
        }

        return view('Yunshop\Mryt::admin.level_add', [
            'level' => $level_model,
            'upgrade_type' => $parase[0],
            'upgrade_value' => $parase[1],
            'upgrade_data' => $upgrade_data,
            'level_list'   => $level_list,
            'level_set'    => 1,
            'set' => $set
        ])->render();
    }

    public  function deleted()
    {
        $id = \YunShop::request()->id ? intval(\YunShop::request()->id) : 0;

        if ($id == 0 || !is_int($id)) {
            return $this->message('参数错误', '', 'error');
        }

        $level = MrytLevelModel::getLevelById($id);


        if (empty($level)) {
            return $this->message('等级不存在', '', 'error');
        }

        if (MrytLevelModel::deletedLevel($id)) {
            MrytLevelUpgradeModel::deleteUpgradeByLevelId($id);
            return $this->message('等级删除成功', yzWebUrl('plugin.mryt.admin.level.index'));
        } else {
            return $this->message('等级删除失败', yzWebUrl('plugin.mryt.admin.level.index'), 'error');
        }
    }

    public function displayorder()
    {
        $displayOrders = \YunShop::request()->display_order;
        $count = array_count_values($displayOrders);
        foreach ($count as $key => $sum) {
            if (!$key) {
                return $this->message('等级权重不能为0', yzWebUrl('plugin.mryt.admin.level.index'),'error');
            }
            if ($sum > 1) {
                return $this->message('等级权重不能相同', yzWebUrl('plugin.mryt.admin.level.index'),'error');
            }
        }

        foreach($displayOrders as $id => $displayOrder){
            $level_model = MrytLevelModel::find($id);
            $level_model->level_weight = $displayOrder ?: 0;

            $level_model->save();
        }
        return $this->message('等级权重排序成功', yzWebUrl('plugin.mryt.admin.level.index'));
    }

    public function validator()
    {
        $data = \YunShop::request()->data;
        $id = \YunShop::request()->id;
        if ($id) {
            $exists = MrytLevelModel::uniacid()->where('id','<>',$id)->where('level_weight',$data)->exists();
        } else {
            $exists = MrytLevelModel::uniacid()->where('level_weight',$data)->exists();
        }

        echo json_encode([
            'data' => $exists,
        ]);
    }
}