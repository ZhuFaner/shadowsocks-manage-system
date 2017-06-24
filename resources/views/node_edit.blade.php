@extends('layouts.app')
@section('content')

    <div class="container">
        <div class="row"> 
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        节点编辑
                        <a id="delete" style="float: right">删除节点</a>
                    </div>
                    <div class="panel-body">
                        <div class="form-group" style="height: 44px">
                              <label class="col-md-4 control-label">节点名称：</label>
                              <div class="col-md-6">
                                  <input class="form-control" id="name" value="{{$node->name}}" placeholder="节点名称">
                              </div>
                            </div>
                            <div class="form-group" style="height: 44px">
                              <label class="col-md-4 control-label">节点地址：</label>
                              <div class="col-md-6">
                                  <input class="form-control" id="address" value="{{$node->node_address}}" placeholder="节点地址">
                              </div>
                            </div>
                            <div class="form-group" style="height: 44px">
                              <label class="col-md-4 control-label">端口：</label>
                              <div class="col-md-6">
                                  <input class="form-control" id="port" value="{{$node->node_port}}" placeholder="该节点SSServer的端口">
                              </div>
                            </div>
                            <div class="form-group" style="height: 44px">
                              <div class="col-md-6 col-md-offset-4">
                                <button id="btn1" class="btn btn-primary login-btn">保存</button>
                              </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#delete").click(function () {
                swal({
                  title: "确定要删除此节点?",
                  type: "error",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "确定",
                  cancelButtonText: "取消",
                  closeOnConfirm: false,
                  html: false
                }, function(){
                    $.post("{{ url('node/delete/'.$node->id) }}", function (data) {
                        if(data.code == 0){
                            swal("",data.msg, "success");
                            window.setTimeout(function() {
                                location.href= "{{ url('node/list') }}";
                                $.get(location.href);
                            }, 1500);
                        }else{
                            swal("", data.msg, "error");
                        }
                    });
                });
            })

            $("#btn1").click(function () {
                var port_test = /^\+?[1-9][0-9]*$/;
                var port = $("#port").val();
                var address = $("#address").val();
                var name = $("#name").val();
                if($.isEmptyObject(port)){
                    swal("端口号不能为空");
                    return;
                }
                if($.isEmptyObject(address)){
                    swal("地址不能为空");
                    return;
                }
                if(!port_test.test(port)){
                    swal("端口号不合法");
                    return;
                }
                $.post("{{ url('node/update/'.$node->id) }}",
                        {
                            port:port,
                            name:name,
                            address:address,
                        },
                        function(data){

                            if(data.code == 0){
                                swal("",data.msg, "success");
                                window.setTimeout(function() {
                                    location.href= "{{ url('node/list') }}";
                                    $.get(location.href);
                                }, 1500);
                            }else{
                                swal("", data.msg, "error");
                            }
                        });
            });
        });
    </script>
@endsection