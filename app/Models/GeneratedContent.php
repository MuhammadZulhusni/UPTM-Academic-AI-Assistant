<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedContent extends Model
{
    protected $guarded = [];

    // GeneratedContent (M:1) User 
    public function user(){
        return $this->belongsTo(User::class);
    }

    // Template (1:M) GeneratedContents 
    public function template(){
        return $this->belongsTo(Template::class);
    }
 

}