<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    // indexページへ移動
    public function index()
    {
        $jobs = Job::all();
        return view('jobs.index',['jobs' => $jobs]);
    }
}
