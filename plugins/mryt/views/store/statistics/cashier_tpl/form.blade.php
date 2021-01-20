<form action="" method="get" class="form-horizontal" role="form" id="form1">
    <input type="hidden" name="c" value="site"/>
    <input type="hidden" name="a" value="entry"/>
    <input type="hidden" name="m" value="yun_shop"/>
    <input type="hidden" name="do" value="cashier" id="form_do"/>
    <input type="hidden" name="route" value="{{\Yunshop\Mryt\store\admin\CashierController::INDEX_URL}}" id="route"/>

    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
        <div class="">
            <input type="text" class="form-control"  name="search[store_name]" value="{{$search['store_name']?$search['store_name']:''}}" placeholder="门店名称"/>
        </div>
    </div>

    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
        <div class="">
            <input type="text" class="form-control"  name="search[member]" value="{{$search['member']?$search['member']:''}}" placeholder="店主会员昵称/姓名/手机号"/>
        </div>
    </div>

    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
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

    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
        <div class="">
            <input type="hidden" id="province_id" value="{{$search['province_id']?$search['province_id']:0}}"/>
            <input type="hidden" id="city_id" value="{{$search['city_id']?$search['city_id']:0}}"/>
            <input type="hidden" id="district_id" value="{{$search['district_id']?$search['district_id']:0}}"/>
            <input type="hidden" id="street_id" value="{{$search['street_id']?$search['street_id']:0}}"/>
            {!! app\common\helpers\AddressHelper::tplLinkedAddress(['search[province_id]','search[city_id]','search[district_id]','search[street_id]'], [])!!}
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-7 col-lg-9 col-xs-12">
            <button class="btn btn-success" id="search"><i class="fa fa-search"></i> 搜索</button>
            <button type="submit" name="export" value="1" id="export" class="btn btn-default">导出
                Excel
            </button>
        </div>
    </div>
</form>