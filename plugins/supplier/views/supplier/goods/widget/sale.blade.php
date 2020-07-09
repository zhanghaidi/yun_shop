<link href="{{static_url('yunshop/goods/goods.css')}}" media="all" rel="stylesheet" type="text/css"/>

<input type="hidden" name="widgets[sale][max_point_deduct]" value=""
                   class="form-control"/>
<input type="hidden" name="widgets[sale][min_point_deduct]" value=""
       class="form-control"/>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">单品满件包邮</label>
    <div class="col-xs-12 col-sm-9 col-md-10">
        <div class='input-group'>
            <span class="input-group-addon">满</span>
            <input type="text" name="widgets[sale][ed_num]" value="{{ $item->ed_num }}" class="form-control"/>
            <span class="input-group-addon">件</span>
        </div>
        <span class="help-block">设置0，则不支持满件包邮</span>

    </div>
</div>

<input type="hidden" name="widgets[sale][ed_money]" value="" class="form-control"/>

<input type="hidden" name="widgets[sale][ed_full]" class="form-control" value=""/>
<input type="hidden" name="widgets[sale][ed_reduction]" class="form-control"
                           value=""/>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">不参与单品包邮地区</label>
    <div class="col-xs-12 col-sm-9 col-md-10">
        <div id="areas" class="form-control-static">{{ $item->ed_areas }}</div>
        <a href="javascript:;" class="btn btn-default selectareas" onclick="selectAreas()">添加不参加满包邮的地区</a>
        <input type="hidden" id='selectedareas' name="widgets[sale][ed_areas]" value="{{ $item->ed_areas }}"/>
        <input type="hidden" id='selectedareaids' name="widgets[sale][ed_areaids]" value="{{ $item->ed_areaids }}"/>

    </div>
</div>

<input type="hidden" name="widgets[sale][point]" value="" class="form-control"/>


@include('area.selectprovinces')