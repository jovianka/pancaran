<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('faculty', 'major');

        $profileData = [];

        if ($user->type === 'student') {
            $profileData = [
                'Name' => $user->name,
                'NIM' => $user->nim,
                'Faculty' => $user->faculty?->name ?? '-',
                'Major' => $user->major?->name ?? '-',
                'Status' => 'Active',
            ];
        } elseif ($user->type === 'organization') {
            $profileData = [
                'Name' => $user->name,
                'Jenis Organisasi' => 'Eksekutif Mahasiswa',
                'Scope' => 'Fakultas Kedokteran',
                'Tahun berdiri' => '22 January 1999',
                'Status' => 'Active',
            ];
        } 
        // elseif ($user->type === 'admin') {
        //     $profileData = [
        //         'Name' => 'Admin',
        //         'Email' => $user->email,
        //         'Role' => 'Super Admin',
        //     ];
        // }

        return Inertia::render('Dashboard', [
            'profileData' => $profileData,
            'userType' => $user->type,
        ]);
    }
}