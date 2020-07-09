<?php
namespace Yunshop\Article\admin;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\facades\Setting;

class SetController extends BaseController
{
     public function index()
     {
         $set = Setting::get('plugin.article');
         $requestModel = \YunShop::request()->article;
         if ($requestModel) {
             if (Setting::set('plugin.article', $requestModel)) {
                 return $this->message('设置成功', Url::absoluteWeb('plugin.article.admin.set'));
             } else {
                 $this->error('设置失败');
             }
         }

         return view('Yunshop\Article::admin.set',
             [
                 'set' => $set,
             ]
         )->render();
     }
}