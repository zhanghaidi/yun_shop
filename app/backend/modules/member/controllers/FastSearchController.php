<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/9/30
 * Time: δΈε5:42
 */

namespace app\backend\modules\member\controllers;

use app\backend\modules\member\models\Member;
use app\common\components\BaseController;

class FastSearchController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $values = Member::searchLike(request('keyword'))->get();
        return $this->successJson('ζε',$values);
    }
}