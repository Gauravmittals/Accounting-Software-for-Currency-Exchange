<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashIn;
use App\Models\CashOut;


class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $type      = $request->input('type');

        // Initialize the records collection
        $records = collect();

        // Query CashIn if 'cash_in' type or all
        if ($type === 'cash_in' || !$type) {
            $cashInQuery = CashIn::query();
            if ($startDate && $endDate) {
                $cashInQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $cashInRecords = $cashInQuery->get()->map(function ($item) {
                $item->entry_type = 'Cash In';
                return $item;
            });

            $records = $records->merge($cashInRecords);
        }

        // Query CashOut if 'cash_out' type or all
        if ($type === 'cash_out' || !$type) {
            $cashOutQuery = CashOut::query();
            if ($startDate && $endDate) {
                $cashOutQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $cashOutRecords = $cashOutQuery->get()->map(function ($item) {
                $item->entry_type = 'Cash Out';
                return $item;
            });

            $records = $records->merge($cashOutRecords);
        }

        // If no filters, both CashIn and CashOut data should be shown
        if (!$type) {
            // Query for both CashIn and CashOut and merge them
            $cashIn = CashIn::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })->get()->map(function ($item) {
                $item->entry_type = 'Cash In';
                return $item;
            });

            $cashOut = CashOut::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })->get()->map(function ($item) {
                $item->entry_type = 'Cash Out';
                return $item;
            });

            $records = $cashIn->merge($cashOut);
        }

        // Sort the combined records collection by 'created_at'
        $records = $records->sortByDesc('created_at');

        // Paginate the results manually
        $perPage = 10;
        $page = $request->input('page', 1);
        $paginatedRecords = $records->slice(($page - 1) * $perPage, $perPage);
        $recordsPagination = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedRecords,
            $records->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('transactions.index', ['records' => $recordsPagination]);
    }
    public function today(Request $request)
    {
        $type = $request->input('type');

        $startOfDay = \Carbon\Carbon::today('Asia/Kolkata')->startOfDay()->timezone('UTC');
        $endOfDay = \Carbon\Carbon::today('Asia/Kolkata')->endOfDay()->timezone('UTC');

        $cashIns = collect();
        $cashOuts = collect();

        if (!$type || $type === 'cash_in') {
            $cashIns = CashIn::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->get()
                ->map(function ($item) {
                    $item->entry_type = 'cashin';
                    return $item;
                });
        }

        if (!$type || $type === 'cash_out') {
            $cashOuts = CashOut::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->get()
                ->map(function ($item) {
                    $item->entry_type = 'cashout';
                    return $item;
                });
        }

        $transactions = $cashIns->merge($cashOuts)->sortByDesc('created_at');
        $totalCashIn = $cashIns->sum('total');
        $totalCashOut = $cashOuts->sum('total');

        return view('transactions.today', compact('transactions', 'totalCashIn', 'totalCashOut'));
    }
}
