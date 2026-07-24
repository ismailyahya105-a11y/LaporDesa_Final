<?php

namespace App\Http\Controllers;

use App\Models\Polling;
use App\Models\PollingOption;
use App\Models\PollingVote;
use Illuminate\Http\Request;

class PollingController extends Controller
{
    public function index()
    {
        return view('smart.polling', ['items' => Polling::with(['options.votes'])->where('aktif', true)->latest()->get()]);
    }

    public function vote(Request $request, Polling $polling)
    {
        abort_unless(
            $polling->aktif && ($polling->berakhir_pada === null || $polling->berakhir_pada->isFuture()),
            422,
            'Polling ini sudah tidak menerima suara.'
        );

        $data = $request->validate(['polling_option_id' => 'required|integer|exists:polling_option,id']);
        abort_unless(PollingOption::whereKey($data['polling_option_id'])->where('polling_id', $polling->id)->exists(), 422);
        PollingVote::updateOrCreate(['polling_id' => $polling->id, 'user_id' => $request->user()->id], ['polling_option_id' => $data['polling_option_id']]);

        return back()->with('success', 'Pilihan Anda berhasil disimpan.');
    }
}
