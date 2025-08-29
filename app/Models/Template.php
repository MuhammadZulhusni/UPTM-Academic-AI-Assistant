<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $guarded = [];

    /**
     * Defines a relationship where a Template belongs to a User.
     * The 'created_by' column in the templates table is used as the foreign key.
     */
    public function ceratedBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Defines a relationship where a Template has many TemplateInputFields.
     * The 'template_id' column in the template_input_fields table is the foreign key.
     */
    public function inputFields(){
        return $this->hasMany(TemplateInputFields::class, 'template_id');
    }
}