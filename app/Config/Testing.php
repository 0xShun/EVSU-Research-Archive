<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Testing extends BaseConfig
{
    public $testUploadPath = WRITEPATH . 'uploads/test/';
    public $testFilePath = WRITEPATH . 'files/test/';
    public $testImagePath = WRITEPATH . 'images/test/';
    public $testThumbnailPath = WRITEPATH . 'thumbnails/test/';

    public $allowedFileTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    public $maxFileSize = 5120; // 5MB
    public $maxImageSize = 2048; // 2MB
    public $thumbnailWidth = 300;
    public $thumbnailHeight = 300;

    public $testUser = [
        'id' => 1,
        'username' => 'testuser',
        'email' => 'test@example.com',
        'role' => 'admin'
    ];

    public $testPublication = [
        'title' => 'Test Publication',
        'authors' => 'Test Author',
        'abstract' => 'Test Abstract',
        'keywords' => 'test,research',
        'college_id' => 1,
        'department_id' => 1,
        'program_id' => 1,
        'publication_date' => '2024-01-01',
        'file_path' => 'test.pdf',
        'thumbnail_path' => 'test.jpg',
        'status' => 'published'
    ];

    public function __construct()
    {
        parent::__construct();

        // Create test directories if they don't exist
        $this->createTestDirectories();
    }

    protected function createTestDirectories(): void
    {
        $directories = [
            $this->testUploadPath,
            $this->testFilePath,
            $this->testImagePath,
            $this->testThumbnailPath
        ];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
        }
    }
} 