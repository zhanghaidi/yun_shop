@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="top" style="margin-bottom:20px">
                <ul class="add-shopnav" id="myTab">
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.somato-type.index')}}">体质管理</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.label.index')}}">症状标签</a></li>
                    <li><a href="{{yzWebUrl('plugin.minapp-content.admin.question.index')}}">测评题库</a></li>
                    <li class="active"><a href="{{yzWebUrl('plugin.minapp-content.admin.answer.index')}}">用户测评</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="panel panel-success">
                <div class="panel-heading">答题详情</div>
                <div class="panel-body">
                    {{$info['answers']}}
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

