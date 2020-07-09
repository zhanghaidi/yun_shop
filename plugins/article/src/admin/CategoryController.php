<?php
namespace Yunshop\Article\admin;

use Illuminate\Http\Request;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Article\models\Category;
use app\backend\modules\member\models\MemberLevel;


class CategoryController extends BaseController
{
    public function index()
    {
        $pageSize = 10;
        $keyword = \YunShop::request()->category ? \YunShop::request()->category['keyword'] : '';
        if (\YunShop::request()->category) {
            $categorys = Category::getCategorysByKeyword(\YunShop::request()->category['keyword'])->paginate($pageSize)->toArray();
        } else {
            $categorys = Category::getCategorys()->paginate($pageSize)->toArray();
        }
        $pager = PaginationHelper::show($categorys['total'], $categorys['current_page'], $categorys['per_page']);
        return view('Yunshop\Article::admin.category.list',
            [
                'categorys' => $categorys,
                'keyword' => $keyword,
                'pager' => $pager
            ]
        )->render();
    }

    public function add()
    {
        $categoryModel = new Category();
        $memberLevels = MemberLevel::getMemberLevelList();
        $requestCategory = \YunShop::request()->category;

        if ($requestCategory) {
            //将数据赋值到model
            $categoryModel->setRawAttributes($requestCategory);
            //其他字段赋值
            $categoryModel->uniacid = \YunShop::app()->uniacid;

            //字段检测
            $validator = $categoryModel->validator($categoryModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($categoryModel->save()) {
                    //显示信息并跳转
                    return $this->message('分类创建成功', Url::absoluteWeb('plugin.article.admin.category.index'));
                } else {
                    $this->error('分类创建失败');
                }
            }
        }
        return view('Yunshop\Article::admin.category.info',
            [
                'category' => $categoryModel,
                'levels' => $memberLevels
            ]
        )->render();
    }

    public function edit()
    {
        $categoryModel = Category::getCategory(\YunShop::request()->id);
        if (!$categoryModel) {
            return $this->message('无此记录或已被删除', '', 'error');
        }
        $memberLevels = MemberLevel::getMemberLevelList();
        $requestCategory = \YunShop::request()->category;
        if ($requestCategory) {
            $categoryModel->setRawAttributes($requestCategory);
            $categoryModel->uniacid = \YunShop::app()->uniacid;
            $validator = $categoryModel->validator($categoryModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($categoryModel->save()) {
                    return $this->message('分类修改成功', Url::absoluteWeb('plugin.article.admin.category.index'));
                } else {
                    $this->error('分类修改失败');
                }
            }
        }
        return view('Yunshop\Article::admin.category.info',
            [
                'category' => $categoryModel,
                'levels' => $memberLevels
            ]
        )->render();
    }

    public function deleted()
    {
        $id = \YunShop::request()->id;
        if (!Category::getCategory($id)) {
            return $this->error('没有此分类或已删除');
        }
        if (Category::deletedCategory($id)) {
            return $this->message('删除分类成功', Url::absoluteWeb('plugin.article.admin.category.index'));
        }
    }

}
