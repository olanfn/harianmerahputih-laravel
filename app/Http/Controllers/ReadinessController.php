<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\View\View;

class ReadinessController extends Controller
{
    public function __invoke(Application $application): View
    {
        abort_unless(in_array(config('app.env'), ['local', 'testing'], true), 404);

        return view('readiness', [
            'application' => $application,
            'checks' => [
                'Konfigurasi aplikasi' => (bool) config('app.key'),
                'Penyimpanan runtime' => is_writable(storage_path()),
                'Cache runtime' => is_writable(storage_path('framework/cache/data')),
            ],
        ]);
    }
}
