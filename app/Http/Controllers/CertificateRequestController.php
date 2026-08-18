<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCertificateRequest;
use App\Models\CertificateRequest;
use App\Models\CertificateType;
use App\Http\Requests\UpdateCertificateRequestStatusRequest;
use App\Http\Requests\ConfirmCertificateRequestPaymentRequest;

class CertificateRequestController extends Controller
{
    public function show(int $id)
    {
        $certificateRequest = CertificateRequest::with([
            'certificateType:id,name'
        ])->find($id);

        if (!$certificateRequest) {
            return response()->json([
                'message' => 'Certificate request not found',
            ], 404);
        }

        return response()->json([
            'certificate_request' => $certificateRequest,
        ]);
    }

    public function index()
    {
        $certificateRequests = CertificateRequest::with([
            'certificateType:id,name'
        ])
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json([
            'certificate_requests' => $certificateRequests,
        ]);
    }

    public function store(StoreCertificateRequest $request)
    {
        $validated = $request->validated();

        $certificateType = CertificateType::where('is_active', true)
            ->findOrFail($validated['certificate_type_id']);

        $certificateRequest = CertificateRequest::create([
            'certificate_type_id' => $certificateType->id,

            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'company_name' => $validated['company_name'] ?? null,
            'country' => $validated['country'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'notes' => $validated['notes'] ?? null,

            'certificate_name' => $certificateType->name,
            'amount' => $certificateType->price,
        ]);

        return response()->json([
            'message' => 'Certificate request submitted successfully',
            'certificate_request' => $certificateRequest,
        ], 201);
    }

    public function updateStatus(UpdateCertificateRequestStatusRequest $request, int $id)
    {
        $certificateRequest = CertificateRequest::find($id);

        if (!$certificateRequest) {
            return response()->json([
                'message' => 'Certificate request not found',
            ], 404);
        }

        if ($certificateRequest->order_status !== 'pending') {
            return response()->json([
                'message' => 'Certificate request status has already been decided',
            ], 422);
        }

        $validated = $request->validated();

        $certificateRequest->order_status = $validated['order_status'];

        if (array_key_exists('admin_notes', $validated)) {
            $certificateRequest->admin_notes = $validated['admin_notes'];
        }

        $certificateRequest->save();

        return response()->json([
            'message' => 'Certificate request status updated successfully',
            'certificate_request' => $certificateRequest,
        ]);
    }

    public function confirmPayment(ConfirmCertificateRequestPaymentRequest $request, int $id)
    {
        $certificateRequest = CertificateRequest::find($id);

        if (!$certificateRequest) {
            return response()->json([
                'message' => 'Certificate request not found',
            ], 404);
        }

        if ($certificateRequest->order_status !== 'approved') {
            return response()->json([
                'message' => 'Only approved certificate requests can be marked as paid',
            ], 422);
        }

        if ($certificateRequest->payment_status === 'paid') {
            return response()->json([
                'message' => 'Certificate request payment has already been confirmed',
            ], 422);
        }

        $validated = $request->validated();

        $certificateRequest->payment_status = 'paid';
        $certificateRequest->paid_at = now();

        if (array_key_exists('payment_reference', $validated)) {
            $certificateRequest->payment_reference =
                $validated['payment_reference'];
        }

        $certificateRequest->save();

        return response()->json([
            'message' => 'Certificate request payment confirmed successfully',
            'certificate_request' => $certificateRequest,
        ]);
    }
}
