<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-05
 * Time: 14:23
 */

namespace Yunshop\Designer\Backend\Modules\BottomMenu\Controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Designer\Backend\Models\MenuModel;

class RecordsController extends BaseController
{
    /**
     * @var string
     */
    protected $ingress = '';

    /**a
     * @var string
     */
    protected $storeUrl = 'plugin.designer.Backend.Modules.BottomMenu.Controllers.store.index';

    /**
     * @var string
     */
    protected $defaultUrl = 'plugin.designer.Backend.Modules.BottomMenu.Controllers.default.index';

    /**
     * @var string
     */
    protected $destroyUrl = 'plugin.designer.Backend.Modules.BottomMenu.Controllers.destroy.index';

    /**
     * @var MenuModel
     */
    protected $pageModel;


    public function __construct()
    {
        parent::__construct();

        $this->pageModel = $this->getMenuModels();
    }

    public function index()
    {
        return view('Yunshop\Designer::bottomMenu.records', $this->getResultData());
    }

    public function shareIndex()
    {
        $data = $this->getMenuModels();
        return $data->toArray();
    }

    /**
     * @return array
     */
    private function getResultData()
    {
        return [
            'pageList'   => $this->pageModel,
            'page'       => $this->page(),
            'search'     => $this->searchParams(),
            'ingress'    => $this->ingress,
            'storeUrl'   => yzWebUrl($this->storeUrl),
            'defaultUrl' => yzWebUrl($this->defaultUrl),
            'destroyUrl' => yzWebUrl($this->destroyUrl)
        ];
    }

    /**
     * @return MenuModel
     */
    private function getMenuModels()
    {
        $pageModel = new MenuModel();

        $searchParams = $this->searchParams();
        if ($searchParams) {
            $pageModel = $pageModel->search($searchParams);
        }
        return $pageModel->orderBy('created_at', 'desc')->paginate();
    }

    /**
     * @return string
     */
    private function page()
    {
        return PaginationHelper::show($this->pageModel->total(), $this->pageModel->currentPage(), $this->pageModel->perPage());
    }

    /**
     * @return array
     */
    private function searchParams()
    {
        $searchParams = \YunShop::request()->search;

        !is_array($searchParams) && $searchParams = [];

        return array_merge($searchParams, ['ingress' => $this->ingress]);
    }
}
