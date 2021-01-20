@extends('layouts.base')

@section('title', trans('会员等级'))
@section('content')
    <div class="rightlist">
        <form action="" method="post">
            <div class='panel panel-default'>
                <div class='panel-body'>
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="text-align: center;">等级权重</th>
                            <th style="text-align: center;">等级名称</th>
                            <th style="text-align: center;">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $row)
                            <tr>
                                <td style="text-align: center;">
                                    <span class="label label-danger">
                                        {{ $row->level }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    {{ $row->level_name }}
                                </td>
                                <td style="text-align: center;">
                                    <a class='btn btn-default' href="{{ yzWebUrl('plugin.nominate.admin.level.detail', ['id' => $row->id]) }}" title="编辑／查看"><i class='fa fa-edit'></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
@endsection