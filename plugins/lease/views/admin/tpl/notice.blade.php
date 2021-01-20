<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">归还成功通知</label>
        <div class="col-sm-9 col-xs-12">
            <select name='setdata[return_success]' class='form-control diy-notice'>
                <option value="" @if(!$setdata['return_success']) selected @endif >
                    请选择消息模板
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($setdata['return_success'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>