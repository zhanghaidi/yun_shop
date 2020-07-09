<link rel="stylesheet" href="{{static_url('css/viewer.min.css')}}">
@if($diyform_items)
    <div class='panel-heading'>
        自定义表单信息
    </div>
    <div class='panel-body' id="tp_name_0">

        @foreach($diyform_items as $item)
            @foreach($item['fields'] as $fname => $field)
                @foreach($item['detail']['form_data'] as $key => $val)
                    @if($key == $fname)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                                {{$field['tp_name']}}
                            </label>
                            <div class="col-sm-9 col-xs-12">
                                                <span class="help-block">
                                                   @if(is_array($val) && (strexists($val[0], 'image') || strexists($val[0], 'images') || strexists($val[0], 'newimage')))
                                                        @foreach($val as $v)
                                                            <img style="width:50px;height:50px;border:1px solid #ccc;padding:1px"
                                                                 src="{!! yz_tomedia($v) !!}"/>
                                                        @endforeach
                                                    @elseif (is_array($val) && (!strexists($val[0], 'image') || !strexists($val[0], 'images') || !strexists($val[0], 'newimage')))
                                                        @foreach($val as $v)
                                                            <span>{{ $v }}</span>
                                                        @endforeach
                                                    @else
                                                        {{$val}}
                                                    @endif
                                                </span>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        @endforeach


    </div>
@endif
<script type="text/javascript" src="{{static_url('js/viewer.min.js')}}"></script>
<script>
    var tp_name_0 = new Viewer(document.getElementById('tp_name_0'), {
        url: 'src'
    });
</script>