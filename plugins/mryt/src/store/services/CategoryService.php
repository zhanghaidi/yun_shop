<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/8
 * Time: 下午4:48
 */

namespace Yunshop\Mryt\store\services;


use Yunshop\Mryt\store\models\Category;

class CategoryService
{
    public static function getCategoryMenu($params)
    {
        $catetorys = Category::getAllCategoryGroup($params);

        $catetory_menus = CategoryService::tpl_form_field_category_level2_multi(
            'category', $catetorys['parent'], $catetorys['children'],
            isset($params['ids'][0][0]) ? $params['ids'][0][0] : 0,
            isset($params['ids'][1][0]) ? $params['ids'][1][0] : 0,
            isset($params['ids'][2][0]) ? $params['ids'][2][0] : 0
        );

        return $catetory_menus;
    }

    public static function getCategoryMenuToEdit($params)
    {
        $catetorys = Category::getAllCategoryGroup($params);

        $catetory_menus = CategoryService::tpl_form_field_category_level2_multi(
            'category', $catetorys['parent'], $catetorys['children'],
            isset($params['ids'][0]) ? $params['ids'][0] : 0,
            isset($params['ids'][1]) ? $params['ids'][1] : 0,
            isset($params['ids'][2]) ? $params['ids'][2] : 0
        );

        return $catetory_menus;
    }

    public static function tpl_form_field_category_level2($name, $parents, $children, $parentid, $childid)
    {
        $html = '
        <script type="text/javascript">
            window._' . $name . ' = ' . json_encode($children) . ';
        </script>';
        //if (!defined('TPL_INIT_CATEGORY')) {

            $html .= '
        <script type="text/javascript">
            
            function renderCategory(obj, name){
                var index = obj.options[obj.selectedIndex].value;
                require([\'jquery\', \'util\'], function($, u){
                    $selectChild = $(\'#\'+name+\'_child\');
                    var html = \'<option value="0">请选择二级分类</option>\';
                    if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
                        $selectChild.html(html);
                        return false;
                    }
                    for(var i=0; i< window[\'_\'+name][index].length; i++){
                        html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
                    }
                    $selectChild.html(html);
                });
            }
        </script>
                    ';
            define('TPL_INIT_CATEGORY', true);
        //}
        $html .=
            '<div class="row row-fix tpl-category-container">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <select class="form-control tpl-category-parent" id="' . $name . '_parent" name="' . $name . '[parentid]" onchange="renderCategory(this,\'' . $name . '\')">
                    <option value="0">请选择一级分类</option>';
        $ops = '';
        foreach ($parents as $row) {
            $html .= '
                    <option value="' . $row['id'] . '" ' . (($row['id'] == $parentid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
        }
        $html .= '
                </select>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <select class="form-control tpl-category-child" id="' . $name . '_child" name="' . $name . '[childid]">
                    <option value="0">请选择二级分类</option>';
        if (!empty($parentid) && !empty($children[$parentid])) {
            foreach ($children[$parentid] as $row) {
                $html .= '
                    <option value="' . $row['id'] . '"' . (($row['id'] == $childid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
            }
        }
        $html .= '
                </select>
            </div>
        </div>
    ';
        return $html;
    }

    public static function tpl_form_field_category_level2_multi($name, $parents, $children, $parentid, $childid)
    {
        $html = '
        <script type="text/javascript">
            window._' . $name . ' = ' . json_encode($children) . ';
        </script>';
        //if (!defined('TPL_INIT_CATEGORY')) {
            $html .= '
        <script type="text/javascript">
            function renderCategory(obj, name){
                var index = obj.options[obj.selectedIndex].value;
                require([\'jquery\', \'util\'], function($, u){
                    $selectChild = $(obj).parent().siblings().find(\'#\'+name+\'_child\');
                    var html = \'<option value="0">请选择二级分类</option>\';
                    if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
                        $selectChild.html(html);
                        return false;
                    }
                    for(var i=0; i< window[\'_\'+name][index].length; i++){
                        html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
                    }
                    $selectChild.html(html);
                });
            }
        </script>
                    ';
            define('TPL_INIT_CATEGORY', true);
        //}

        $html .=
            '<div class="row row-fix tpl-category-container">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <select class="form-control tpl-category-parent" id="' . $name . '_parent" name="' . $name . '[parentid][]" onchange="renderCategory(this,\'' . $name . '\')">
                    <option value="0">请选择一级分类</option>';
        $ops = '';
        foreach ($parents as $row) {
            $html .= '
                    <option value="' . $row['id'] . '" ' . (($row['id'] == $parentid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
        }
        $html .= '
                </select>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <select class="form-control tpl-category-child" id="' . $name . '_child" name="' . $name . '[childid][]">
                    <option value="0">请选择二级分类</option>';
        if (!empty($parentid) && !empty($children[$parentid])) {
            foreach ($children[$parentid] as $row) {
                $html .= '
                    <option value="' . $row['id'] . '"' . (($row['id'] == $childid) ? 'selected="selected"' : '') . '>' . $row['name'] . '</option>';
            }
        }
        $html .= '
                </select></div>
        </div>
    ';
        return $html;
    }
}
