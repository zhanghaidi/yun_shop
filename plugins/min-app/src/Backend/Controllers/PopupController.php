<?php

namespace Yunshop\MinApp\Backend\Controllers;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\MinApp\Common\Models\Popup;
use Yunshop\MinApp\Common\Models\PopupPositon;
use Illuminate\Support\Facades\DB;
use Yunshop\MinApp\Common\Services\PopupService;

class PopupController extends BaseController
{
    const PAGE_SIZE = 20;

    protected $popup_model;
    protected $pos_model;

    public function index()
    {
        $search = $this->getPostSearch();
        $popup = new Popup();

        if ($search) {
            //dd($search);
            $popup = $popup->search($search);
        }

        $popupList = $popup->orderBy('sort','desc')->orderBy('created_at','desc')->paginate(static::PAGE_SIZE);
        $page = PaginationHelper::show($popupList->total(),$popupList->currentPage(),$popupList->perPage());

        return view('Yunshop\MinApp::popup.index',[
            'popupList'      => $popupList,
            'page'           => $page,
            'search'         => $search,
            'position'       => PopupPositon::uniacid()->get(),
        ])->render();
    }

    public function edit()
    {

        $pop_id = intval(request()->id);

        if($pop_id){
            $this->verifyPopup($pop_id);
        }else{
            $this->popup_model = new Popup();
        }

        if (request()->popup) {
            $this->popup_model->fill(Popup::handleArray(request()->popup, $pop_id));
            $validator = $this->popup_model->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            }else{
                $ret = $this->popup_model->save();
                if (!$ret) {
                    return $this->message('保存弹窗失败', Url::absoluteWeb('plugin.min-app.Backend.Controllers.popup.edit',['id'=>$pop_id]), 'error');
                }
                (new PopupService())->logPopup($this->popup_model, $pop_id ? 'update' : 'create');
                return $this->message('保存弹窗成功', Url::absoluteWeb('plugin.min-app.Backend.Controllers.popup.index'));
            }
        }

        return view('Yunshop\MinApp::popup.edit',[
            'popup' => $this->popup_model,
            'position' => PopupPositon::uniacid()->get(),
            'show_rule' => Popup::getShowRule(),
        ]);
    }

    public function position()
    {
        $search = $this->getPostSearch();
        $position = new PopupPositon();

        if ($search) {
            //dd($search);
            $position = $position->search($search);
        }

        $positionList = $position->orderBy('created_at','desc')->paginate(static::PAGE_SIZE);
        $page = PaginationHelper::show($positionList->total(),$positionList->currentPage(),$positionList->perPage());

        return view('Yunshop\MinApp::popup.position',[
            'positionList'      => $positionList,
            'page'              => $page,
            'search'            => $search,
            'weappAccount'      => array_column(DB::table('account_wxapp')->select('uniacid','name')->orderBy('uniacid','desc')->get()->toArray(),'name','uniacid'),
            'typeList'          => PopupPositon::getPosType(),
        ])->render();
    }

    public function positionEdit(){

        $pos_id = intval(request()->id);

        if($pos_id){
            $this->verifyParam($pos_id);
        }else{
            $this->pos_model = new PopupPositon();
        }

        if (request()->position) {
            $this->pos_model->fill(PopupPositon::handleArray(request()->position, $pos_id));
            $validator = $this->pos_model->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            }else {
                $ret = $this->pos_model->save();
                if (!$ret) {
                    return $this->message('保存弹窗位置失败', Url::absoluteWeb('plugin.min-app.Backend.Controllers.popup.position-edit',['id'=>$pos_id]), 'error');
                }
                (new PopupService())->logPopupPosition($this->pos_model, $pos_id ? 'update' : 'create');
                return $this->message('保存弹窗位置成功', Url::absoluteWeb('plugin.min-app.Backend.Controllers.popup.position'));
            }
        }

        return view('Yunshop\MinApp::popup.position-edit',[
            'position' => $this->pos_model,
            'pos_type' => PopupPositon::getPosType(),
        ]);
    }

    private function getPostSearch()
    {
        return \YunShop::request()->search;
    }

    private function verifyPopup($pop_id)
    {
        if (!$pop_id) {
            return $this->message('参数错误', Url::absoluteWeb('plugin.min-app.admin.popup.index'), 'error');
        }
        $popup_model = Popup::getPopupById($pop_id)->first();
        if (!$popup_model) {
            return $this->message('未找到数据', Url::absoluteWeb('plugin.min-app.admin.popup.index'), 'error');
        }
        $this->popup_model = $popup_model;
    }

    private function verifyParam($pos_id)
    {
        if (!$pos_id) {
            return $this->message('参数错误', Url::absoluteWeb('plugin.min-app.admin.popup.position'), 'error');
        }
        $pos_model = PopupPositon::getpositionById($pos_id)->first();
        if (!$pos_model) {
            return $this->message('未找到数据', Url::absoluteWeb('plugin.min-app.admin.popup.position'), 'error');
        }
        $this->pos_model = $pos_model;
    }

}
