<?php

namespace Yunshop\Diyform\api;

use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\frontend\models\Member;
use Yunshop\Diyform\models\DiyformDataModel;
use Yunshop\Diyform\models\DiyformOrderContentModel;
use Yunshop\Diyform\models\DiyformOrderModel;
use Yunshop\Diyform\models\DiyformTypeModel;
use Yunshop\Diyform\models\DiyformTypeMemberDataModel;
use Illuminate\Support\Facades\DB;
use app\common\components\ApiController;
use app\common\exceptions\MemberNotLoginException;
use app\common\helpers\Client;
use Illuminate\Http\Request;

class DiyFormController extends BaseController
{

    // 上传图片接口
    public function upload()
    {
        $file = request()->file('file');
        if (!$file) {
            return $this->errorJson('请传入正确参数.');
        }
        if ($file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $ext = $file->getClientOriginalExtension();
            $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;

            \Storage::disk('image')->put($newOriginalName, file_get_contents($realPath));

            return $this->successJson('上传成功', [
                'img'    => \Storage::disk('image')->url($newOriginalName),
            ]);
        } else {
            return $this->errorJson('上传失败!');
        }
    }

    public function DiyFormUpload(Request $request)
    {
        // plugin.supplier.supplier.controllers.apply.supplier-apply.upload
        $attach = '';
        $path = $request->file('file')->storeAs('avatars', 'supplier_apply' . str_random(10));

        if (\YunShop::request()->attach) {
            $attach = \YunShop::request()->attach;
        }

        return $this->successJson('上传成功', [
            'img'    => request()->getSchemeAndHttpHost(). config('app.webPath') . '/storage/app/'.$path,
            'attach' => $attach
        ]);
    }

    // 查看一张表单时，将表单，表单数据查询返回给前端。
    public function getDiyFormTypeMemberData($request, $integrated = null,$formId = null)
    {
        if($formId){
            $form_id = $formId;
        }else{
            $form_id = (int) \YunShop::request()->form_id;
        }

        $memberId = Member::current()->uid;
        if(!$form_id){
            if(is_null($integrated)){
                return $this->errorJson('form_id 不存在',[]);
            }else{
                return show_json(0,'form_id 不存在');
            }
        }

        $diyformTypeModel = DiyformTypeModel::uniacid()
            ->with(['hasOneDiyformTypeMemberData' => function ($query) use ($memberId) {
                $query->where('uniacid', '=', \YunShop::app()->uniacid)
                    ->where('member_id','=',$memberId)
                    ->with('hasOneDiyformData');
            }])->find($form_id);
        if ($diyformTypeModel) {
            $diyformTypeModel->fields = iunserializer($diyformTypeModel->fields);
            $diyformTypeModel->thumb = yz_tomedia($diyformTypeModel->thumb);
            $diyformTypeModel->description = htmlspecialchars_decode($diyformTypeModel->description);
            $diyformTypeModel = $diyformTypeModel->toArray();
            if ($diyformTypeModel['has_one_diyform_type_member_data']) {
                if ($diyformTypeModel['has_one_diyform_type_member_data']['has_one_diyform_data']) {
                    foreach ($diyformTypeModel['fields'] as $key => &$field) {
                        foreach ($diyformTypeModel['has_one_diyform_type_member_data']['has_one_diyform_data']['form_data'] as $k => $item) {
                            if ($key == $k) {
                                $field['name'] = $field['tp_name'];
                                $field['is_image'] = $field['data_type'] == 5 ? 1 : 0;
                                if ($field['is_image'] == 1) {
                                    $field['value'] = [];
                                    foreach ($item as $img) {
                                        $field['value'][] = yz_tomedia($img);
                                    }
                                } else {
                                    $field['value'] = $item;
                                }
                            }
                        }
                    }
                    $diyformTypeModel['status'] = 1;
                    unset($diyformTypeModel['form_type']);
                    unset($diyformTypeModel['has_one_diyform_type_member_data']);
                    if(is_null($integrated)){
                        return $this->successJson('成功', $diyformTypeModel);
                    }else{
                        return show_json(1,$diyformTypeModel);
                    }
                } else {
                    if(is_null($integrated)){
                        return $this->errorJson('表单数据不存在!', []);
                    }else{
                        return show_json(0,'表单数据不存在');
                    }
                }

            } else {
                // 没有填写过表单，则直接返回
                $diyformTypeModel['status'] = 0;
                if(is_null($integrated)){
                    return $this->successJson('成功', $diyformTypeModel);
                }else{
                    return show_json(1,$diyformTypeModel);
                }
            }
        } else {
            if(is_null($integrated)){
                return $this->errorJson('表单不存在!', []);
            }else{
                return show_json(0,'表单不存在');
            }
        }
    }

