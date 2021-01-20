<div class='panel panel-default'>

    <div class='panel-heading'>微店分红</div>
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">微店分红</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline">
                    <input type="radio" name="widgets[micro][is_open_bonus]" value="0"
                           @if(empty($micro_goods['is_open_bonus'])) checked="true" @endif /> 关闭</label>
                <label class="radio-inline">
                    <input type="radio" name="widgets[micro][is_open_bonus]" value="1"
                           @if($micro_goods['is_open_bonus'] == '1') checked="checked" @endif /> 开启</label>
                <span class='help-block'>通过微店店主链接购买任何参与微店分红的商品，店主都可获得对应的等级分红</span>
            </div>
        </div>
    </div>
    <div class='panel-heading'>
        店主独立分红设置
    </div>
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">店主独立分红</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline">
                    <input type="radio" name="widgets[micro][independent_bonus]" value="0"
                           @if(empty($micro_goods['independent_bonus'])) checked="true" @endif /> 关闭</label>
                <label class="radio-inline">
                    <input type="radio" name="widgets[micro][independent_bonus]" value="1"
                           @if($micro_goods['independent_bonus'] == '1') checked="checked" @endif /> 开启</label>
                <span class='help-block'>启用店主独立设置，微店店主拥有独立的分红比例，不受店主等级分红比例影响<br><span style="color:red">等级比例只能填写小数或整数，其他不做保存处理</span></span>
            </div>
        </div>
        @foreach($micro_levels as $level)
            <div class="form-group">
                <div class="col-xs-12 col-sm-9 col-md-10">
                    <div class='input-group'>
                        <span class="input-group-addon">{{$level->level_name}}</span>
                        <input type="text" name="widgets[micro][level][{{$level->id}}]"  value="{{$micro_goods['set'][$level->id]}}" class="form-control" />
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>