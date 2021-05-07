<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shoe extends Model
{
    //
    protected $primaryKey = 'id';
    protected $table = 'shoes';

    // Esconde alguns campos/colunas da DB do model, ou seja, podemos mais descansadamente passar models inteiros para json e mandá-los cá para fora numa response()->json() sem perigo que campos que devem estar escondidos sejam transmitidos.
    protected $hidden = ['created_at', 'updated_at', 'id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['reference', 'barcode', 'color', 'stock'];



    public function getFeedstockIds(){
        $feedstocksList = $this->feedstocks()->orderBy('name')->get();

        $feedArray = [];
        foreach ($feedstocksList as $feedstock){
            array_push($feedArray, $feedstock->id);
        }
        return $feedArray;
    }

    public function feedstocks()
    {
        return $this->belongsToMany('App\Feedstock', 'relations', 'shoes_id', 'feedstock_id');
    }


    public function cost()
    {
        $feedstocks = $this->feedstocks()->orderBy('name')->get();

        $shoeCost = 0;
        foreach($feedstocks as $feedstock){
            $shoeCost += $feedstock->cost;
        }

        return $shoeCost;
    }

}
