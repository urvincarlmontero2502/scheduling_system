<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Select only necessary columns to minimize payload:
            // resource_id, name, type, status, image, description
            $columns = ['resource_id', 'name', 'type', 'status', 'image'];

            // Check if description column exists before selecting it
            if (Schema::hasColumn('resources', 'description')) {
                $columns[] = 'description';
            }

            $query = Resource::query()->select($columns);

            if ($request->has('type')) {
                $query->where('type', $request->query('type'));
            }

            $isFacility = $request->query('type') === 'facility';
            $isVehicle  = $request->query('type') === 'vehicle';

            // Only join detail tables if they exist
            if (!$request->has('type') || $isFacility) {
                if (Schema::hasTable('facility_details')) {
                    $query->leftJoin('facility_details', 'facility_details.resource_id', '=', 'resources.resource_id')
                           ->addSelect('facility_details.capacity as facility_capacity');
                }
            }
            if (!$request->has('type') || $isVehicle) {
                if (Schema::hasTable('vehicle_details')) {
                    $query->leftJoin('vehicle_details', 'vehicle_details.resource_id', '=', 'resources.resource_id')
                           ->addSelect('vehicle_details.unit_name as vehicle_unit_name');
                }
            }

            $resources = $query->get()->map(function ($r) {
                return [
                    'id' => $r->resource_id,
                    'resource_id' => $r->resource_id,
                    'name' => $r->name,
                    'type' => $r->type,
                    'image' => $r->image ? Storage::disk('assets')->url($r->image) : null,
                    'image_url' => $r->image ? Storage::disk('assets')->url($r->image) : null,
                    'available' => $r->status === 'available',
                    'status' => $r->status,
                    'is_maintenance' => $r->status === 'maintenance',
                    'description' => $r->description ?? null,
                    'capacity' => $r->type === 'facility' && $r->facility_capacity
                        ? $r->facility_capacity . ' pax'
                        : null,
                    'unit_name' => $r->type === 'vehicle' ? ($r->vehicle_unit_name ?? null) : null,
                ];
            });

            return response()->json($resources);
        } catch (\Throwable $e) {
            // Always return the actual error message for diagnostics
            $message = $e->getMessage() . ' | File: ' . $e->getFile() . ':' . $e->getLine();
            return response()->json(['error' => $message], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:facility,vehicle'],
            'description' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer'], // Specific to facilities
            'unit_name' => ['nullable', 'string'], // Specific to vehicles
            'image' => ['nullable', 'image', 'max:2048'], // Optional image upload validation
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $subdir = $validated['type'] === 'facility' ? 'facilities' : 'vehicles';
            $filename = \Illuminate\Support\Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension();
            $imagePath = $request->file('image')->storeAs($subdir, $filename, 'assets');
        }

        // Create the parent resource record
        $resource = Resource::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'status' => 'available',
        ]);

        // Create the corresponding details row based on resource type
        if ($validated['type'] === 'facility') {
            $resource->facilityDetails()->create([
                'capacity' => $validated['capacity'] ?? null,
            ]);
        } else {
            $resource->vehicleDetails()->create([
                'unit_name' => $validated['unit_name'] ?? null,
            ]);
        }

        return response()->json($resource->load(['facilityDetails', 'vehicleDetails']), 201);
    }

    public function update(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:facility,vehicle'],
            'description' => ['nullable', 'string'],
            'capacity' => ['nullable', 'string'],
            'status' => ['nullable', 'in:available,unavailable,maintenance'],
            'image' => ['nullable', 'image', 'max:2048'], // Optional image upload on update
        ]);

        $imagePath = $resource->image;
        if ($request->hasFile('image')) {
            // Delete old image if a new one is uploaded
            if ($resource->image) {
                Storage::disk('assets')->delete($resource->image);
            }
            $subdir = $validated['type'] === 'facility' ? 'facilities' : 'vehicles';
            $filename = \Illuminate\Support\Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension();
            $imagePath = $request->file('image')->storeAs($subdir, $filename, 'assets');
        }

        $updateData = [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
        ];

        if (isset($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }

        $resource->update($updateData);

        if ($validated['type'] === 'facility') {
            $resource->facilityDetails()->updateOrCreate(
                ['resource_id' => $resource->resource_id],
                ['capacity' => $validated['capacity'] ?? null]
            );
        } else {
            $resource->vehicleDetails()->updateOrCreate(
                ['resource_id' => $resource->resource_id],
                ['unit_name' => $validated['capacity'] ?? null]
            );
        }

        return response()->json($resource->load(['facilityDetails', 'vehicleDetails']));
    }

    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);

        // Delete image file from storage if it exists
        if ($resource->image) {
            Storage::disk('assets')->delete($resource->image);
        }

        // Cascade deletes child details automatically if configured in DB,
        // otherwise delete them explicitly:
        $resource->facilityDetails()->delete();
        $resource->vehicleDetails()->delete();

        $resource->delete();

        return response()->json(['message' => 'Resource deleted successfully']);
    }

    public function setMaintenance($id)
    {
        $resource = Resource::findOrFail($id);

        $resource->update(['status' => 'maintenance']);

        return response()->json([
            'message' => "{$resource->name} is now marked for maintenance.",
            'resource' => $resource,
        ]);
    }

    public function setAvailable($id)
    {
        $resource = Resource::findOrFail($id);

        $resource->update(['status' => 'available']);

        return response()->json([
            'message' => "{$resource->name} is now available.",
            'resource' => $resource,
        ]);
    }
}