    // 自定义表单保存接口。需要保存yz_diyform_type_member_data关联表和yz_diyform_data表
    public function saveFormData()
    {
        // 先保存表单数据
        $memberId =  \YunShop::app()->getMemberId();
        if (empty($memberId)) {
            return $this->errorJson('会员不存在!', []);
        }
        $formId = \YunShop::request()->form_id;
        if (empty(DiyformTypeModel::uniacid()->find($formId))) {
            return $this->errorJson('表单不存在!', []);
        }
        $formData = \YunShop::request()->form_data;
        $formType = \YunShop::request()->form_type;
        /*
        foreach ($formData[0] as &$row) {
            if (is_array($row)) {
                foreach ($row as &$v) {
                    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $v, $result)) {
                        $img_name = 'supplier' . str_random(16) . '.'.$result[2];
                        $img_file = storage_path('app/public/avatar').'/' . $img_name;
                        file_put_contents($img_file, base64_decode(str_replace($result[1], '', $v)));
                        $v = '/addons/yun_shop/storage/app/public/avatar/' . $img_name;
                    }
                }
            }
        }

        unset($v);
        unset($row);
        */
        $formDatas = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $memberId,
            'form_id' => $formId,
            'data' => iserializer($formData[0]),
            'form_type' => $formType,
            'created_at' => time()
        ];
        DB::beginTransaction();
        $formDataId = DiyformDataModel::insertGetId($formDatas);
        if($formDataId){
            // 再保存关联表
            $diyformTypeMemberDataModel = new DiyformTypeMemberDataModel();
            $diyformTypeMemberDataModel->uniacid = \YunShop::app()->uniacid;
            $diyformTypeMemberDataModel->form_id = $formId;
            $diyformTypeMemberDataModel->member_id = $memberId;
            $diyformTypeMemberDataModel->form_data_id = $formDataId;
            if ($diyformTypeMemberDataModel->save()) {
                DB::commit();
                return $this->successJson('保存成功',['form_id'=>$formId]);
            } else {
                DB::rollBack();
                return $this->errorJson('保存失败!');
            }
        }
        DB::rollBack();
        return $this->errorJson('保存失败');
    }


    /**
     * 根据from_id 获取表单数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDiyFormById()
    {
        // plugin.diyform.api.diy-form.get-diy-form-by-id form_id
        $formId = (int) \YunShop::request()->form_id;
        if(!$formId){
            return $this->errorJson('from_id 不存在',[]);
        }
        $forms = DiyformTypeModel::find($formId);
        $forms->fields = iunserializer($forms->fields);

        $forms->thumb = yz_tomedia( $forms->thumb);
        $forms->description = htmlspecialchars_decode($forms->description);

        if($forms){
            return $this->successJson('成功', $forms->toArray());
        }
        return $this->errorJson('失败', []);
    }

    public function saveDiyFormData()
    {
        //plugin.diyform.api.diy-form.save-diy-form-data ['form_id',[form_data]]
        $memberId = \YunShop::request()->get('member_id');
        $memberId = $memberId ? $memberId : \YunShop::app()->getMemberId();

        $formId = \YunShop::request()->form_id;
        $formData = \YunShop::request()->form_data;
        $formType = \YunShop::request()->form_type;
        foreach ($formData[0] as &$row) {
            if (is_array($row)) {
                foreach ($row as &$v) {
                    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $v, $result)) {
                        $img_name = 'supplier' . str_random(16) . '.'.$result[2];
                        $img_file = storage_path('app/public/avatar').'/' . $img_name;
                        file_put_contents($img_file, base64_decode(str_replace($result[1], '', $v)));
                        $v = config('app.webPath') . '/storage/app/public/avatar/' . $img_name;
                    }
                }
            }
        }
        unset($v);
        unset($row);
        /**
         * @todo 验证表单提交数据
         */
