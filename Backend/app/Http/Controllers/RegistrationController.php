<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationConfirmation;

class RegistrationController extends Controller
{
    public function register(Request $request, $workshopId)
    {
        $workshop = Workshop::findOrFail($workshopId);

        if ($workshop->isFullyBooked()) {
            return response()->json([
                'message' => 'Désolé, ce workshop est complet'
            ], 422);
        }

        $exists = Registration::where('user_id', $request->user()->id)
            ->where('workshop_id', $workshopId)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Vous êtes déjà inscrit à ce workshop'
            ], 422);
        }

        $registration = Registration::create([
            'user_id' => $request->user()->id,
            'workshop_id' => $workshopId,
            'registered_at' => now()
        ]);


        return response()->json([
            'message' => 'Inscription réussie!',
            'registration' => $registration
        ], 201);
    }

    public function myRegistrations(Request $request)
    {
        $registrations = Registration::with('workshop')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($registrations);
    }

    public function cancel(Request $request, $workshopId)
    {
        $registration = Registration::where('user_id', $request->user()->id)
            ->where('workshop_id', $workshopId)
            ->firstOrFail();

        $registration->delete();

        return response()->json(['message' => 'Inscription annulée avec succès']);
    }
}
