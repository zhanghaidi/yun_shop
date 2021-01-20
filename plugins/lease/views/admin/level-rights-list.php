@extends('layouts.base')

@section('content')
@section('title', trans('等级权益'))

<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">等级权益</a></li>
    </ul>
</div>


<div class='panel panel-default'>

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
                    <td>{{$row->rent_free}}</td>
                    <td>{{$row->deposit_free}}</td>

                </tr>

            @endforeach
            </tbody>
        </table>

        {!! $pager !!}
    </div>
</div>
<div style="width:100%;height:150px;"></div>
<script language='javascript'>
   
</script>
@endsection