<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateRequest extends Model
{
    protected $fillable = [
        'certificate_type_id',
        'first_name',
        'last_name',
        'company_name',
        'country',
        'phone',
        'email',
        'notes',
        'certificate_name',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function certificateType()
    {
        return $this->belongsTo(CertificateType::class);
    }
    public function issuedCertificate()
    {
        return $this->hasOne(IssuedCertificate::class);
    }
}
