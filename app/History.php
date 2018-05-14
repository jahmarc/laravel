<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{

    protected $table = 'history';
    //Fill the model

    protected $fillable = [
        'survey1', 'survey2', 'survey3', 'userID'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}
