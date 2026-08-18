<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssuedCertificateRequest;
use App\Models\CertificateRequest;
use App\Models\IssuedCertificate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateIssuedCertificateRequest;

class IssuedCertificateController extends Controller
{
    public function store(StoreIssuedCertificateRequest $request,int $id)
    {
        $certificateRequest = CertificateRequest::find($id);

        if (!$certificateRequest) {
            return response()->json([
                'message' => 'Certificate request not found',
            ], 404);
        }

        if ($certificateRequest->order_status !== 'approved') {
            return response()->json([
                'message' => 'Certificate request must be approved before issuing a certificate',
            ], 422);
        }

        if ($certificateRequest->payment_status !== 'paid') {
            return response()->json([
                'message' => 'Certificate request must be paid before issuing a certificate',
            ], 422);
        }

        if (
            IssuedCertificate::where(
                'certificate_request_id',
                $certificateRequest->id
            )->exists()
        ) {
            return response()->json([
                'message' => 'A certificate has already been issued for this request',
            ], 422);
        }

        $validated = $request->validated();

        $filePath = null;

        try {
            $filePath = str_replace(
                '\\',
                '/',
                $request->file('certificate_pdf')
                    ->store('certificates', 'public')
            );

            $issuedCertificate = DB::transaction(
                function () use (
                    $certificateRequest,
                    $validated,
                    $filePath
                ) {
                    $issuedCertificate = IssuedCertificate::create([
                        'certificate_request_id' => $certificateRequest->id,
                        'certificate_type_id' => $certificateRequest->certificate_type_id,

                        'code' => $validated['code'],

                        'certificate_name' => $certificateRequest->certificate_name,
                        'holder_name' => $validated['holder_name'],

                        'issue_date' => $validated['issue_date'],
                        'expiry_date' => $validated['expiry_date'] ?? null,

                        'status' => $validated['status'] ?? 'active',

                        'file_path' => $filePath,
                    ]);

                    $certificateRequest->order_status = 'completed';
                    $certificateRequest->save();

                    return $issuedCertificate;
                }
            );
        } catch (\Throwable $e) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }

            throw $e;
        }

        $issuedCertificate->load([
            'certificateRequest',
            'certificateType:id,name',
        ]);

        return response()->json([
            'message' => 'Certificate issued successfully',
            'certificate' => $issuedCertificate,
        ], 201);
    }

    public function index()
    {
        $certificates = IssuedCertificate::with([
            'certificateType:id,name',
            'certificateRequest:id,first_name,last_name,email,phone',
        ])
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json([
            'certificates' => $certificates,
        ]);
    }

    public function show(int $id)
    {
        $certificate = IssuedCertificate::with([
            'certificateType:id,name',
            'certificateRequest:id,first_name,last_name,email,phone',
        ])->find($id);

        if (!$certificate) {
            return response()->json([
                'message' => 'Certificate not found',
            ], 404);
        }

        return response()->json([
            'certificate' => $certificate,
        ]);
    }

    public function verify(string $code)
    {
        $certificate = IssuedCertificate::with([
            'certificateType:id,name',
        ])
            ->where('code', $code)
            ->first();

        if (!$certificate) {
            return response()->json([
                'message' => 'Certificate not found',
            ], 404);
        }
        /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');
        return response()->json([
            'certificate' => [
                'code' => $certificate->code,
                'certificate_name' => $certificate->certificate_name,
                'holder_name' => $certificate->holder_name,
                'issue_date' => $certificate->issue_date,
                'expiry_date' => $certificate->expiry_date,
                'status' => $certificate->status,
                // 'file_url' => Storage::disk('public')->url($certificate->file_path),
                'file_url' => $publicDisk->url($certificate->file_path),
            ],
        ]);
    }

    public function update(UpdateIssuedCertificateRequest $request, int $id)
    {
        $certificate = IssuedCertificate::find($id);

        if (!$certificate) {
            return response()->json([
                'message' => 'Certificate not found',
            ], 404);
        }

        $validated = $request->validated();

        $oldFilePath = str_replace('\\', '/', $certificate->file_path);
        $newFilePath = null;

        try {
            if ($request->hasFile('certificate_pdf')) {
                $newFilePath = str_replace(
                    '\\',
                    '/',
                    $request->file('certificate_pdf')
                        ->store('certificates', 'public')
                );
            }

            DB::transaction(function () use (
                $certificate,
                $validated,
                $newFilePath
            ) {
                if (array_key_exists('code', $validated)) {
                    $certificate->code = $validated['code'];
                }

                if (array_key_exists('holder_name', $validated)) {
                    $certificate->holder_name = $validated['holder_name'];
                }

                if (array_key_exists('issue_date', $validated)) {
                    $certificate->issue_date = $validated['issue_date'];
                }

                if (array_key_exists('expiry_date', $validated)) {
                    $certificate->expiry_date = $validated['expiry_date'];
                }

                if (array_key_exists('status', $validated)) {
                    $certificate->status = $validated['status'];
                }

                if ($newFilePath) {
                    $certificate->file_path = $newFilePath;
                }

                if (
                    $certificate->expiry_date &&
                    $certificate->issue_date &&
                    $certificate->expiry_date->lt($certificate->issue_date)
                ) {
                    throw new \InvalidArgumentException(
                        'Expiry date cannot be before issue date'
                    );
                }

                $certificate->save();
            });
        } catch (\Throwable $e) {
            if ($newFilePath) {
                Storage::disk('public')->delete($newFilePath);
            }

            if ($e instanceof \InvalidArgumentException) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }

            throw $e;
        }

        if (
            $newFilePath &&
            $oldFilePath &&
            str_starts_with($oldFilePath, 'certificates/')
        ) {
            Storage::disk('public')->delete($oldFilePath);
        }

        return response()->json([
            'message' => 'Certificate updated successfully',
            'certificate' => $certificate->fresh(),
        ]);
    }
}
