@extends('layouts.app')
@section('content')

    <div class="container">
        <div class="row"> 
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        节点管理
                        <a href="{{ url('/node/create') }}" style="float: right">添加节点</a>
                    </div>
                    <div class="panel-body">
                        @if(count($nodeArray) > 0)
                            <ol>
                                @foreach($nodeArray as $node)
                                    <li class="li_user">
                                        <div class="list_item">
                                            名称：{{ empty($node->name) ? '无' : $node->name }}
                                        </div>
                                        <div class="list_item">地址：{{ $node->node_address }}</div>
                                        <div class="list_item">端口：{{ $node->node_port }}</div>
                                        <div>
                                            <button class="btn btn-default btn_common"
                                                    onclick="location.href='/node/detail/{{ $node->id }}'">
                                                节点详情
                                            </button>
                                            <button class="btn btn-default btn_common"
                                                    onclick="location.href='{{ url('node/edit/'.$node->id) }}'">
                                                节点编辑
                                            </button>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection