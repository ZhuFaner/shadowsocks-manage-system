<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
  protected $fillable = ['name','node_address','node_port'];
}
