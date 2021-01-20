<div class='panel panel-default'>
    <div class='panel-heading'>{{$plugin_name}}</div>
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$plugin_name}}</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline">
                    <input type="radio" name="widgets[nominate][is_open]" value="0"
                           @if(empty($is_open)) checked="true" @endif /> 关闭</label>
                <label class="radio-inline">
                    <input type="radio" name="widgets[nominate][is_open]" value="1"
                           @if($is_open == '1') checked="checked" @endif /> 开启</label>
                <span class='help-block'></span>
            </div>
        </div>
    </div>
</div>