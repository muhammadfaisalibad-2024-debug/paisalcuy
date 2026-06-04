<?php

namespace App\Http\Controllers;

use App\Models\NfcCard;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NfcController extends Controller
{
    public function scannerView()
    {
        return view('nfc.scanner');
    }

    public function registerCard(Request $request)
    {
        $validated = $request->validate([
            'serial' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
        ]);

        $card = NfcCard::updateOrCreate([
            'serial' => $validated['serial'],
        ], [
            'owner_name' => $validated['owner_name'] ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $card]);
    }

    public function recordAttendance(Request $request)
    {
        $validated = $request->validate([
            'serial' => 'required|string|max:255',
            'note' => 'nullable|string',
        ]);

        $serial = $validated['serial'];
        $card = NfcCard::where('serial', $serial)->first();

        $attendance = Attendance::create([
            'nfc_card_id' => $card ? $card->id : null,
            'serial' => $serial,
            'status' => 'present',
            'note' => $validated['note'] ?? null,
            'scanned_at' => now(),
        ]);

        Log::info('NFC attendance recorded', ['serial' => $serial, 'card_id' => $card->id ?? null]);

        return response()->json([
            'success' => true,
            'data' => [
                'attendance' => $attendance,
                'card' => $card,
            ],
        ]);
    }

    public function attendanceList()
    {
        $rows = Attendance::with('nfcCard')->orderByDesc('scanned_at')->paginate(50);
        return view('nfc.attendance-list', compact('rows'));
    }
}
