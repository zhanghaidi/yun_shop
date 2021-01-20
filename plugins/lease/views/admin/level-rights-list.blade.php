@extends('layouts.base')

@section('content')
@section('title', trans('等级权益'))

<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">等级权益</a></li>
    </ul>
</div>

<div class='panel panel-default'>
    <form action="{!! yzWebUrl('plugin.lease-toy.admin.level-rights.setFree') !!}" method="post">
        <div class='panel-body'>
            <table class="table table-hover" style="overflow:visible;">
                <thead>
                <tr style="height: 20%;text-align: center;">
                    <th style='width:5%;'>id</th>
                    <th style='width:10%;'>等级</th>
                    <th style='width:10%;'>免租金</th>
                    <th style='width:10%;'>免押金</th>

                </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->level_name}}</td>
                        <td>
                             <div class='input-group col-sm-6'>
                                <input type="text" name="free[{{$row->id}}][rent_free]" class="form-control"
                                       value="{{$row->rent_free}}"/>
                                <span class='input-group-addon'>件</span>
                            </div>
                        </td>
                        <td>
                            <div class='input-group col-sm-6'>
                                <input type="text" name="free[{{$row->id}}][deposit_free]" class="form-control"
                                       value="{{$row->deposit_free}}"/>
                                <span class='input-group-addon'>件</span>
                            </div>
                        </td>

                    </tr>

                @endforeach
                </tbody>
            </table>
            {!! $pager !!}
        </div>
        <div style="margin-left:13px;margin-top:20px">
            <input name="submit" type="submit" class="btn btn-success" value="保存设置">
        </div>
    </form>
</div>
<div style="width:100%;height:150px;"></div>
<script language='javascript'>
   
</script>
@endsection