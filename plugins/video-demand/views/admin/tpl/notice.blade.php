
<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">讲师分红订单通知</label>
        <div class="col-sm-9 col-xs-12">
            <select name='setdata[lecturer_reward_order]' class='form-control diy-notice'>
                <option value="" @if(!$set['lecturer_reward_order']) selected @endif >
                    请选择消息模板
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($set['lecturer_reward_order'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">讲师分红订单结算通知</label>
        <div class="col-sm-9 col-xs-12">
            <select name='setdata[reward_order_settle]' class='form-control diy-notice'>
                <option value="" @if(!$set['reward_order_settle']) selected @endif >
                    请选择消息模板
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($set['reward_order_settle'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class='panel-body'>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员打赏通知</label>
        <div class="col-sm-9 col-xs-12">
            <select name='setdata[reward]' class='form-control diy-notice'>
                <option value="" @if(!$set['reward']) selected @endif >
                    请选择消息模板
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($set['reward'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>