<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedstock extends Model
{
    //
    protected $hidden = ['created_at', 'updated_at', 'id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'cost', 'stock'];
}
