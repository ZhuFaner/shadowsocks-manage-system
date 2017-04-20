@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row"> 
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">用户详情</div>
                    <div class="panel-body">
                        <div>
                            <div>用户名：<label>{{ $user->name }}</label></div>
                            <div>端&nbsp;&nbsp;&nbsp;口：<label id="label_port">{{ $user->port }}</label></div>
                            <div>密&nbsp;&nbsp;&nbsp;码：<label>{{ $user->password }}</label></div>
                            {{--round($day_flow / 1024, 2) . 'KB'--}}
                            <div>本&nbsp;&nbsp;&nbsp;日：<label>{{ $day_flow }}</label></div>
                            <div>本&nbsp;&nbsp;&nbsp;周：<label>{{ $week_flow }}</label></div>
                            <div>本&nbsp;&nbsp;&nbsp;月：<label>{{ $month_flow }}</label></div>
                            <div>总流量：<label>{{ $total }}</label></div>
                            <div>
                                <qr-code data="{{ $qr_url }}"></qr-code>
                            </div>
                            <br>
                            <div>流量统计：</div>
                            <hr>
                            <div style="width: 100%; max-width: 800px;">
                                <div>统计周期：
                                    <button id="btn_by_hour">按小时</button>
                                    <button id="btn_by_day">按天</button>
                                    <button id="btn_by_week">按周</button>
                                </div>
                                <div id="time_period" class="period_style"></div>
                                <div id="chart_parent">
                                    <canvas id="myChart" class="chart" width="500" height="250"></canvas>
                                </div>
                                <div>
                                    <button id="pre" style="padding-left: 20px;padding-right: 20px">前一天</button>
                                    <button id="next" style="padding-left: 20px;padding-right: 20px">后一天</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            var hour = 0;
            var day = 0;
            var week = 0;
            var type = 1;
            var port = $("#label_port").text();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            getDayFlow(day);
            $("#btn_by_hour").click(function () {
                type = 0;
                $("#pre").text("前一小时");
                $("#next").text("后一小时");
                getHourFlow(hour);
            });
            $("#btn_by_day").click(function () {
                type = 1;
                $("#pre").text("前一天");
                $("#next").text("后一天");
                getDayFlow(day);
            });
            $("#btn_by_week").click(function () {
                type = 2;
                $("#pre").text("前一周");
                $("#next").text("后一周");
                getWeekFlow(week);
            });
            $("#pre").click(function () {
                switch (type) {
                    case 0:
                        hour = hour - 3600;
                        getHourFlow(hour);
                        break;
                    case 1:
                        day = day - 86400;
                        getDayFlow(day);
                        break;
                    case 2:
                        week = week - 604800;
                        getWeekFlow(week);
                        break;
                }
            });
            $("#next").click(function () {
                switch (type) {
                    case 0:
                        hour = hour + 3600;
                        getHourFlow(hour);
                        break;
                    case 1:
                        day = day + 86400;
                        getDayFlow(day);
                        break;
                    case 2:
                        week = week + 604800;
                        getWeekFlow(week);
                        break;
                }
            });

            function getHourFlow(hour) {
                $.post("hour_flow",
                        {
                            port: port,
                            hour: hour
                        },
                        function (data) {
                            var json = JSON.parse(data);
                            var title = "按小时流量使用情况";
                            $("#time_period").text("时间区间：" + json.time_period);
                            drawSheet(title, json.labels, json.flow)
                        });
            }

            function getDayFlow(day) {
                $.post("day_flow",
                        {
                            port: port,
                            day: day
                        },
                        function (data) {
                            var json = JSON.parse(data);
                            var title = "按天流量使用情况";
                            $("#time_period").text("时间区间：" + json.time_period);
                            drawSheet(title, json.labels, json.flow)
                        });
            }

            function getWeekFlow(week) {
                $.post("week_flow",
                        {
                            port: port,
                            week: week
                        },
                        function (data) {
                            var json = JSON.parse(data);
                            var title = "按周流量使用情况";
                            $("#time_period").text("时间区间：" + json.time_period);
                            drawSheet(title, json.labels, json.flow);
                        });
            }

            function drawSheet(title, labels, flows) {
                $("#myChart").remove(); // this is my <canvas> element
                $("#chart_parent").append('<canvas id="myChart" class="chart" width="500" height="250"></canvas>');
                var ctx = document.getElementById("myChart").getContext("2d");
                var data = {
                    labels: labels,
                    datasets: [
                        {
                            fill: true,
                            backgroundColor: "rgba(75,192,192,0.4)",
                            pointColor: "rgba(75,192,192,0.4)",
                            strokeColor: "rgba(75,192,192,0.4)",
                            pointStrokeColor: "#fff",
                            data: flows
                        }
                    ]
                };
                var options = {
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
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: data,
                    options: options
                });
            }

        });
    </script>
@endsection
