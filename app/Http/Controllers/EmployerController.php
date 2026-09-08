<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployerController extends Controller
{
    public function index()
    {
        return view('employers.index');
    }

    public function post(Request $request)
    {
        return view('employers.post', [
            'user' => $request->user(),
            'redirectTo' => '/employers/post',
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return view('employers.dashboard', ['user' => null, 'redirectTo' => '/employers/dashboard']);
        }

        $jobs = $user->postedJobs()->latest('posted_at')->get();
        $payments = $user->jobPostingPayments()->with('jobListing')->latest('posted_at')->get();

        return view('employers.dashboard', [
            'user' => $user,
            'jobs' => $jobs,
            'payments' => $payments,
        ]);
    }

    public function removeJob(Request $request, string $id)
    {
        $user = $request->user();
        $job = $user?->postedJobs()->where('id', $id)->first();

        abort_if(! $job, 404);

        $job->delete();

        return redirect('/employers/dashboard');
    }
}
