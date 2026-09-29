<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\BookingRequest;
use App\Models\Notification;
use App\Models\Resource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function stats()
    {
        return response()->json([
            'pending' => BookingRequest::where('status', 'pending')->count(),
            'approved' => BookingRequest::where('status', 'approved')->count(),
            'resources' => Resource::count(),
        ]);
    }

    public function index(Request $request)
    {
        $query = BookingRequest::with(['resource', 'user']);

        $bookings = $query->latest('booking_id')->get();

        $formatted = $bookings->map(fn ($b) => [
            'id' => $b->booking_id,
            'resource' => $b->resource->name ?? 'Unknown Resource',

            // Explicitly separate individual requestor name vs organization/account origin
            'full_name' => $b->full_name && $b->full_name !== ($b->user->name ?? null) ? $b->full_name : '—',
            'requester' => $b->full_name && $b->full_name !== ($b->user->name ?? null) ? $b->full_name : '—',
            'barangay' => $b->user->barangay ?? $b->user->name ?? 'Poblacion',

            'cell_number' => $b->cell_number ?? 'N/A',
            'where_do_you_live' => $b->address ?? 'N/A',
            'address' => $b->address ?? 'N/A',
            'purpose' => $b->purpose ?? 'No purpose provided',
            'date' => Carbon::parse($b->start_date)->toDateString() . ' to ' . Carbon::parse($b->end_date)->toDateString(),
            'time' => ($b->start_time && $b->end_time) ? "{$b->start_time} - {$b->end_time}" : 'All Day',
            'status' => $b->status,
        ]);

        return response()->json($formatted);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resource_id' => ['nullable', 'exists:resources,resource_id'],
            'item_id' => ['nullable', 'exists:resources,resource_id'], // Accepts item_id sent from modals
            'full_name' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'string'],
            'end_time' => ['nullable', 'string'],
            'purpose' => ['nullable', 'string'],
            'cell_number' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
        ]);

        // Resolve resource ID from either key
        $resourceId = $validated['resource_id'] ?? $validated['item_id'] ?? null;

        $booking = BookingRequest::create([
            'user_id' => $request->user()->user_id,
            'resource_id' => $resourceId,
            'full_name' => $validated['full_name'] ?? $request->user()->full_name,
            'purpose' => $validated['purpose'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'cell_number' => $validated['cell_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json($booking->load(['resource', 'user']), 201);
    }

    public function updateStatus(Request $request, BookingRequest $booking)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'remarks' => ['nullable', 'string'],
        ]);

        $booking->update(['status' => $validated['status']]);

        Approval::create([
            'booking_id' => $booking->booking_id,
            'admin_id' => $request->user()->user_id,
            'action' => $validated['status'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        Notification::create([
            'booking_id' => $booking->booking_id,
            'user_id' => $booking->user_id,
            'message' => "Your booking request has been {$validated['status']}.",
            'is_read' => false,
        ]);

        return response()->json($booking);
    }
}
