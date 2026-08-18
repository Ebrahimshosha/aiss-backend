<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCertificateTypeRequest;
use App\Models\CertificateType;
use App\Http\Requests\UpdateCertificateTypeRequest;

class CertificateTypeController extends Controller
{
    public function index()
    {
        $certificateTypes = CertificateType::where('is_active', true)
            ->orderBy('id')
            ->get([
                'id',
                'name',
                'price',
                'description',
            ]);

        return response()->json([
            'certificate_types' => $certificateTypes,
        ]);
    }

    public function store(StoreCertificateTypeRequest $request)
    {
        $certificateType = CertificateType::create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Certificate type created successfully',
            'certificate_type' => $certificateType,
        ], 201);
    }

    public function show($id)
    {
        $certificateType = CertificateType::where('is_active', true)
            ->find($id);

        if (!$certificateType) {
            return response()->json([
                'message' => 'Certificate type not found',
            ], 404);
        }

        return response()->json([
            'certificate_type' => $certificateType,
        ]);
    }

    public function update(UpdateCertificateTypeRequest $request, $id)
    {
        $certificateType = CertificateType::find($id);

        if (!$certificateType) {
            return response()->json([
                'message' => 'Certificate type not found',
            ], 404);
        }

        $certificateType->update(
            $request->validated()
        );

        return response()->json([
            'message' => 'Certificate type updated successfully',
            'certificate_type' => $certificateType->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $certificateType = CertificateType::find($id);

        if (!$certificateType) {
            return response()->json([
                'message' => 'Certificate type not found',
            ], 404);
        }

        $certificateType->delete();

        return response()->json([
            'message' => 'Certificate type deleted successfully',
        ]);
    }

    public function adminIndex()
    {
        $certificateTypes = CertificateType::orderByDesc('id')
            ->get();

        return response()->json([
            'certificate_types' => $certificateTypes,
        ]);
    }
}
