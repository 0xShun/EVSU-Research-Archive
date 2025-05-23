<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class AdminController extends Controller
{
    public function index()
    {
        // Load admin dashboard view
        return view('admin/dashboard');
    }

    public function manageUsers()
    {
        $userModel = new \App\Models\UserModel();
        $users = $userModel->findAll();

        return view('admin/manage_users', ['users' => $users]);
    }

    public function manageSubmissions()
    {
        $publicationModel = new \App\Models\PublicationModel();
        $submissions = $publicationModel->findAll();

        return view('admin/manage_submissions', ['submissions' => $submissions]);
    }

    public function viewAnalytics()
    {
        $publicationModel = new \App\Models\PublicationModel();
        $totalPublications = $publicationModel->countAll();

        $userModel = new \App\Models\UserModel();
        $totalUsers = $userModel->countAll();

        return view('admin/view_analytics', [
            'totalPublications' => $totalPublications,
            'totalUsers' => $totalUsers
        ]);
    }
} 