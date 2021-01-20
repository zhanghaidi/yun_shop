<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启积会员分奖励</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[member][award]' value='1'
                   @if($exist_plugins['member']['member_award'] == 1) checked @endif
            /> 是
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[member][award]' value='0'
                   @if($exist_plugins['member']['member_award'] == 0) checked @endif
            /> 否
        </label>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员奖励积分</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[member][award_point]' class="form-control discounts_value"
                   value="{{str_replace('%', '', $exist_plugins['member']['award_point'])?str_replace('%', '', $exist_plugins['member']['award_point']):0}}"/>
            <div class='input-group-addon waytxt'>%</div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级会员奖励积分</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[member][award_point_1]' class="form-control discounts_value"
                   value="{{str_replace('%', '', $exist_plugins['member']['award_point_1'])?str_replace('%', '', $exist_plugins['member']['award_point_1']):0}}"/>
            <div class='input-group-addon waytxt'>%</div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">二级会员奖励积分</label>
    <div class="col-sm-6 col-xs-6">
        <div class='input-group'>
            <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[member][award_point_2]' class="form-control discounts_value"
                   value="{{str_replace('%', '', $exist_plugins['member']['award_point_2'])?str_replace('%', '', $exist_plugins['member']['award_point_2']):0}}"/>
            <div class='input-group-addon waytxt'>%</div>
        </div>
    </div>
</div>

@if(array_key_exists('love', $exist_plugins))
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启{{$exist_plugins['love']['name']}}奖励</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='widgets[love][award]' value='1'
                       @if($exist_plugins['love']['love_goods']['award'] == 1) checked @endif
                /> 是
            </label>
            <label class='radio-inline'>
                <input type='radio' name='widgets[love][award]' value='0'
                       @if($exist_plugins['love']['love_goods']['award'] == 0) checked @endif
                /> 否
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员{{$exist_plugins['love']['name']}}奖励</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                {{--  <input type="hidden" name="widgets[love][award]" value="1">  --}}
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[love][award_proportion]' class="form-control discounts_value"
                       value="{{$exist_plugins['love']['love_goods']['award_proportion']?$exist_plugins['love']['love_goods']['award_proportion']:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">一级会员{{$exist_plugins['love']['name']}}奖励</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[love][parent_award_proportion]' class="form-control discounts_value"
                       value="{{$exist_plugins['love']['love_goods']['parent_award_proportion']?$exist_plugins['love']['love_goods']['parent_award_proportion']:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">二级会员{{$exist_plugins['love']['name']}}奖励</label>
        <div class="col-sm-6 col-xs-6">
            <div class='input-group'>
                <input onkeyup="value=value.replace(/[^\d.]/g,'')" type='text' name='widgets[love][second_award_proportion]' class="form-control discounts_value"
                       value="{{$exist_plugins['love']['love_goods']['second_award_proportion']?$exist_plugins['love']['love_goods']['second_award_proportion']:0}}"/>
                <div class='input-group-addon waytxt'>%</div>
            </div>
        </div>
    </div>
@endif
