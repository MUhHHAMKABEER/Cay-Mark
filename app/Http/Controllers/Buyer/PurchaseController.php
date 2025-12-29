<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    protected $invoiceService;

    public function __construct()
    {
        $this->invoiceService = new InvoiceService();
    }

    /**
     * Display won auctions with invoices.
     */
    public function index()
    {
        $user = Auth::user();
        $invoices = $this->invoiceService->getBuyerInvoices($user);

        return view('Buyer.auctions-won', compact('invoices'));
    }

    /**
     * Download invoice PDF.
     */
    public function downloadInvoice($invoiceId)
    {
        $user = Auth::user();
        $invoice = \App\Models\Invoice::where('id', $invoiceId)
            ->where('buyer_id', $user->id)
            ->firstOrFail();

        if (!$invoice->pdf_path || !file_exists(public_path($invoice->pdf_path))) {
            return back()->with('error', 'Invoice PDF not found. Please contact support.');
        }

        return response()->download(public_path($invoice->pdf_path));
    }
}
