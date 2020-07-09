<?php


namespace Yunshop\Commission\admin;

use app\common\facades\Setting;
use Illuminate\Http\Request;
use app\common\components\BaseController;
use Yunshop\Commission\models\AgentLevel;
use app\common\helpers\PaginationHelper;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\services\AgentLevelService;
use app\common\helpers\Url;

class LevelController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $set = Setting::get('plugin.commission');
        $pageSize = 10;
        $list = AgentLevel::getLevels()->orderBy('level', 'desc')->paginate($pageSize);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('Yunshop\Commission::admin.level_list', [
            'set' => $set,
            'list' => AgentLevelService::setUpgrades($list->items()),
            'pager' => $pager
        ])->render();
    }

    public function add()
    {
        $set = Setting::get('plugin.commission');
        $LevelModel = new AgentLevel();

        $requestLevel = \YunShop::request()->level;

        if ($requestLevel) {
            //将数据赋值到model
            $LevelModel->setRawAttributes($requestLevel);
            //其他字段赋值
            $LevelModel->uniacid = \YunShop::app()->uniacid;

            $upgrades = AgentLevelService::addUpgrades(\YunShop::request()->upgrade_type, \YunShop::request()->upgrade_value);
            $LevelModel->upgraded = $upgrades;

            if($requestLevel['level']==0){
               return $this->message('等级权重不可为0为空');
            }
            $validator = $LevelModel->validator();
            if ($validator->fails()) {//检测失败

                $this->error($validator->messages());
            } else {
                //数据保存
                if ($LevelModel->save()) {
                    //显示信息并跳转
                    return $this->message('分销商等级创建成功', Url::absoluteWeb('plugin.commission.admin.level.index'));
                } else {
                    $this->error('分销商等级创建失败');
                }
            }
        }

        $upgrade_data = AgentLevelService::setUpgradedata();
        $upgrade_config = AgentLevelService::upgradeConfig();

        return view('Yunshop\Commission::admin.level_info', [
            'set' => $set,
            'upgrade_data' => $upgrade_data,
            'upgrade_config' => $upgrade_config,
            'levelModel' => $LevelModel
        ])->render();
    }

    public function edit()
    {
        $set = Setting::get('plugin.commission');
        $id = intval(\YunShop::request()->id);
        $LevelModel = AgentLevel::getAgentLevelByid($id);
        if (!$LevelModel) {
            return $this->message('无此记录或已被删除', '', 'error');
        }

        $requestLevel = \YunShop::request()->level;

        if ($requestLevel) {
            //将数据赋值到model
            $LevelModel->fill($requestLevel);
            $upgrades = AgentLevelService::addUpgrades(\YunShop::request()->upgrade_type, \YunShop::request()->upgrade_value);
            $LevelModel->upgraded = $upgrades;
            //字段检测
            if($requestLevel['level']==0){
                return $this->message('等级权重不可为0');
            }
            $validator = $LevelModel->validator();
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($LevelModel->save()) {
                    //显示信息并跳转
                    return $this->message('分销商等级编辑成功', Url::absoluteWeb('plugin.commission.admin.level.index'));
                } else {
                    $this->error('分销商等级编辑失败');
                }
            }
        }
        $upgrade_data = AgentLevelService::setUpgradedata($LevelModel->upgraded);
        $upgrade_config = AgentLevelService::upgradeConfig();

        return view('Yunshop\Commission::admin.level_info', [
            'set' => $set,
            'upgrade_data' => $upgrade_data,
            'upgrade_config' => $upgrade_config,
            'levelModel' => $LevelModel
        ])->render();
    }

    public function deleted()
    {
        $id = intval(\YunShop::request()->id);
        $LevelModel = AgentLevel::getAgentLevelByid($id);
        if (!$LevelModel) {
            return $this->message('无此记录或已被删除', '', 'error');
        }
        $result = AgentLevel::daletedLevel($id);
        if ($result) {
            return $this->message('删除分销商等级成功', Url::absoluteWeb('plugin.commission.admin.level.index'));
        } else {
            return $this->message('删除销商等级失败', '', 'error');
        }
    }
}