//        fixBY-wk-20201210 用户只需提交一次表单，第二次提交更新表单
        $formInfo = DiyformDataModel::where(['member_id' => $memberId, 'form_id' => $formId, 'form_type' => $formType])->orderBy('id', 'desc')->first();
        if (!empty($formInfo) && $formInfo['submit_number'] > 0) {
            $formDatas = [
                'uniacid' => \YunShop::app()->uniacid,
                'member_id' => $memberId,
                'form_id' => $formId,
                'data' => iserializer($formData[0]),
                'form_type' => $formType,
                'updated_at' => time()
            ];
            $formDataId = DiyformDataModel::updated($formDatas);
        } else {
            $formDatas = [
                'uniacid' => \YunShop::app()->uniacid,
                'member_id' => $memberId,
                'form_id' => $formId,
                'data' => iserializer($formData[0]),
                'form_type' => $formType,
                'created_at' => time()
            ];
            $formDataId = DiyformDataModel::insertGetId($formDatas);
        }
        if($formDataId){
            if (!empty($formInfo) && $formInfo['submit_number'] > 0) {
                $formDataId = $formInfo['id'];
            }
            return $this->successJson('保存成功',['form_data_id'=>$formDataId]);
        }
        return $this->successJson('保存失败');
    }

    /**
     * 获取自定义表单数据
     */
    public function getSingleFormData()
    {

        $form_id = intval(request()->input('form_id'));
        $form_data_id = intval(request()->input('form_data_id'));

        if (empty($form_id)) {
            return $this->errorJson('参数错误!');
        }
        $form_type =  DiyformTypeModel::uniacid()->find($form_id);

        $form_type->fields = iunserializer($form_type->fields);

        $form_type->thumb = yz_tomedia($form_type->thumb);
        $form_type->description = htmlspecialchars_decode($form_type->description);

        if  (!$form_type) {
            return $this->errorJson('自定义表单为空!');
        }

        $form_data = DiyformDataModel::uniacid()->where('id', $form_data_id)->where('form_id', $form_id)->first();

        if ($form_data) {
            $form_type =  $form_type->toArray();
            foreach ($form_type['fields'] as $key => &$field) {
                foreach ($form_data->form_data as $k => $item) {
                    if ($key == $k) {
                        $field['name'] = $field['tp_name'];
                        $field['is_image'] = $field['data_type'] == 5 ? 1 : 0;
                        if ($field['is_image'] == 1) {
                            $field['value'] = [];
                            foreach ($item as $img) {
                                $field['value'][] = yz_tomedia($img);
                            }
                        } else {
                            $field['value'] = $item;
                        }
                    }
                }
            }
            $form_type['status'] = 1;
        } else {
            $form_type['status'] = 0;
        }

        return $this->successJson('', $form_type);
    }


    public static function getData($request)
    {
        $form_data_id = \YunShop::request()->get('form_data_id');
        $result = DiyformOrderModel::getDiyFormByGoodsId($request->id)->first();
        //判断商品有没有设置自定义表单
        if(empty($form_data_id)  && !is_int($form_data_id)){
            if($result['status'] == 1){

                    $data = DiyformOrderModel::getDiyFormByGoodsId($request->id)->with('diyform')->first();
                    $data['diyform']['fields'] = unserialize($data['diyform']['fields']);
                    $data['diyform']['thumb'] = yz_tomedia($data['diyform']['thumb']);
                    $data['diyform']['description'] = htmlspecialchars_decode($data['diyform']['description']);
                    return show_json('1',$data['diyform']);
            }
        }
    }


    public  function saveData($request)
    {
        $memberId = \YunShop::request()->get('member_id');
        $form_id = \YunShop::request()->get('form_id');
        $memberId = $memberId ? $memberId : \YunShop::app()->getMemberId();
        $goods_id =  \YunShop::request()->get('id');
        $formData = \YunShop::request()->form_data;
        foreach ($formData[0] as &$row) {
            if (is_array($row)) {
                foreach ($row as &$v) {
                    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $v, $result)) {
                        $img_name = 'supplier' . str_random(16) . '.'.$result[2];
                        $img_file = storage_path('app/public/avatar').'/' . $img_name;
                        file_put_contents($img_file, base64_decode(str_replace($result[1], '', $v)));
                        $v = config('app.webPath') . '/storage/app/public/avatar/' . $img_name;
                    }
                }
            }
        }
        unset($v);
        unset($row);
        /**
         * @todo 验证表单提交数据
         */

        $formDatas = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $memberId,
            'form_id' => $form_id,
            'goods_id' => $goods_id,
            'data' => iserializer($formData[0]),
            'created_at' => time()
        ];
       // DB::beginTransaction();
        $formDataId = DiyformOrderContentModel::insertGetId($formDatas);
        if($formDataId){
            return $this->successJson('保存成功',['form_data_id'=>$formDataId]);
        }
        return $this->successJson('保存失败');
    }

    public function test()
    {
        DiyformOrderContentModel::uniacid()
            ->where('member_id',71)
            ->whereIn('goods_id',[95])
            ->update(['order_id'=>3]);
        $arr = array(
            array('cat_id' => 10),
            array('cat_id' => 120),
            array('cat_id' => 10),
        );

        $nArr = array();
        for($i = 0, $len = count($arr); $i < $len; ++$i) {
            $nArr[] = $arr[$i]['cat_id'];
        }

        $test = explode(',',implode($nArr, ','));
        dd($test);
        /* $result=
         dd($result);*/
        $test = array();
        $array=array(array('goods_id'=>1),array('goods_id'=>2));
       foreach ($array as $key=>$value){
          $test[$key] = $value['goods_id'];
       }

       dd($test);
    }
}