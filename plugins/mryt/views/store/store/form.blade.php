<input type="hidden" name="c" value="site"/>
<input type="hidden" name="a" value="entry"/>
<input type="hidden" name="m" value="yun_shop"/>
<input type="hidden" name="do" value="store" id="form_do"/>
<input type="hidden" name="route" value="{{\Yunshop\Mryt\store\admin\StoreController::INDEX_URL}}" id="route" />
<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">门店地址</label>
        <div class="">
            {!! app\common\helpers\AddressHelper::tplLinkedAddress(['search[province_id]','search[city_id]','search[district_id]','search[street_id]'], [])!!}
        </div>
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
    {{--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微店名称</label>--}}
    <div class="">
        <input type="text" class="form-control"  name="search[store_name]" value="{{$search['store_name']?$search['store_name']:''}}" placeholder="门店名称"/>
    </div>
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
    {{--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微店名称</label>--}}
    <div class="">
        <input type="text" class="form-control"  name="search[member]" value="{{$search['member']?$search['member']:''}}" placeholder="店主会员昵称/姓名/手机号"/>
    </div>
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
    {{--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">微店等级</label>--}}
    <div class="">
        <select name='search[category]' class='form-control'>
            <option value=''>分类不限</option>
            @foreach($category_list as $category)
                <option value='{{$category->id}}' @if($search['category'] == $category->id)  selected="selected"@endif
                >{{$category->name}}</option>
            @endforeach
        </select>
    </div>
</div>