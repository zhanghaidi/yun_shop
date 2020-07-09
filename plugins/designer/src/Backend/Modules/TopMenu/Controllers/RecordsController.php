<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/22
 * Time: 上午10:56
 */

namespace Yunshop\Designer\Backend\Modules\TopMenu\Controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Designer\Backend\Models\TopMenuModel;

class RecordsController extends BaseController
{
    /**
     * @var string
     */
    private $page;

    /**
     * @var array
     */
    private $search;

    /**
     * @var TopMenuModel
     */
    protected $topMenuModel;


    public function preAction()
    {
        parent::preAction();
        $this->search = $this->getSearch();
        $this->topMenuModel = $this->getTopMenuModels();
        $this->page = $this->getPage();
    }

    public function index()
    {
        $data = $this->getResultData();

        return view('Yunshop\Designer::topMenu.records', $data);
    }

    /**
     * @return array
     */
    private function getResultData()
    {
        return [
            'pageList'  => $this->topMenuModel,
            'page'      => $this->page,
            'search'    => $this->search
        ];
    }

    /**
     * @return TopMenuModel
     */
    private function getTopMenuModels()
    {
        $topMenuModel = new TopMenuModel();

        if ($this->search) {
            $topMenuModel = $topMenuModel->search($this->search);
        }
        return $topMenuModel->orderBy('created_at','desc')->paginate();
    }

    /**
     * @return string
     */
    private function getPage()
    {
        return PaginationHelper::show($this->topMenuModel->total(),$this->topMenuModel->currentPage(),$this->topMenuModel->perPage());
    }

    /**
     * @return array
     */
    private function getSearch()
    {
        return \YunShop::request()->search;
    }

}
