<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $action = $request->query('action');
        $status = $request->query('status');

        $entries = LedgerEntry::query()
            ->with('actor', 'digitalIdentity.user')
            ->when($action, fn ($query) => $query->where('action', $action))
            ->when($status, fn ($query) => $query->whereHas('digitalIdentity', fn ($identityQuery) => $identityQuery->where('status', $status)))
            ->latest('recorded_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.audit.index', [
            'entries' => $entries,
            'actions' => LedgerEntry::query()->select('action')->distinct()->orderBy('action')->pluck('action'),
            'selectedAction' => $action,
            'selectedStatus' => $status,
            'totalEntries' => LedgerEntry::count(),
            'adminActions' => LedgerEntry::whereNotNull('actor_id')->count(),
        ]);
    }
}
