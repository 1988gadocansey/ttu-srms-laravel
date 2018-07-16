@extends('layouts.app')


@section('style')
    <!-- additional styles for plugins -->
    <!-- weather icons -->
    <link rel="stylesheet" href="public/assets/plugins/weather-icons/css/weather-icons.min.css" media="all">
    <!-- metrics graphics (charts) -->
    <link rel="stylesheet" href="public/assets/plugins/metrics-graphics/dist/metricsgraphics.css">
    <!-- chartist -->
    <link rel="stylesheet" href="public/assets/plugins/chartist/dist/chartist.min.css">

@endsection
@section('content')
    <div class="uk-grid">
        <div class="uk-width-1-1">



            <div class="uk-grid uk-grid-small">
                 <div>{!! $chart->container() !!}</div>

            </div>
        </div></div>
@endsection
@section('js')
     {!! $chart->script() !!}
    <script src=//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js charset=utf-8></script>
    <script src=//cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.6/highcharts.js charset=utf-8></script>
    <script src=//cdn.jsdelivr.net/npm/fusioncharts@3.12.2/fusioncharts.js charset=utf-8></script>
    <script src=//cdnjs.cloudflare.com/ajax/libs/echarts/4.0.2/echarts-en.min.js charset=utf-8></script>


@endsection