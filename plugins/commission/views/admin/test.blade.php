@extends('layouts.base')

@section('content')
    <section class="content">


        <script type="text/javascript" src="{{ plugin_assets('example-plugin', 'assets/js/example2.js') }}"></script>

        <br/>
        测试语言包：{{trans('Yunshop\ExamplePlugin::test.title')}}

    </section><!-- /.content -->
    @endsection