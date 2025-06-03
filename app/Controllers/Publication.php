<?php

namespace App\Controllers;

use App\Models\PublicationModel;
use App\Models\CollegeModel;
use App\Models\DepartmentModel;
use App\Models\ProgramModel;

class Publication extends BaseController
{
    protected $publicationModel;
    protected $collegeModel;
    protected $departmentModel;
    protected $programModel;

    public function __construct()
    {
        $this->publicationModel = new PublicationModel();
        $this->collegeModel = new CollegeModel();
        $this->departmentModel = new DepartmentModel();
        $this->programModel = new ProgramModel();
    }

    public function index()
    {
        $department_id = $this->request->getGet('department_id');
        $college_id = $this->request->getGet('college_id');
        $program_id = $this->request->getGet('program_id');
        $year = $this->request->getGet('year');
        $sort = $this->request->getGet('sort') ?? 'newest';

        $query = $this->publicationModel->builder();

        // Only show approved publications on the public listing
        $query->where('publications.status', 'approved');

        if ($department_id) {
            $query->where('department_id', $department_id);
        }
        if ($college_id) {
            $query->where('college_id', $college_id);
        }
        if ($program_id) {
            $query->where('program_id', $program_id);
        }
        if ($year) {
            $query->where('YEAR(publication_date)', $year);
        }

        switch ($sort) {
            case 'oldest':
                $query->orderBy('publication_date', 'ASC');
                break;
            case 'title':
                $query->orderBy('title', 'ASC');
                break;
            case 'department':
                $query->orderBy('department_id', 'ASC');
                break;
            default: // newest
                $query->orderBy('publication_date', 'DESC');
        }

        $publications = $this->publicationModel->paginate(10);
        
        // Get all colleges and departments for the filter
        $colleges = $this->collegeModel->findAll();
        $departments = $this->departmentModel->findAll();
        
        // Get unique years from publications
        $years = $this->publicationModel->select('DISTINCT YEAR(publication_date) as year')
                                      ->orderBy('year', 'DESC')
                                      ->get()
                                      ->getResultArray();
        
        $data = [
            'publications' => $publications,
            'pager' => $this->publicationModel->pager,
            'department_id' => $department_id,
            'college_id' => $college_id,
            'program_id' => $program_id,
            'year' => $year,
            'sort' => $sort,
            'total_publications' => $this->publicationModel->countAll(),
            'stats' => $this->getPublicationStats(),
            'colleges' => $colleges,
            'departments' => $departments,
            'years' => array_column($years, 'year')
        ];

        return view('list_publications', $data);
    }

    public function upload()
    {
        $colleges = $this->collegeModel->orderBy('name', 'ASC')->findAll();
        return view('upload_publication', ['colleges' => $colleges]);
    }

    public function create()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('auth/login');
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'authors' => 'required|min_length[3]|max_length[255]',
            'abstract' => 'required|min_length[10]',
            'keywords' => 'required|min_length[3]',
            'college_id' => 'required|numeric',
            'department_id' => 'required|numeric',
            'program_id' => 'required|numeric',
            'publication_date' => 'required|valid_date',
            'file' => 'uploaded[file]|max_size[file,10240]|mime_in[file,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('file');
        $fileName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/publications', $fileName);

        $data = [
            'title' => $this->request->getPost('title'),
            'authors' => $this->request->getPost('authors'),
            'abstract' => $this->request->getPost('abstract'),
            'keywords' => $this->request->getPost('keywords'),
            'college_id' => $this->request->getPost('college_id'),
            'department_id' => $this->request->getPost('department_id'),
            'program_id' => $this->request->getPost('program_id'),
            'publication_date' => $this->request->getPost('publication_date'),
            'file_path' => 'uploads/publications/' . $fileName,
            'user_id' => session()->get('user_id'),
            'status' => 'pending'
        ];

        $this->publicationModel->insert($data);

        return redirect()->to('profile')->with('message', 'Publication uploaded successfully and is pending approval.');
    }

    public function view($id)
    {
        $publication = $this->publicationModel->find($id);

        if (!$publication) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Fetch department and program names
        $department = $this->departmentModel->find($publication['department_id']);
        $program = $this->programModel->find($publication['program_id']);

        // Add department and program names to the publication data
        $publication['department_name'] = $department ? $department['name'] : 'N/A';
        $publication['program_name'] = $program ? $program['name'] : 'N/A';

        $relatedPublications = $this->publicationModel
            ->where('id !=', $id)
            ->where('department_id', $publication['department_id'])
            ->orGroupStart()
                ->whereIn('keywords', explode(',', $publication['keywords']))
            ->groupEnd()
            ->limit(5)
            ->findAll();

        return view('view_publication', [
            'publication' => $publication,
            'related_publications' => $relatedPublications
        ]);
    }

    public function edit($id)
    {
        log_message('debug', 'Entering Publication::edit method for ID: ' . $id . '. Request Method: ' . $this->request->getMethod());

        if (!session()->get('isLoggedIn')) {
            return redirect()->to('auth/login');
        }

        $publication = $this->publicationModel->find($id);

        if (!$publication) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if the user owns this publication
        if ($publication['user_id'] != session()->get('user_id')) {
            return redirect()->to('profile')->with('error', 'You do not have permission to edit this publication.');
        }

        // Handle form submission for updating
        if ($this->request->getMethod() === 'post') {
            log_message('debug', 'Processing POST request for publication update');

            // Get only the fields we want to update
            $data = [
                'title' => $this->request->getPost('title'),
                'authors' => $this->request->getPost('authors'),
                'abstract' => $this->request->getPost('abstract'),
                'keywords' => $this->request->getPost('keywords'),
                'college_id' => $this->request->getPost('college_id'),
                'department_id' => $this->request->getPost('department_id'),
                'program_id' => $this->request->getPost('program_id'),
                'publication_date' => $this->request->getPost('publication_date'),
                'user_id' => session()->get('user_id'),
                'status' => 'pending'
            ];

            log_message('debug', 'Update data: ' . print_r($data, true));

            try {
                if ($this->publicationModel->update($id, $data)) {
                    log_message('debug', 'Publication updated successfully');
                    return redirect()->to('profile')->with('success', 'Publication updated successfully and is pending approval.');
                } else {
                    log_message('error', 'Model update returned false');
                    return redirect()->to('profile')->with('error', 'Failed to update publication. Please try again.');
                }
            } catch (\Exception $e) {
                log_message('error', 'Exception during update: ' . $e->getMessage());
                return redirect()->to('profile')->with('error', 'An error occurred while updating the publication.');
            }
        }

        // Load the edit form for GET requests
        $colleges = $this->collegeModel->findAll();
        $departments = $this->departmentModel->where('college_id', $publication['college_id'])->findAll();
        $programs = $this->programModel->where('department_id', $publication['department_id'])->findAll();

        return view('edit_publication', [
            'title' => 'Edit Publication',
            'publication' => $publication,
            'colleges' => $colleges,
            'departments' => $departments,
            'programs' => $programs
        ]);
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('auth/login');
        }

        $publication = $this->publicationModel->find($id);

        if (!$publication) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if the user owns this publication
        if ($publication['user_id'] != session()->get('user_id')) {
            return redirect()->to('profile')->with('error', 'You do not have permission to delete this publication.');
        }

        // Delete file
        if (!empty($publication['file_path'])) {
            $file = WRITEPATH . $publication['file_path'];
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $this->publicationModel->delete($id);

        return redirect()->to('profile')->with('message', 'Publication deleted successfully.');
    }

    public function download($id)
    {
        $publication = $this->publicationModel->find($id);

        if (!$publication || empty($publication['file_path'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $file = WRITEPATH . $publication['file_path'];
        if (!file_exists($file)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response->download($file, null)->setFileName(basename($publication['file_path']));
    }

    public function search()
    {
        $keyword = $this->request->getGet('keyword');
        $college_id = $this->request->getGet('college_id');
        $department_id = $this->request->getGet('department_id');
        $program_id = $this->request->getGet('program_id');
        $year = $this->request->getGet('year');
        $category = $this->request->getGet('category');

        $query = $this->publicationModel->builder();

        // Only search approved publications
        $query->where('publications.status', 'approved');

        if ($keyword) {
            $query->groupStart()
                ->like('title', $keyword)
                ->orLike('authors', $keyword)
                ->orLike('abstract', $keyword)
                ->orLike('keywords', $keyword)
                ->groupEnd();
        }

        if ($college_id) {
            $query->where('college_id', $college_id);
        }
        if ($department_id) {
            $query->where('department_id', $department_id);
        }
        if ($year) {
            $query->where('YEAR(publication_date)', $year);
        }
        if ($category) {
            $query->where('category', $category);
        }

        $colleges = $this->collegeModel->findAll();
        $departments = $this->departmentModel->findAll();

        $data = [
            'publications' => $query->get()->getResultArray(),
            'keyword' => $keyword,
            'college_id' => $college_id,
            'department_id' => $department_id,
            'program_id' => $program_id,
            'year' => $year,
            'colleges' => $colleges,
            'departments' => $departments
        ];

        return view('search', $data);
    }

    public function updateFromProfileModal($id)
    {
        log_message('debug', 'Entering Publication::updateFromProfileModal method for ID: ' . $id);

        if (!session()->get('isLoggedIn')) {
            log_message('debug', 'User not logged in. Redirecting to login.');
            return redirect()->to('auth/login');
        }

        $publication = $this->publicationModel->find($id);

        if (!$publication) {
            log_message('error', 'Publication not found for ID: ' . $id);
            return redirect()->to('profile')->with('error', 'Publication not found.');
        }

        // Check if the user owns this publication
        if ($publication['user_id'] != session()->get('user_id')) {
            log_message('warning', 'User ' . session()->get('user_id') . ' attempted to edit publication ' . $id . ' they do not own.');
            return redirect()->to('profile')->with('error', 'You do not have permission to edit this publication.');
        }

        // Get only the fields allowed for update from the modal
        $data = [
            'title' => $this->request->getPost('title'),
            'authors' => $this->request->getPost('authors'),
            'abstract' => $this->request->getPost('abstract'),
            'keywords' => $this->request->getPost('keywords'),
            'college_id' => $this->request->getPost('college_id'),
            'department_id' => $this->request->getPost('department_id'),
            'program_id' => $this->request->getPost('program_id'),
            'publication_date' => $this->request->getPost('publication_date'),
            // Status is set to pending on update from profile
            'status' => 'pending'
        ];

        // Clean up data - remove nulls or empty strings if necessary, but keep 0 for IDs if valid input
        $cleanedData = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        log_message('debug', 'Attempting to update publication with data: ' . print_r($cleanedData, true));

        try {
            if ($this->publicationModel->update($id, $cleanedData)) {
                log_message('debug', 'Publication updated successfully via modal for ID: ' . $id);
                return redirect()->to('profile')->with('success', 'Publication updated successfully and is pending approval.');
            } else {
                // This might indicate a database error not caught by exception
                log_message('error', 'Model update returned false for ID: ' . $id);
                return redirect()->to('profile')->with('error', 'Failed to update publication. Please try again.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception during modal update for ID ' . $id . ': ' . $e->getMessage());
            return redirect()->to('profile')->with('error', 'An error occurred while updating the publication.');
        }
    }

    private function getPublicationStats()
    {
        // Since we don't have a type field in the existing table,
        // we'll return a simple count of publications
        return [
            'total' => $this->publicationModel->countAll()
        ];
    }
} 