<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentimientoAdjunto extends Model
{
    use HasFactory;

    protected $table = 'consentimiento_adjuntos';

    protected $fillable = [
        'consentimiento_id',
        'archivo',
        'tipo'
    ];

    public function consentimiento()
    {
        return $this->belongsTo(
            Consentimiento::class,
            'consentimiento_id'
        );
    }
}