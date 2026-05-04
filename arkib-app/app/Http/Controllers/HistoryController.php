<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        $histories = History::with(['user', 'fakultiBahagian'])
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('history.index', compact('histories'));
    }

    public function destroy(History $history): RedirectResponse
    {
        $history->delete();
        return redirect()->route('history.index')->with('success', 'Rekod history berjaya dipadam.');
    }
}
