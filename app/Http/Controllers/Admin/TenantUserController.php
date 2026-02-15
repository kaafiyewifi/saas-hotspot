<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TenantUserController extends Controller
{
    public function index(Tenant $tenant): JsonResponse
    {
        $users = $tenant->users()
            ->select('users.id', 'users.name', 'users.email')
            ->withPivot(['role', 'status'])
            ->get();

        return response()->json([
            'tenant_id' => $tenant->id,
            'users' => $users,
        ]);
    }

    public function store(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'name' => ['required_without:user_id', 'string', 'max:255'],
            'email' => ['required_without:user_id', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['owner', 'admin', 'support', 'readonly'])],
            'status' => ['required', Rule::in(['active', 'suspended'])],
        ]);

        if (isset($validated['user_id'])) {
            $user = User::query()->findOrFail($validated['user_id']);
        } else {
            $user = User::query()->firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['name'],
                    'password' => Hash::make($validated['password'] ?? 'password'),
                ]
            );
        }

        $tenant->users()->syncWithoutDetaching(
            [
                $user->id => [
                    'role' => $validated['role'],
                    'status' => $validated['status'],
                ],
            ]
        );

        return response()->json([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'role' => $validated['role'],
            'status' => $validated['status'],
        ], 201);
    }
}
