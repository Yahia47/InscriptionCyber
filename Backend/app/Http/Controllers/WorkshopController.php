<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WorkshopNotification;

class WorkshopController extends Controller
{
    public function index()
    {
        $workshops = Workshop::withCount('registrations')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get()
            ->map(function ($workshop) {
                $workshop->remaining_seats = $workshop->remaining_seats;
                $workshop->is_full = $workshop->isFullyBooked();
                return $workshop;
            });

        return response()->json($workshops);
    }

    public function show($id)
    {
        $workshop = Workshop::withCount('registrations')->findOrFail($id);
        $workshop->remaining_seats = $workshop->remaining_seats;
        $workshop->is_full = $workshop->isFullyBooked();

        return response()->json($workshop);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructor' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'max_participants' => 'required|integer|min:1'
        ]);

        $workshop = Workshop::create($request->all());

        return response()->json($workshop, 201);
    }

    public function update(Request $request, $id)
    {
        $workshop = Workshop::findOrFail($id);

        $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'instructor' => 'string|max:255',
            'date' => 'date|after_or_equal:today',
            'time' => 'date_format:H:i',
            'location' => 'string|max:255',
            'max_participants' => 'integer|min:1'
        ]);

        $workshop->update($request->all());

        return response()->json($workshop);
    }

    public function destroy($id)
    {
        $workshop = Workshop::findOrFail($id);
        $workshop->delete();

        return response()->json(['message' => 'Workshop supprimé avec succès']);
    }

    public function registrations($id)
    {
        $workshop = Workshop::with(['registrations.user' => function ($query) {
            $query->select('id', 'name', 'email');
        }])->findOrFail($id);

        return response()->json([
            'workshop' => $workshop->only(['id', 'title', 'date', 'time']),
            'registrations' => $workshop->registrations
        ]);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        $workshop = Workshop::with('registrations.user')->findOrFail($id);

        foreach ($workshop->registrations as $registration) {
            Mail::to($registration->user->email)->send(
                new WorkshopNotification(
                    $workshop,
                    $request->subject,
                    $request->message
                )
            );
        }

        return response()->json([
            'message' => 'Messages envoyés avec succès',
            'sent_to' => $workshop->registrations->count()
        ]);
    }
}
