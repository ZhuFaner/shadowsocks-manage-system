@extends('layouts.app')
@section('content')
    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#btn1").click(function () {
                var port_test = /^\+?[1-9][0-9]*$/;
                var port = $("#user_port").val();
                var password = $("#user_pass").val();
                var name = $("#name").val();
                if($.isEmptyObject(port)){
                    swal("端口号不能为空");
                    return;
                }
                if($.isEmptyObject(password)){
                    swal("密码不能为空");
                    return;
                }
                if(!port_test.test(port)){
                    swal("端口号不合法");
                    return;
                }
                $.post('add',
                        {
                            port:port,
                            name:name,
                            password:password,
                        },
                        function(data){
                            var json = JSON.parse(data);
                            if(json.code == 0){
                                swal("",json.msg, "success");
                                window.setTimeout(function() {
                                    location.href='user_manage';
                                    $.get(location.href);
                                }, 1500);
                            }else{
                                swal("", json.msg, "error");
                            }
                        });
            });

        });
    </script>

    <div class="container">
        <div class="row"> 
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        添加用户
                    </div>
                    <div class="panel-body">
                        <div>
                            <label>端&nbsp &nbsp口</label>
                            <input id="user_port">
                        </div>
                        <div>
                            <label>密&nbsp &nbsp码</label>
                            <input id="user_pass" type="password">
                        </div>
                        <div>
                            <label>用户名</label>
                            <input id="name">
                        </div>
                        <div>
                            <button class="btn btn-default btn_submit" id="btn1" >确定</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection