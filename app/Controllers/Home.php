<?php

namespace App\Controllers;

use App\Models\PublicationModel;
use App\Models\DepartmentModel;

class Home extends BaseController
{
    protected $publicationModel;
    protected $departmentModel;

    public function __construct()
    {
        $this->publicationModel = new PublicationModel();
        // $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        // Get latest publications
        $latestPublications = $this->publicationModel->orderBy('publication_date', 'DESC')
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
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email',
                'subject' => 'required|min_length[5]|max_length[200]',
                'message' => 'required|min_length[10]'
            ];

            if ($this->validate($rules)) {
                // Here you would typically send the email
                // For now, we'll just redirect with success message
                return redirect()->to('/contact')->with('message', 'Thank you for your message. We will get back to you soon.');
            }

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        return view('contact');
    }
}
