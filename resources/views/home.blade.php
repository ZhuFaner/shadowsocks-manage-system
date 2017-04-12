@extends('layouts.app')
@section('content')
    <script type="text/javascript">
        $(document).ready(function () {
            $.get("{{ url('rank?type=day') }}",function (data) {
                var json = JSON.parse(data);
                var title = "最近一天流量使用排名（单位：KB）";
                drawSheet(title, json.labels, json.flows,json.bg_colors)
            });
            $("#btn_last_hour").click(function () {
                $.get("{{ url('rank?type=hour')  }}",function (data) {
                    var json = JSON.parse(data);
                    var title = "最近一小时流量使用排名（单位：KB）";
                    drawSheet(title, json.labels, json.flows,json.bg_colors)
                });
            });

            $("#btn_last_day").click(function () {
                $.get("{{ url('rank?type=day') }}",function (data) {
                    var json = JSON.parse(data);
                    var title = "最近一天流量使用排名（单位：KB）";
                    drawSheet(title, json.labels, json.flows,json.bg_colors)
                });
            });

            $("#btn_last_week").click(function () {
                $.get("{{ url('rank?type=week') }}",function (data) {
                    var json = JSON.parse(data);
                    var title = "过去七天流量使用排名（单位：KB）";
                    drawSheet(title, json.labels, json.flows,json.bg_colors)
                });
            });
            function drawSheet(title,labels,flows,backgroundColor) {
                $("#myChart").remove(); // this is my <canvas> element
                $("#chart_parent").append('<canvas id="myChart" class="chart" width="500" height="250"></canvas>');
                var ctx = document.getElementById("myChart").getContext("2d");
                var data = {
                    datasets: [{
                        data: flows,
                        backgroundColor: backgroundColor,
                        label: '流量' // for legend
                    }],
                    labels: labels
                };
                var options = {
                    elements: {
                        arc: {
                            borderColor: "#ffffff"
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                    },
                    title: {
                        display: true,
                        text: title,
                        fontColor: "#5184bc",
                        fontSize:18
                    },
                    legend: {
                        display: false
                    },
                };

                new Chart(ctx, {
                    data: data,
                    type: "bar",
                    options: options
                });
            }

        });

    </script>
    <div class="container">
        <div class="row"> 
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">流量统计排行</div>
                    <div class="panel-body">
                        <div>统计周期：
                            <button id="btn_last_hour">过去1小时</button>
                            <button id="btn_last_day">过去1天</button>
                            <button id="btn_last_week">过去7天</button>
                        </div>
                        <div id="chart_parent">
                            <canvas id="myChart" class="chart" width="500" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection