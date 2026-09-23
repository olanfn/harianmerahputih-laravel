<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function requestForm() { return view('admin.auth.forgot-password'); }
    public function send(Request $request) { $data = $request->validate(['email' => ['required', 'email']]); $key = 'admin-reset:'.$request->ip(); if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 3)) return back()->withErrors(['email' => 'Terlalu banyak permintaan. Coba lagi nanti.']); \Illuminate\Support\Facades\RateLimiter::hit($key, 300); Password::sendResetLink($data); return back()->with('status', 'Jika email terdaftar, tautan reset telah dikirim.'); }
    public function form(string $token, Request $request) { return view('admin.auth.reset-password', ['token' => $token, 'email' => $request->query('email')]); }
    public function reset(Request $request) { $data = $request->validate(['token' => ['required'], 'email' => ['required', 'email'], 'password' => ['required', 'min:12', 'confirmed']]); $status = Password::reset($data, function ($user, $password) { $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save(); event(new PasswordReset($user)); }); return $status === Password::PASSWORD_RESET ? redirect()->route('admin.login')->with('status', 'Password berhasil diubah.') : back()->withErrors(['email' => __($status)]); }
}
