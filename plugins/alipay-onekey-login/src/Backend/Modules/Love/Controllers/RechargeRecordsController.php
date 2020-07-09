<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/24
 * Time: 4:40 PM
 */

namespace Yunshop\Love\Backend\Modules\Love\Controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Love\Backend\Modules\Love\Models\LoveRechargeRecords;

class RechargeRecordsController extends BaseController
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
     * @var LoveRechargeRecords
     */
    protected $rechargeModel;


    public function preAction()
    {
        parent::preAction();

        $this->search = $this->getSearch();
        $this->rechargeModel = $this->getMenuModels();
        $this->page = $this->getPage();
    }

    public function index()
    {
        $data = $this->getResultData();

        //dd($this->search);
        return view('Yunshop\Love::Backend.Love.rechargeRecords', $data);
    }

    /**
     * @return array
     */
    private function getResultData()
    {
        return [
            'pageList'  => $this->rechargeModel,
            'page'      => $this->page,
            'search'    => $this->search
        ];
    }

    /**
     * @return LoveRechargeRecords
     */
    private function getMenuModels()
    {
        $rechargeModel = new LoveRechargeRecords();

        if ($this->search) {
            $rechargeModel = $rechargeModel->search($this->search)->searchMember($this->search);
        }
        return $rechargeModel->withMember()->orderBy('updated_at','desc')->paginate();
    }

    /**
     * @return string
     */
    private function getPage()
    {
        return PaginationHelper::show($this->rechargeModel->total(),$this->rechargeModel->currentPage(),$this->rechargeModel->perPage());
    }

    /**
     * @return array
     */
    private function getSearch()
    {
        return \YunShop::request()->search;
    }
}
