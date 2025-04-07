<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\DepartmentModel;
use App\Models\ProgramModel;
use App\Models\PublicationModel;

class Api extends ResourceController
{
    protected $departmentModel;
    protected $programModel;
    protected $publicationModel;

    public function __construct()
    {
        $this->departmentModel = new DepartmentModel();
        $this->programModel = new ProgramModel();
        $this->publicationModel = new PublicationModel();
    }

    public function getDepartments($collegeId)
    {
        $departments = $this->departmentModel->where('college_id', $collegeId)
                                           ->orderBy('name', 'ASC')
                                           ->findAll();
        return $this->response->setJSON($departments);
    }

    public function getPrograms($departmentId)
    {
        $programs = $this->programModel->where('department_id', $departmentId)
                                     ->orderBy('name', 'ASC')
                                     ->findAll();
        return $this->response->setJSON($programs);
    }

    public function searchPublications()
    {
        $query = $this->request->getGet('q');
        $page = $this->request->getGet('page') ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $publications = $this->publicationModel->builder()
            ->select('publications.*, departments.name as department_name')
            ->join('departments', 'departments.id = publications.department_id', 'left')
            ->groupStart()
                ->like('publications.title', $query)
                ->orLike('publications.authors', $query)
                ->orLike('publications.abstract', $query)
                ->orLike('publications.keywords', $query)
            ->groupEnd()
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();

        $total = $this->publicationModel->builder()
            ->groupStart()
                ->like('title', $query)
                ->orLike('authors', $query)
                ->orLike('abstract', $query)
                ->orLike('keywords', $query)
            ->groupEnd()
            ->countAllResults();

        return $this->response->setJSON([
            'publications' => $publications,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ]);
    }

    public function getPublication($id)
    {
        $publication = $this->publicationModel->builder()
            ->select('publications.*, departments.name as department_name, colleges.name as college_name, programs.name as program_name')
            ->join('departments', 'departments.id = publications.department_id', 'left')
            ->join('colleges', 'colleges.id = publications.college_id', 'left')
            ->join('programs', 'programs.id = publications.program_id', 'left')
            ->where('publications.id', $id)
            ->get()
            ->getRowArray();

        if (!$publication) {
            return $this->failNotFound('Publication not found');
        }

        return $this->response->setJSON($publication);
    }

    public function createPublication()
    {
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

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $file = $this->request->getFile('file');
        if ($file && $file->isValid()) {
            $fileName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/publications', $fileName);
            $this->request->setVar('file_path', 'uploads/publications/' . $fileName);
        }

        $id = $this->publicationModel->insert($this->request->getVar());
        $publication = $this->publicationModel->find($id);

        return $this->respondCreated($publication);
    }

    public function updatePublication($id)
    {
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

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $file = $this->request->getFile('file');
        if ($file && $file->isValid()) {
            $fileName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/publications', $fileName);
            $this->request->setVar('file_path', 'uploads/publications/' . $fileName);

            // Delete old file
            $publication = $this->publicationModel->find($id);
            if ($publication && !empty($publication['file_path'])) {
                $oldFile = WRITEPATH . $publication['file_path'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
        }

        $this->publicationModel->update($id, $this->request->getVar());
        $publication = $this->publicationModel->find($id);

        return $this->respond($publication);
    }

    public function deletePublication($id)
    {
        $publication = $this->publicationModel->find($id);
        
        if (!$publication) {
            return $this->failNotFound('Publication not found');
        }

        // Delete file if exists
        if (!empty($publication['file_path'])) {
            $file = WRITEPATH . $publication['file_path'];
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $this->publicationModel->delete($id);
        return $this->respondDeleted(['id' => $id]);
    }
} 