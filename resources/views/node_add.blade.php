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
                $.post('store',
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
    <div class="container">
        <div class="row"> 
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">添加节点</div>
                    <div class="panel-body">
                        {{-- <form class="form-horizontal" role="form"> --}}
                            <div class="form-group">
                              <label class="col-md-4 control-label">节点名称：</label>
                              <div class="col-md-6">
                                  <input class="form-control" id="name" placeholder="节点名称">
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-md-4 control-label">节点地址：</label>
                              <div class="col-md-6">
                                  <input class="form-control" id="address" placeholder="节点地址">
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-md-4 control-label">端口：</label>
                              <div class="col-md-6">
                                  <input class="form-control" id="port" placeholder="该节点SSServer的端口">
                              </div>
                            </div>
                            <div class="form-group">
                              <div class="col-md-6 col-md-offset-4">
                                <button id="btn1" class="btn btn-primary login-btn">新增</button>
                              </div>
                            </div>
                            {{-- <div class="form-group">
                              <div class="col-md-6 col-md-offset-4">
                                <span class="help-block login-error-message" style="color: #FF0000; font-weight: bolder; display: none;"></span>
                              </div>
                            </div> --}}
                      {{-- </form> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection