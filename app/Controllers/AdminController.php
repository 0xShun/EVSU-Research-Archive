<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\Controller;

class AdminController extends BaseController
{
    public function index()
    {
        // Load the admin dashboard view
        $publicationModel = new \App\Models\PublicationModel();
        $totalPublications = $publicationModel->countAll();

        $userModel = new \App\Models\UserModel();
        $totalUsers = $userModel->countAll();

        return view('admin/index', [
            'title' => 'Admin Dashboard',
            'totalPublications' => $totalPublications,
            'totalUsers' => $totalUsers
        ]);
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
        
        // Fetch submissions and join with departments table to get department name
        $submissions = $publicationModel->select('publications.*, departments.name as department_name')
                                        ->join('departments', 'departments.id = publications.department_id')
                                        ->findAll();

        return view('admin/manage_submissions', ['submissions' => $submissions]);
    }

    // Approve a submission
    public function approveSubmission($id)
    {
        $publicationModel = new \App\Models\PublicationModel();
        $publication = $publicationModel->find($id);

        if (!$publication) {
            return redirect()->to(base_url('admin/manage-submissions'))->with('error', 'Submission not found.');
        }

        // Update status to 'approved'
        if ($publicationModel->update($id, ['status' => 'approved'])) {
            // TODO: Add notification logic for the user who uploaded the submission

            // Fetch the user who uploaded the submission
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($publication['user_id']);

            if ($user) {
                $userEmail = $user['email'];

                // Email sending logic
                $email = \Config\Services::email();

                $email->setTo($userEmail);
                $email->setSubject('Your Submission Has Been Approved');
                $email->setMessage('Your submission with title: ' . $publication['title'] . ' has been approved.');

                if ($email->send()) {
                    log_message('debug', 'Approval email sent successfully to: ' . $userEmail);
                } else {
                    log_message('error', 'Failed to send approval email to: ' . $userEmail . '. Error: ' . $email->printDebugger());
                }
            }

            return redirect()->to(base_url('admin/manage-submissions'))->with('success', 'Submission approved successfully.');
        } else {
            return redirect()->to(base_url('admin/manage-submissions'))->with('error', 'Failed to approve submission. Please try again.');
        }
    }

    // Reject a submission
    public function rejectSubmission($id)
    {
        $publicationModel = new \App\Models\PublicationModel();
        $publication = $publicationModel->find($id);

        if (!$publication) {
            return redirect()->to(base_url('admin/manage-submissions'))->with('error', 'Submission not found.');
        }

        // Update status to 'rejected'
        if ($publicationModel->update($id, ['status' => 'rejected'])) {
            // TODO: Add notification logic for the user who uploaded the submission

            // Fetch the user who uploaded the submission
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($publication['user_id']);

            if ($user) {
                $userEmail = $user['email'];

                // Email sending logic
                $email = \Config\Services::email();

                $email->setTo($userEmail);
                $email->setSubject('Your Submission Has Been Rejected');
                $email->setMessage('Your submission with title: ' . $publication['title'] . ' has been rejected.');

                if ($email->send()) {
                    log_message('debug', 'Rejection email sent successfully to: ' . $userEmail);
                } else {
                    log_message('error', 'Failed to send rejection email to: ' . $userEmail . '. Error: ' . $email->printDebugger());
                }
            }

            return redirect()->to(base_url('admin/manage-submissions'))->with('success', 'Submission rejected successfully.');
        } else {
            return redirect()->to(base_url('admin/manage-submissions'))->with('error', 'Failed to reject submission. Please try again.');
        }
    }

    // Show form to create a new user
    public function createUser()
    {
        // Load the form view for creating a user
        return view('admin/create_user', ['title' => 'Create User']);
    }

    // Store a new user in the database
    public function storeUser()
    {
        $userModel = new \App\Models\UserModel();

        // Validate input
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[actors.email]',
            'password' => 'required|min_length[8]',
            'role' => 'required|in_list[Student,Faculty & Researcher,Thesis Adviser,University Administration]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Create user data array
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'is_active' => true, // New users are active by default
        ];

        // Insert user into database
        if ($userModel->insert($data)) {
            return redirect()->to(base_url('admin/manage-users'))->with('success', 'User created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create user. Please try again.');
        }
    }

    // Show form to edit an existing user
    public function editUser($id)
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to(base_url('admin/manage-users'))->with('error', 'User not found.');
        }

        // Load the form view for editing a user
        return view('admin/edit_user', ['title' => 'Edit User', 'user' => $user]);
    }

    // Update an existing user in the database
    public function updateUser($id)
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to(base_url('admin/manage-users'))->with('error', 'User not found.');
        }

        // Prepare data for update
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'is_active' => (bool) $this->request->getPost('is_active'), // Assuming a checkbox or similar input
        ];

        // Update password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Perform direct database update, bypassing model validation
        $db = \Config\Database::connect();
        $builder = $db->table('actors');
        $builder->where('id', $id);

        if ($builder->update($data)) {
            return redirect()->to(base_url('admin/manage-users'))->with('success', 'User updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update user. Please try again.');
        }
    }

    // Delete a user from the database
    public function deleteUser($id)
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to(base_url('admin/manage-users'))->with('error', 'User not found.');
        }

        // Prevent deleting the currently logged-in user
        if ($user['id'] === session()->get('user_id')) {
            return redirect()->to(base_url('admin/manage-users'))->with('error', 'You cannot delete your own account.');
        }

        // Delete user from database
        if ($userModel->delete($id)) {
            return redirect()->to(base_url('admin/manage-users'))->with('success', 'User deleted successfully.');
        } else {
            return redirect()->to(base_url('admin/manage-users'))->with('error', 'Failed to delete user. Please try again.');
        }
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

    public function contactMessages()
    {
        $contactMessageModel = new \App\Models\ContactMessageModel();
        $messages = $contactMessageModel->orderBy('created_at', 'DESC')->findAll();

        return view('admin/contact_messages', [
            'title' => 'Contact Messages',
            'messages' => $messages
        ]);
    }

    public function managePublications()
    {
        log_message('debug', 'Attempting to access admin publications management page.');
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            log_message('debug', 'User not logged in or not admin. Redirecting to login.');
            return redirect()->to('auth/login');
        }

        log_message('debug', 'User is logged in as admin. Loading publications.');
        $publicationModel = new \App\Models\PublicationModel();
        $collegeModel = new \App\Models\CollegeModel();
        $departmentModel = new \App\Models\DepartmentModel();
        $programModel = new \App\Models\ProgramModel();

        // Get all publications with related data
        $publications = $publicationModel->select('
                publications.*,
                colleges.name as college_name,
                departments.name as department_name,
                programs.name as program_name
            ')
            ->join('colleges', 'colleges.id = publications.college_id')
            ->join('departments', 'departments.id = publications.department_id')
            ->join('programs', 'programs.id = publications.program_id')
            ->orderBy('publications.created_at', 'DESC')
            ->paginate(10);

        $data = [
            'title' => 'Manage Publications',
            'publications' => $publications,
            'pager' => $publicationModel->pager,
            'colleges' => $collegeModel->findAll(),
            'departments' => $departmentModel->findAll(),
            'programs' => $programModel->findAll()
        ];

        log_message('debug', 'Loading admin/manage_publications view.');
        return view('admin/manage_publications', $data);
    }

    public function updatePublication($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('auth/login');
        }

        $publicationModel = new \App\Models\PublicationModel();
        $publication = $publicationModel->find($id);

        if (!$publication) {
            return redirect()->to('admin/publications')->with('error', 'Publication not found.');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'title' => $this->request->getPost('title'),
                'authors' => $this->request->getPost('authors'),
                'abstract' => $this->request->getPost('abstract'),
                'keywords' => $this->request->getPost('keywords'),
                'college_id' => $this->request->getPost('college_id'),
                'department_id' => $this->request->getPost('department_id'),
                'program_id' => $this->request->getPost('program_id'),
                'publication_date' => $this->request->getPost('publication_date'),
                'status' => $this->request->getPost('status')
            ];

            try {
                if ($publicationModel->update($id, $data)) {
                    return redirect()->to('admin/publications')->with('success', 'Publication updated successfully.');
                } else {
                    return redirect()->to('admin/publications')->with('error', 'Failed to update publication.');
                }
            } catch (\Exception $e) {
                log_message('error', 'Error updating publication: ' . $e->getMessage());
                return redirect()->to('admin/publications')->with('error', 'An error occurred while updating the publication.');
            }
        }

        return redirect()->to('admin/publications');
    }

    public function approvePublication($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('auth/login');
        }

        $publicationModel = new \App\Models\PublicationModel();
        $publication = $publicationModel->find($id);

        if (!$publication) {
            return redirect()->to('admin/publications')->with('error', 'Publication not found.');
        }

        try {
            if ($publicationModel->update($id, ['status' => 'approved'])) {
                return redirect()->to('admin/publications')->with('success', 'Publication approved successfully.');
            } else {
                return redirect()->to('admin/publications')->with('error', 'Failed to approve publication.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error approving publication: ' . $e->getMessage());
            return redirect()->to('admin/publications')->with('error', 'An error occurred while approving the publication.');
        }
    }
} 