<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PublicationModel;
use App\Models\CollegeModel;
use App\Models\DepartmentModel;
use App\Models\ProgramModel;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $publicationModel;
    protected $collegeModel;
    protected $departmentModel;
    protected $programModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->publicationModel = new PublicationModel();
        $this->collegeModel = new CollegeModel();
        $this->departmentModel = new DepartmentModel();
        $this->programModel = new ProgramModel();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('auth/login');
        }

        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $publicationModel = new \App\Models\PublicationModel();
        $collegeModel = new \App\Models\CollegeModel();
        $departmentModel = new \App\Models\DepartmentModel();
        $programModel = new \App\Models\ProgramModel();

        $user = $userModel->find($userId);
        
        // Fetch publications uploaded by the logged-in user with related college, department, and program names
        $publications = $publicationModel->select('
                publications.*,
                colleges.name as college_name,
                departments.name as department_name,
                programs.name as program_name
            ')
            ->join('colleges', 'colleges.id = publications.college_id')
            ->join('departments', 'departments.id = publications.department_id')
            ->join('programs', 'programs.id = publications.program_id')
            ->where('publications.user_id', $userId)
            ->orderBy('publications.created_at', 'DESC')
            ->paginate(5);

        // Fetch all colleges, departments, and programs for the dropdowns in the modal
        $colleges = $collegeModel->findAll();
        $departments = $departmentModel->findAll();
        $programs = $programModel->findAll();

        $data = [
            'title' => 'Profile',
            'user' => $user,
            'publications' => $publications,
            'pager' => $publicationModel->pager,
            'colleges' => $colleges,
            'departments' => $departments,
            'programs' => $programs
        ];

        return view('profile/index', $data);
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

        // Handle profile picture upload
        $file = $this->request->getFile('profile_picture');
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/profiles', $newName);
            $data['profile_picture'] = 'uploads/profiles/' . $newName;
            // Update session data with new profile picture path
            session()->set('profile_picture', $data['profile_picture']);

            // Delete old profile picture if it exists and is not the default
            $oldProfilePicture = session()->get('profile_picture_old'); // Assuming you stored the old path in session
            if ($oldProfilePicture && $oldProfilePicture !== 'assets/user-default.png' && file_exists($oldProfilePicture)) {
                 unlink($oldProfilePicture);
            }
             // Store the new profile picture path in session for future deletion
             session()->set('profile_picture_old', $data['profile_picture']);

        } else {
             // If no new file is uploaded, remove profile_picture from data to prevent unsetting existing one if input is empty
             unset($data['profile_picture']);
        }

        // Remove empty fields to prevent overwriting with empty values, except for fields that can be intentionally cleared like research_interests
        $cleanedData = array_filter($data, function($value, $key) {
            // Keep research_interests even if empty string, to allow clearing it
            return $value !== null || $key === 'research_interests';
        }, ARRAY_FILTER_USE_BOTH);

        // Ensure research_interests is included even if it was an empty string and filtered out by array_filter
         if (isset($data['research_interests']) && $data['research_interests'] === '') {
             $cleanedData['research_interests'] = '';
         }

        if ($this->userModel->update($userId, $cleanedData)) {
            // Re-fetch user data after successful update to ensure session is updated
            $updatedUser = $this->userModel->find($userId);
            if ($updatedUser) {
                session()->set('name', $updatedUser['name']);
                // Only update profile_picture in session if a new one was uploaded and saved
                if (isset($cleanedData['profile_picture'])) {
                     session()->set('profile_picture', $updatedUser['profile_picture']);
                }
                // Assuming username is the same as name or derived, update if necessary
                // session()->set('username', $updatedUser['username']);
            }
            session()->setFlashdata('success', 'Profile updated successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to update profile.');
        }

        return redirect()->to('/profile');
    }
} 