<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormatoConsentimiento extends Model
{
    use HasFactory;

    protected $table = 'formatos_consentimiento';

    protected $fillable = [
        'nombre',
        'descripcion',
        'contenido',
        'requiere_firma',
        'activo'
    ];

    protected $casts = [
        'requiere_firma' => 'boolean',
        'activo' => 'boolean'
    ];

    public function consentimientos()
    {
        return $this->hasMany(Consentimiento::class, 'formato_id');
    }
}