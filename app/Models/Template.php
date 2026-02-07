<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{

    use SoftDeletes;
    
    protected $guarded = [];

    /**
     * Defines a relationship where a Template belongs to a User.
     * The 'created_by' column in the templates table is used as the foreign key.
     */
    // User (1:M) Template 
    public function ceratedBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Defines a relationship where a Template has many TemplateInputFields.
     * The 'template_id' column in the template_input_fields table is the foreign key.
     */
    // Template (1:M) TemplateInputFields 
    public function inputFields(){
        return $this->hasMany(TemplateInputFields::class, 'template_id');
    }

    public function generatedContents()
    {
        return $this->hasMany(GeneratedContent::class);
    }

    /**
     * Generate content by replacing placeholders
     * Example: "Hello {name}" = "Hello Ali"
     *
     * @param array $inputData
     * @return string
     */
    public function generateContent(array $inputData): string
    {
        $content = $this->prompt ?? '';

        foreach ($inputData as $key => $value) {
            $content = str_replace("{" . $key . "}", $value, $content);
        }

        return $content;
    }
}