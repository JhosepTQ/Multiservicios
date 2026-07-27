<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'html_template', 'css_styles', 'company_name', 'company_logo', 'company_info', 'footer_text', 'active', 'is_default', 'created_by'];

    protected $casts = [
        'active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
