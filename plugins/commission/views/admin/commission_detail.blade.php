@extends('layouts.base')
@section('title', "分红详情")
@section('content')
    <div class="rightlist">
        <div id="app">
            <p v-if="seen">现在你看到我了</p>
        </div>
    </div>
    <script>
        var app = new Vue({
            el: '#app',
            delimiters: ['[[', ']]'],
            data: {
                seen: false
            }
        })
    </script>

@endsection