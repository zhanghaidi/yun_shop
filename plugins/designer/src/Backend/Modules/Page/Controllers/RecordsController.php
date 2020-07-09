<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/9/17
 * Time: 上午10:13
 */

namespace Yunshop\Designer\Backend\Modules\Page\Controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Designer\Backend\Models\PageModel;

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
     * @var PageModel
     */
    protected $pageModel;


    public function __construct()
    {
        parent::__construct();

        $this->search = $this->getSearch();
        $this->pageModel = $this->getMenuModels();
        $this->page = $this->getPage();
    }

    public function index()
    {
        $data = $this->getResultData();


        return view('Yunshop\Designer::page.records', $data);
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
            'pageList'  => $this->pageModel,
            'page'      => $this->page,
            'search'    => $this->search
        ];
    }

    /**
     * @return PageModel
     */
    private function getMenuModels()
    {
        $pageModel = new PageModel();

        if ($this->search) {
            $pageModel = $pageModel->search($this->search);
        }
        return $pageModel->orderBy('updated_at','desc')->paginate();
    }

    /**
     * @return string
     */
    private function getPage()
    {
        return PaginationHelper::show($this->pageModel->total(),$this->pageModel->currentPage(),$this->pageModel->perPage());
    }

    /**
     * @return array
     */
    private function getSearch()
    {
        return \YunShop::request()->search;
    }
}
