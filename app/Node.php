<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
  protected $fillable = ['name','node_address','node_port', 'valid'];

  public static function allNodes()
  { 
    return Node::orderBy('id', 'desc')->get();
  }

  public static function first()
  {
    return Node::orderBy('id', 'desc')->where('valid', true)->first();
  }
}
