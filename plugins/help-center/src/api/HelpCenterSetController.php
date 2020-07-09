<?php
namespace Yunshop\HelpCenter\api;
use app\common\components\ApiController;
use app\common\facades\Setting;
use Yunshop\HelpCenter\services\HelpCenterService;
use Yunshop\HelpCenter\models\HelpCenterSetModel;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/31 0031
 * Time: 下午 1:39
 */
class HelpCenterSetController extends ApiController
{
    public $_set;
    public $_helpCenterService;
    public $_pluginName;

    public function __construct()
    {
        parent::__construct();

        $this->_helpCenterService = new HelpCenterService();
        $this->_pluginName = $this->_helpCenterService->get('plugin_name');
        $this->_set = Setting::get('plugin.help_center');
    }

    public function index()
    {

    }

    public function getData()
    {
        $set_data = HelpCenterSetModel::get()->toarray();

        $model = new HelpCenterSetController;
        dd($set_data);

        return $this->successJson('ok',['set_data' => $set_data, 'state' => 'true']);
    }
}