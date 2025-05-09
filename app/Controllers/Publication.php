<?php

namespace App\Controllers;

use App\Models\PublicationModel;
use App\Models\CollegeModel;
use App\Models\DepartmentModel;

class Publication extends BaseController
{
    protected $publicationModel;
    protected $collegeModel;
    protected $departmentModel;

    public function __construct()
    {
        $this->publicationModel = new PublicationModel();
        $this->collegeModel = new CollegeModel();
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        $department_id = $this->request->getGet('department_id');
        $college_id = $this->request->getGet('college_id');
        $program_id = $this->request->getGet('program_id');
        $year = $this->request->getGet('year');
        $sort = $this->request->getGet('sort') ?? 'newest';

        $query = $this->publicationModel->builder();

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
        $colleges = $this->collegeModel->findAll();
        $departments = $this->departmentModel->findAll();
        return view('upload_publication', ['colleges' => $colleges, 'departments' => $departments]);
    }

    public function create()
    {
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
            'file_path' => 'uploads/publications/' . $fileName
        ];

        $this->publicationModel->insert($data);

        return redirect()->to('publication')->with('message', 'Publication uploaded successfully.');
    }

    public function view($id)
    {
        $publication = $this->publicationModel->find($id);

        if (!$publication) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

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
        $publication = $this->publicationModel->find($id);

        if (!$publication) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('edit_publication', ['publication' => $publication]);
    }

    public function update($id)
    {
        $publication = $this->publicationModel->find($id);

        if (!$publication) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'authors' => 'required|min_length[3]|max_length[255]',
            'abstract' => 'required|min_length[10]',
            'keywords' => 'required|min_length[3]',
            'college_id' => 'required|numeric',
            'department_id' => 'required|numeric',
            'program_id' => 'required|numeric',
            'publication_date' => 'required|valid_date'
        ];

        if ($this->request->getFile('file')->isValid()) {
            $rules['file'] = 'uploaded[file]|max_size[file,10240]|mime_in[file,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'authors' => $this->request->getPost('authors'),
            'abstract' => $this->request->getPost('abstract'),
            'keywords' => $this->request->getPost('keywords'),
            'college_id' => $this->request->getPost('college_id'),
            'department_id' => $this->request->getPost('department_id'),
            'program_id' => $this->request->getPost('program_id'),
            'publication_date' => $this->request->getPost('publication_date')
        ];

        $file = $this->request->getFile('file');
        if ($file->isValid()) {
            // Delete old file
            if (!empty($publication['file_path'])) {
                $oldFile = WRITEPATH . $publication['file_path'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            // Upload new file
            $fileName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/publications', $fileName);
            $data['file_path'] = 'uploads/publications/' . $fileName;
        }

        $this->publicationModel->update($id, $data);

        return redirect()->to('publication/view/' . $id)->with('message', 'Publication updated successfully.');
    }

    public function delete($id)
    {
        $publication = $this->publicationModel->find($id);

        if (!$publication) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Delete file
        if (!empty($publication['file_path'])) {
            $file = WRITEPATH . $publication['file_path'];
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $this->publicationModel->delete($id);

        return redirect()->to('publication')->with('message', 'Publication deleted successfully.');
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

    private function getPublicationStats()
    {
        // Since we don't have a type field in the existing table,
        // we'll return a simple count of publications
        return [
            'total' => $this->publicationModel->countAll()
        ];
    }
} 