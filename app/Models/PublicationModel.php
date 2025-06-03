<?php

namespace App\Models;

use CodeIgniter\Model;

class PublicationModel extends Model
{
    protected $table = 'publications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'title',
        'authors',
        'abstract',
        'keywords',
        'college_id',
        'department_id',
        'program_id',
        'publication_date',
        'file_path',
        'thumbnail',
        'user_id',
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'authors' => 'required|min_length[3]|max_length[255]',
        'abstract' => 'required|min_length[10]',
        'keywords' => 'required|min_length[3]',
        'college_id' => 'required|numeric',
        'department_id' => 'required|numeric',
        'program_id' => 'required|numeric',
        'publication_date' => 'required|valid_date',
        'user_id' => 'required|numeric'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'The title field is required.',
            'min_length' => 'The title must be at least 3 characters long.',
            'max_length' => 'The title cannot exceed 255 characters.'
        ],
        'authors' => [
            'required' => 'The authors field is required.',
            'min_length' => 'The authors field must be at least 3 characters long.',
            'max_length' => 'The authors field cannot exceed 255 characters.'
        ],
        'abstract' => [
            'required' => 'The abstract field is required.',
            'min_length' => 'The abstract must be at least 10 characters long.'
        ],
        'keywords' => [
            'required' => 'The keywords field is required.',
            'min_length' => 'The keywords field must be at least 3 characters long.'
        ],
        'college_id' => [
            'required' => 'The college field is required.',
            'numeric' => 'The college must be a number.'
        ],
        'department_id' => [
            'required' => 'The department field is required.',
            'numeric' => 'The department must be a number.'
        ],
        'program_id' => [
            'required' => 'The program field is required.',
            'numeric' => 'The program must be a number.'
        ],
        'publication_date' => [
            'required' => 'The publication date field is required.',
            'valid_date' => 'The publication date must be a valid date.'
        ],
        'user_id' => [
            'required' => 'The user ID field is required.',
            'numeric' => 'The user ID must be a number.'
        ]
    ];

    protected $skipValidation = false;

    public function getRelatedPublications($id, $limit = 5)
    {
        $publication = $this->find($id);
        if (!$publication) {
            return [];
        }

        return $this->where('id !=', $id)
            ->groupStart()
                ->where('department_id', $publication['department_id'])
                ->orWhere("FIND_IN_SET(?, keywords)", explode(',', $publication['keywords']))
            ->groupEnd()
            ->limit($limit)
            ->find();
    }

    public function getPublicationStats()
    {
        // Since we don't have a type field in the existing table,
        // we'll return a simple count of publications
        return [
            'total' => $this->countAll()
        ];
    }
} 