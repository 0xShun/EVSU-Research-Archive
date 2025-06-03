<?php

namespace App\Controllers;

use App\Models\PublicationModel;
use App\Models\DepartmentModel;
use App\Models\ContactMessageModel;

class Home extends BaseController
{
    protected $publicationModel;
    protected $departmentModel;
    protected $contactMessageModel;

    public function __construct()
    {
        $this->publicationModel = new PublicationModel();
        // $this->departmentModel = new DepartmentModel();
        $this->contactMessageModel = new ContactMessageModel();
    }

    public function index()
    {
        // Get latest approved publications
        $latestPublications = $this->publicationModel
                                                    ->where('status', 'approved')
                                                    ->orderBy('publication_date', 'DESC')
                                                    ->limit(6)
                                                    ->find();

        // Get publication statistics
        $stats = [
            'total' => $this->publicationModel->countAll(),
            'this_year' => $this->publicationModel->where('YEAR(publication_date)', date('Y'))->countAllResults(),
            'colleges' => $this->publicationModel->distinct()->select('college_id')->countAllResults(),
            'departments' => $this->publicationModel->distinct()->select('department_id')->countAllResults()
        ];

        return view('home', [
            'latest_publications' => $latestPublications,
            'stats' => $stats
        ]);
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        // Only load the view for GET requests
        return view('contact');
    }

    public function submitContact()
    {
        // Handle form submission for POST requests
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'subject' => 'required|min_length[5]|max_length[200]',
            'message' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            log_message('debug', 'Contact form validation failed in submitContact method.');
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
        ];

        log_message('debug', 'Attempting to insert contact message in submitContact method: ' . print_r($data, true));

        if ($this->contactMessageModel->insert($data)) {
            log_message('debug', 'Contact message inserted successfully in submitContact method.');
            return redirect()->to('/contact')->with('success', 'Thank you for your message. We will get back to you soon.');
        } else {
            log_message('error', 'Failed to insert contact message in submitContact method. Model insert returned false.');
            return redirect()->back()->withInput()->with('error', 'Failed to send your message. Please try again.');
        }
    }
}
