@extends('layouts.app')
@section('content')
<script type="text/javascript">
    $(document).ready(function () {
        var originPass = "{{ $password }}";
        var originName = "{{ $name }}";
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        $("#btn_save").click(function () {
            var port = "{{ $port }}";
            var password = $("#user_pass").val();
            var name = $("#name").val();
            if($.isEmptyObject(password)){
                swal("密码不能为空");
                return;
            }
            $.post('update_user',
                    {
                        port:port,
                        name:name,
                        password:password,
                        originName:originName,
                        originPass:originPass
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
        $("#btn_del").click(function(){
            var port = "{{ $port }}";
            swal({
                title: "确定要删除端口号为"+port+"的用户？",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                cancelButtonText:"取消",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            }, function(){
                $.post("del_user",
                        {port:port},
                        function(data){
                            var json = JSON.parse(data);
                            if(json.code == 0){
                                swal(json.msg, "", "success");
                                window.setTimeout(function() {
                                    location.href='user_manage';
                                    $.get(location.href);
                                }, 1500);
                            }else{
                                swal(json.msg, "", "error");
                            }
                        });
            });
        });
    });
</script>
<div class="container">
    <div class="row"> 
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">用户编辑</div>
                <div class="panel-body">
                    <div>
                        <label>端&nbsp; &nbsp;口</label>
                        <input id="user_port" disabled="true" value="{{ $port }}">
                    </div>
                    <div>
                        <label>密&nbsp; &nbsp;码</label>
                        <input id="user_pass" value="{{ $password }}">
                    </div>
                    <div>
                        <label>用户名</label>
                        <input id="name" value="{{ $name }}">
                    </div>
                    <div>

                    </div>
                    <button class="btn btn-default btn_submit" id="btn_del">删除用户</button>
                    <button class="btn btn-default btn_submit" id="btn_save" >保存修改</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection