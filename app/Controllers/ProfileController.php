<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PublicationModel;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $publicationModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->publicationModel = new PublicationModel();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        $publications = $this->publicationModel->where('user_id', $userId)->findAll();

        return view('profile/index', [
            'user' => $user,
            'publications' => $publications
        ]);
    }

    public function update()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }

        $userId = session()->get('user_id');
        $data = $this->request->getPost([
            'name',
            'research_interests'
        ]);

        // Remove empty fields to prevent overwriting with empty values
        foreach ($data as $key => $value) {
            if (empty($value)) {
                unset($data[$key]);
            }
        }

        if ($this->userModel->update($userId, $data)) {
            // Update session data if name changed
            if (isset($data['name'])) {
                session()->set('name', $data['name']);
            }
            session()->setFlashdata('success', 'Profile updated successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to update profile.');
        }

        return redirect()->to('/profile');
    }
} 