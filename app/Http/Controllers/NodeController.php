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
      $nodeArray = Node::allNodes(true);
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
        if ($exist_node->valid) {
          $array['msg'] = '请勿添加相同节点';
          $array['code'] = 1;
        }else{
          $exist_node->name = $node_name;
          $exist_node->node_address = $node_address;
          $exist_node->node_port = $node_port;
          $exist_node->valid = true;
          $exist_node->save();
          $array['msg'] = '添加成功';  
        }
      }else{
        $array['msg'] = '添加成功';
        Node::create(['name' => $node_name,'node_address' => $node_address,'node_port' => $node_port, 'valid' => 1]);
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
      if (Node::where('node_address', $request->get('address'))->where('id','!=',$id)->where('valid', 1)->first()) {
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
      $node = Node::find($id);
      $node->valid = 0;
      $node->save();
      $array = array(
            'code' => 0,
            'msg' => '删除成功');
      return $array;
    }
}
