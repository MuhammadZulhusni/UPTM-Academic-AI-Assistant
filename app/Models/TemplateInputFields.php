<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateInputFields extends Model
{
    protected $guarded = [];

    /**
     * Defines an inverse relationship. An input field belongs to a single template.
     * This method allows to easily access the parent Template model from an input field instance.
     */
    // Template (1:M) TemplateInputFields 
    public function template(){
        return $this->belongsTo(Template::class);
    }    
}