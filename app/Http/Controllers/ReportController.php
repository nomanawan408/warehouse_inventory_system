<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of reports.
     */
    public function index()
    {
        return view('report.index');
    }

    /**
     * Show the form for creating a new report.
     */
    public function create()
    {
        return view('report.create');
    }

    /**
     * Display a generated report.
     */
    public function view($id)
    {
        return view('report.view', compact('id'));
    }
}
