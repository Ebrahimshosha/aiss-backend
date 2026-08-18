<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssuedCertificate extends Model
{
    protected $fillable = [
        'certificate_request_id',
        'certificate_type_id',
        'code',
        'certificate_name',
        'holder_name',
        'issue_date',
        'expiry_date',
        'status',
        'file_path',
    ];

    protected $appends = [
        'effective_status',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date:Y-m-d',
            'expiry_date' => 'date:Y-m-d',
        ];
    }

    public function certificateRequest()
    {
        return $this->belongsTo(CertificateRequest::class);
    }

    public function certificateType()
    {
        return $this->belongsTo(CertificateType::class);
    }

    public function getEffectiveStatusAttribute(): string
    {
        if ($this->status === 'revoked') {
            return 'revoked';
        }

        if (
            $this->expiry_date &&
            $this->expiry_date->isPast()
        ) {
            return 'expired';
        }

        return $this->status;
    }
}
