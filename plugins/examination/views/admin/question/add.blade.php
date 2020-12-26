@extends('layouts.base')

@section('content')
@section('title', trans($pluginName))

<div class="rightlist">
    <div class="panel panel-default">
        <div class="panel-heading">选择题目类型</div>
        <div class="panel-body">
            <div class="col-lg-12 col-md-12" style="margin-top:100px"></div>
            <div class="col-lg-3 col-md-4">
                <a href="{{ yzWebUrl('plugin.examination.admin.question.edit', ['type' => 1]) }}">
                    <div class="card">
                        <div class="card-content bg-info text-center">单选题</div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4">
                <a href="{{ yzWebUrl('plugin.examination.admin.question.edit', ['type' => 2]) }}">
                    <div class="card">
                        <div class="card-content bg-success text-center">多选题</div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4">
                <a href="{{ yzWebUrl('plugin.examination.admin.question.edit', ['type' => 3]) }}">
                    <div class="card">
                        <div class="card-content bg-danger text-center">判断题</div>
                    </div>
                </a>
            </div>
            <!-- <div class="col-lg-3 col-md-4" style="display:flex; align-items:center; margin-top: 50px">
                <a href="{{ yzWebUrl('plugin.examination.admin.question.edit', ['type' => 4]) }}">填空题</a>
            </div>
            <div class="col-lg-3 col-md-4" style="display:flex; align-items:center; margin-top: 50px">
                <a href="{{ yzWebUrl('plugin.examination.admin.question.edit', ['type' => 5]) }}">问答题</a>
            </div> -->
        </div>
    </div>
</div>
@endsection

