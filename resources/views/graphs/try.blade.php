@extends('layouts.app')


@section('style')
    <script src=//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js charset=utf-8></script>
    <script src=//cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.6/highcharts.js charset=utf-8></script>
    <script src=//cdn.jsdelivr.net/npm/fusioncharts@3.12.2/fusioncharts.js charset=utf-8></script>
    <script src=//cdnjs.cloudflare.com/ajax/libs/echarts/4.0.2/echarts-en.min.js charset=utf-8></script>

    <script src="https://raw.githubusercontent.com/nnnick/Chart.js/master/dist/Chart.bundle.js"></script>
    <script>
        var year = ['2013','2014','2015', '2016'];
        var data_click =<?php echo $click; ?>;
        var data_viewer = <?php echo $viewer; ?>;


        var barChartData = {
            labels: year,
            datasets: [{
                label: 'Click',
                backgroundColor: "rgba(220,220,220,0.5)",
                data: data_click
            }, {
                label: 'View',
                backgroundColor: "rgba(151,187,205,0.5)",
                data: data_viewer
            }]
        };


        window.onload = function() {
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
                    elements: {
                        rectangle: {
                            borderWidth: 2,
                            borderColor: 'rgb(0, 255, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Yearly Website Visitor'
                    }
                }
            });


        };
    </script>

@endsection
@section('content')
    <div class="uk-grid">
        <div class="uk-width-1-1">



            <div class="uk-grid uk-grid-small">
                <div class="container">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Dashboard</div>
                                    <div class="panel-body">
                                        <canvas id="canvas" height="280" width="600"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div></div>
@endsection
@section('js')



    @endsection
