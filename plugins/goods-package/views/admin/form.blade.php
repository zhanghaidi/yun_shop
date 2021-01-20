<input type="hidden" name="c" value="site"/>
<input type="hidden" name="a" value="entry"/>
<input type="hidden" name="m" value="yun_shop"/>
<input type="hidden" name="do" value="goods-package" id="form_do"/>
<input type="hidden" name="route" value="{{ 'plugin.goods-package.admin.package.index' }}" id="route" />
<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
    <div class="">
        <input type="text" class="form-control"  name="search[title]" value="{{$search['title']?$search['title']:''}}" placeholder="套餐名称"/>
    </div>
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
    <div class="">
        <select name='search[status]' class='form-control'>
            <option value='' >状态</option>
            <option value='1' @if($search['status'] == 1)  selected="selected"@endif>开启</option>
            <option value='0' @if(is_numeric($search['status']) && ((int)$search['status']) == 0)  selected="selected"@endif>关闭</option>
        </select>
    </div>
</div>