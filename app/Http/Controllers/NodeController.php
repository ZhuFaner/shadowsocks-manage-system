<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Node;

class NodeController extends Controller
{

  function __construct()
  {
    $this->middleware('auth');
  }
    public function index()
    {
      $nodeArray = Node::allNodes();
      return view('node_manage')->with('nodeArray', $nodeArray);
    }

    public function detail($id)
    {
      return view('node_detail')->with('node', Node::find($id));
    }

    public function create()
    {
      return view('node_add');
    }

    public function store(Request $request)
    {
      $array = array(
            'code' => 0,
            'msg' => '');
      $node_name = $request->get('name');
      $node_address = $request->get('address');
      $node_port = $request->get('port');
      $exist_node = Node::Where('node_address', $node_address)->first(); 
      if ($exist_node) {
        $array['msg'] = '请勿添加相同节点';
        $array['code'] = 1;
      }else{
        $array['msg'] = '添加成功';
        Node::create(['name' => $node_name,'node_address' => $node_address,'node_port' => $node_port]);
      }
      return $array;
    }

    public function edit($id)
    {
      $node = Node::find($id);
      return view('node_edit')->withNode($node);
    }

    public function update(Request $request, $id)
    {
      $array = array(
            'code' => 0,
            'msg' => '保存成功');
      if (Node::where('node_address', $request->get('address'))->where('id','!=',$id)->first()) {
        $array = array(
            'code' => 1,
            'msg' => '请勿添加相同节点');
        return $array;
      }
      $node = Node::find($id);
      $node->name = $request->get('name');
      $node->node_address = $request->get('address');
      $node->node_port = $request->get('port');
      $node->save();
      return $array;
    }

    public function delete($id)
    {
      Node::destroy($id);
      $array = array(
            'code' => 0,
            'msg' => '删除成功');
      return $array;
    }
}
