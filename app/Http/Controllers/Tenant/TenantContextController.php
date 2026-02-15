<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantContextController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'tenant' => $request->attributes->get('tenant'),
        ]);
    }
}
