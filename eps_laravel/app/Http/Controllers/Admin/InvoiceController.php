<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Invoice;
use App\Models\Admin\member;
use App\Models\Admin\User;

class InvoiceController extends Controller
{
    public function list_inv()
    {
        $data = Invoice::with('User')->get();
        return view('admin.invoice.index', ['data' => $data]);
    }
}
