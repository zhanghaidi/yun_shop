@extends('layouts.base')
@section('title', trans('Yunshop\Sign::sign.sign_set'))
@section('content')

    <div class="rightlist">

        @include('Yunshop\Sign::Backend.tabs')

        <form action="{{ yzWebUrl('plugin.sign.Backend.Controllers.base-set.store') }}" method="post"
              class="form-horizontal form" enctype="multipart/form-data">

            <div class='panel panel-default form-horizontal form'>

                <div class='panel-heading'></div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Sign::sign.sign_on_off') }}：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="sign[sign_status]" value="1" @if ($sign['sign_status'] == 1) checked="checked" @endif />
                                {{ trans('Yunshop\Sign::sign.on') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="sign[sign_status]" value="0" @if ($sign['sign_status'] == 0) checked="checked" @endif />
                                {{ trans('Yunshop\Sign::sign.off') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class='panel-body'>
                    <div class="form-group" style="padding-top:20px;">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Sign::sign.custom_name') }}
                            ：</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sign[sign_name]" class="form-control" value="{{ $sign['sign_name'] }}" placeholder="{{ trans('Yunshop\Sign::sign.custom_name_hint') }}" style="width: 250px;"/>
                            <div class="help-block">{{ trans('Yunshop\Sign::sign.custom_name_introduce') }}</div>
                        </div>
                    </div>
                </div>


                <div class='panel-heading'>{{ trans('Yunshop\Sign::sign.success_link') }}</div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Sign::sign.success_link') }}：</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group ">
                            <input class="form-control" type="text" data-id="PAL-00010" placeholder="{{ trans('Yunshop\Sign::sign.success_link_hint') }}" value="{{ $sign['success_link'] }}" name="sign[success_link]">
                            <span class="input-group-btn">
                                <button class="btn btn-default nav-link" type="button" data-id="PAL-00010">{{ trans('Yunshop\Sign::sign.choose_link') }}</button>
                            </span>
                        </div>
                        <span class='help-block'>{{ trans('Yunshop\Sign::sign.success_link_introduce') }}</span>
                    </div>
                </div>



                <div class='panel-heading'>{{ trans('Yunshop\Sign::sign.award_set') }}</div>
                <div class='panel-body'>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Sign::sign.every_award') }}：</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group-addon" style="width: 110px;">{{ trans('Yunshop\Sign::sign.point_award') }}</div>
                                    <input type="text" name="sign[award_point]" class="form-control" value="{{ $sign['award_point'] }}" placeholder=""/>
                                    <div class="input-group-addon">{{ trans('Yunshop\Sign::sign.point_unit') }}</div>
                                </div>
                            </div>
                            <div class="help-block">
                                {{ trans('Yunshop\Sign::sign.point_award_introduce') }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group">
                                <div class="input-group">
                                    <div class="input-group-addon" style="width: 110px;">{{ trans('Yunshop\Sign::sign.coupon_award') }}</div>
                                    <input type="hidden" class="form-control" name='sign[award_coupon_id]' value='{{ $sign['award_coupon_id'] or '' }}'/>
                                    <input type="text" class="form-control" name='sign[award_coupon_name]' value='{{ $sign['award_coupon_name'] or '' }}' readonly/>
                                    <div class='input-group-btn'>
                                        <button class='btn select_coupon' type='button'>
                                            {{ trans('Yunshop\Sign::sign.choose_coupon') }}
                                        </button>
                                    </div>
                                    <input type="text" name="sign[award_coupon_num]" class="form-control" value="{{ $sign['award_coupon_num'] }}" placeholder=""/>
                                    <div class="input-group-addon">{{ trans('Yunshop\Sign::sign.coupon_unit') }}</div>
                                    <div class='input-group-btn'>
                                         <button class="btn btn-danger" type="button" onclick="index()">清除选择</button>
                                    </div>
                                </div>
                            </div>
                            <div class="help-block">
                                {{ trans('Yunshop\Sign::sign.coupon_award_introduce') }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group" @if ($sign['love_status'] != 1) style="display: none" @endif>
                                <div class="input-group">
                                    <div class="input-group-addon" style="width: 110px;">{{ trans('Yunshop\Sign::sign.love_award') }}</div>
                                    <input type="text" class="form-control" name='sign[award_love_min]' value='{{ $sign['award_love_min'] }}'/>
                                    <div class='input-group-btn'>
                                        <span class='input-group-addon'>
                                            {{ trans('Yunshop\Sign::sign.to') }}
                                        </span>
                                    </div>
                                    <input type="text" name="sign[award_love_max]" class="form-control" value="{{ $sign['award_love_max'] }}" />
                                    <div class="input-group-addon">{{ trans('Yunshop\Sign::sign.love') }}</div>
                                </div>
                                <div class="help-block">
                                    {{ trans('Yunshop\Sign::sign.love_award_introduce') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{ trans('Yunshop\Sign::sign.cumulative_award') }}：</label>
                        <div class="col-sm-9 col-xs-12">



                            <div class='recharge-items'>

                                @foreach( $sign['cumulative'] as $cumulative)
                                <div class="input-group recharge-item" style="margin-top:5px; width: 90%">
                                    <select name='sign[cumulative][award_type][]' class='form-control'>
                                        <option value="1" @if($cumulative['award_type'] == 1) selected @endif>{{ trans('Yunshop\Sign::sign.point') }}</option>
                                        <option value="2" @if($cumulative['award_type'] == 2) selected @endif>{{ trans('Yunshop\Sign::sign.coupon') }}</option>
                                    </select>
                                    <span class="input-group-addon">{{ trans('Yunshop\Sign::sign.cumulative_sign') }}</span>
                                    <input type="text" class="form-control" name='sign[cumulative][days][]' value='{{ $cumulative['days'] or '' }}'/>
                                    <span class="input-group-addon">{{ trans('Yunshop\Sign::sign.sign_unit') }}</span>

                                    <input type="hidden" class="form-control" name='sign[cumulative][coupon_id][]' value='{{ $cumulative['coupon_id'] or '' }}'/>
                                    <input type="text" class="form-control" name='sign[cumulative][coupon_name][]' value='{{ $cumulative['coupon_name'] or '' }}' readonly @if($cumulative["award_type"] != 2)style="display: none;" @endif/>
                                    <div class='input-group-btn' @if($cumulative["award_type"] != 2) style="display: none;" @endif>
                                        <button class='btn select_coupon' type='button'>
                                            {{ trans('Yunshop\Sign::sign.choose_coupon') }}
                                        </button>
                                    </div>

                                    <input type="text" class="form-control" name='sign[cumulative][award_value][]' value='{{ $cumulative['award_value'] or '' }}'/>
                                    <span class="input-group-addon unit">@if($cumulative["award_type"] == 2){{ trans('Yunshop\Sign::sign.coupon_unit') }} @else {{ trans('Yunshop\Sign::sign.point_unit') }} @endif</span>
                                    <div class='input-group-btn'>
                                        <button class='btn btn-danger' type='button' onclick="removeAwardItem(this)"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <h4>
                                <button type='button' class="btn btn-default" onclick='addAwardItem()'
                                        style="margin-bottom:5px">
                                    <i class='fa fa-plus'></i>{{ trans('Yunshop\Sign::sign.add_cumulative_award') }}
                                </button>
                            </h4>

                            <span class="help-block"></span>
                        </div>
                    </div>

                </div>

            </div>


            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9">
                    <input type="submit" name="submit" value="{{ trans('Yunshop\Sign::sign.button_submit') }}"
                           class="btn btn-primary col-lg-1" onclick='return formcheck()'/>
                </div>
            </div>

        </form>
    </div>

    <script language='javascript'>

        $(function(){
            $(document).on('click', '.select_coupon', function() {

                showCouponModel($(this).get(0));
                //console.log($(this).prev('input'));
                $(this).parents('div').prev('input').addClass('select_coupon_name');
                $(this).parents('div').prev('input').prev('input').addClass('select_coupon_id');
                //$(this).parents('.recharge-item').find('input[name="widgets[coupon][coupon_id][]"]').addClass('select_coupon_id');
                //$(this).parents('.recharge-item').find('input[name="widgets[coupon][coupon_name][]"]').addClass('select_coupon_name');
            });
        });

        $(document).on('change', 'select', function () {

            var val = $(this).val();
            if (val == 2) {
                $(this).next('span').next('input').next('span').next('input').next('input').show();
                $(this).next('span').next('input').next('span').next('input').next('input').next('div').show();
                $(this).next('span').next('input').next('span').next('input').next('input').next('div').next('input').next('span').html('张');
            } else {
                $(this).next('span').next('input').next('span').next('input').next('input').hide();
                $(this).next('span').next('input').next('span').next('input').next('input').next('div').hide();
                $(this).next('span').next('input').next('span').next('input').next('input').next('div').next('input').next('span').html('积分');
            }
        });


        function addAwardItem() {
            var html = '<div class="input-group recharge-item"  style="margin-top:5px; width: 90%;">';
            html += '<select name="sign[cumulative][award_type][]"  class="form-control">';
            html += '<option value="1">{{ trans("Yunshop\\Sign::sign.point") }}</option>';
            html += '<option value="2">{{ trans("Yunshop\\Sign::sign.coupon") }}</option>';
            html += '</select>';
            html += '<span class="input-group-addon">{{ trans("Yunshop\\Sign::sign.cumulative_sign") }}</span>';
            html += '<input type="text" class="form-control" name="sign[cumulative][days][]"  />';
            html += '<span class="input-group-addon">{{ trans("Yunshop\\Sign::sign.sign_unit") }}</span>';
            html += '<input type="hidden" class="form-control" name="sign[cumulative][coupon_id][]" value=""/>';
            html += '<input type="text" class="form-control" name="sign[cumulative][coupon_name][]" value="" readonly style="display: none;"/>';
            html += '<div class="input-group-btn" style="display: none;">';
            html += '<button class="btn select_coupon" type="button">{{ trans("Yunshop\\Sign::sign.choose_coupon") }}</button>';
            html += '</div>';
            html += '<input type="text" class="form-control"  name="sign[cumulative][award_value][]"  />';
            html += '<span class="input-group-addon unit">{{ trans("Yunshop\\Sign::sign.point_unit") }}</span>';
            html += '<div class="input-group-btn"><button type="button" class="btn btn-danger" onclick="removeAwardItem(this)"><i class="fa fa-trash"></i></button></div>';
            html += '</div>';
            $('.recharge-items').append(html);
        }


        function removeAwardItem(obj) {
            $(obj).closest('.recharge-item').remove();
        }

        function index(){
            $('input[name="sign[award_coupon_name]"]').val('');
            $('input[name="sign[award_coupon_num]"]').val('');
          
        }

    </script>

    @include('public.admin.mylink')
    @include('public.admin.coupon')

@endsection

