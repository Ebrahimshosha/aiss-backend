<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateType extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function certificateRequests()
    {
        return $this->hasMany(CertificateRequest::class);
    }

    public function issuedCertificates()
    {
        return $this->hasMany(IssuedCertificate::class);
    }
}
