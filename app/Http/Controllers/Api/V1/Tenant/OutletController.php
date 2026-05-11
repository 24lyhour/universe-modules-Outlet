<?php

namespace Modules\Outlet\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Outlet\Models\Outlet;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tenant-scoped Outlet API.
 *
 * Authenticated dashboard user can list / create / read / update / delete
 * the outlets owned by their tenant. Tenant scoping is enforced by
 * filtering on tenant_type + tenant_id from the authenticated user.
 */
class OutletController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $outlets = Outlet::query()
            ->where('tenant_type', $user->tenant_type)
            ->where('tenant_id', $user->tenant_id)
            ->latest()
            ->paginate((int) $request->input('per_page', 15));

        return response()->json($outlets);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'type_outlet_id' => ['nullable', 'integer'],
            'image_url' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $outlet = Outlet::create(array_merge($validated, [
            'tenant_type' => $user->tenant_type,
            'tenant_id' => $user->tenant_id,
            'status' => $validated['status'] ?? 'active',
        ]));

        return response()->json(['data' => $outlet], Response::HTTP_CREATED);
    }

    public function show(Request $request, Outlet $outlet): JsonResponse
    {
        $this->assertOwner($request, $outlet);

        return response()->json(['data' => $outlet]);
    }

    public function update(Request $request, Outlet $outlet): JsonResponse
    {
        $this->assertOwner($request, $outlet);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'type_outlet_id' => ['nullable', 'integer'],
            'image_url' => ['nullable', 'string'],
            'logo' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'status' => ['sometimes', 'string', 'in:active,inactive'],
        ]);

        $outlet->update($validated);

        return response()->json(['data' => $outlet->fresh()]);
    }

    public function destroy(Request $request, Outlet $outlet): JsonResponse
    {
        $this->assertOwner($request, $outlet);

        $outlet->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * 404 (not 403) when an outlet exists but isn't owned by the caller —
     * leaks no info that "this resource exists but you can't see it".
     */
    protected function assertOwner(Request $request, Outlet $outlet): void
    {
        $user = $request->user();
        if ($outlet->tenant_type !== $user->tenant_type
            || $outlet->tenant_id !== $user->tenant_id) {
            abort(Response::HTTP_NOT_FOUND);
        }
    }
}
