<?php

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\PublicationModel;

class SecurityTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $seed = 'TestDataSeeder';
    protected $publicationModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->publicationModel = new PublicationModel();
        
        $this->createTestSession();
    }

    public function testCsrfProtection()
    {
        $result = $this->get('publications/create');
        $result->assertOK();
        $result->assertSee('csrf_token');
    }

    public function testXssProtection()
    {
        $testConfig = config('Testing');
        $testFile = $this->createTestFile();
        
        // Test XSS protection
        $data = [
            'title' => '<script>alert("XSS")</script>' . $testConfig->testPublication['title'],
            'authors' => $testConfig->testPublication['authors'],
            'abstract' => $testConfig->testPublication['abstract'],
            'keywords' => $testConfig->testPublication['keywords'],
            'college_id' => $testConfig->testPublication['college_id'],
            'department_id' => $testConfig->testPublication['department_id'],
            'program_id' => $testConfig->testPublication['program_id'],
            'publication_date' => $testConfig->testPublication['publication_date'],
            'publication_file' => $this->simulateFileUpload($testFile)
        ];
        
        $result = $this->post('publications/create', $data);
        
        $result->assertOK();
        $result->assertDontSee('<script>alert("XSS")</script>');
    }

    public function testSqlInjectionProtection()
    {
        $testConfig = config('Testing');
        $testFile = $this->createTestFile();
        
        // Test SQL injection protection
        $data = [
            'title' => "' OR '1'='1",
            'authors' => $testConfig->testPublication['authors'],
            'abstract' => $testConfig->testPublication['abstract'],
            'keywords' => $testConfig->testPublication['keywords'],
            'college_id' => $testConfig->testPublication['college_id'],
            'department_id' => $testConfig->testPublication['department_id'],
            'program_id' => $testConfig->testPublication['program_id'],
            'publication_date' => $testConfig->testPublication['publication_date'],
            'publication_file' => $this->simulateFileUpload($testFile)
        ];
        
        $result = $this->post('publications/create', $data);
        
        $result->assertOK();
        $result->assertDontSee("' OR '1'='1");
    }

    public function testFileUploadSecurity()
    {
        $testConfig = config('Testing');
        
        // Test file upload security
        $data = [
            'title' => $testConfig->testPublication['title'],
            'authors' => $testConfig->testPublication['authors'],
            'abstract' => $testConfig->testPublication['abstract'],
            'keywords' => $testConfig->testPublication['keywords'],
            'college_id' => $testConfig->testPublication['college_id'],
            'department_id' => $testConfig->testPublication['department_id'],
            'program_id' => $testConfig->testPublication['program_id'],
            'publication_date' => $testConfig->testPublication['publication_date'],
            'publication_file' => $this->simulateFileUpload('test.php')
        ];
        
        $result = $this->post('publications/create', $data);
        
        $result->assertStatus(400);
        $result->assertSee('Invalid file type');
    }

    public function testDirectoryTraversalProtection()
    {
        $result = $this->get('publications/download/../../../config/database.php');
        $result->assertStatus(404);
    }

    public function testSessionSecurity()
    {
        $result = $this->get('publications/create');
        $result->assertOK();
        $result->assertHeader('Set-Cookie');
        $result->assertHeader('HttpOnly');
        $result->assertHeader('Secure');
    }

    public function testContentSecurityPolicy()
    {
        $result = $this->get('/');
        $result->assertOK();
        $result->assertHeader('Content-Security-Policy');
    }

    public function testInputSanitization()
    {
        $data = [
            'title' => 'Test Publication',
            'authors' => 'Test Author',
            'abstract' => 'Test Abstract',
            'keywords' => 'test',
            'college_id' => '1 OR 1=1',
            'department_id' => '1 OR 1=1',
            'program_id' => '1 OR 1=1',
            'publication_date' => date('Y-m-d')
        ];

        $result = $this->post('publications/create', $data);
        $result->assertStatus(400);
    }

    public function testFileTypeValidation()
    {
        // Test executable file upload
        $exeFile = WRITEPATH . 'uploads/test.exe';
        file_put_contents($exeFile, 'Test content');

        $data = [
            'title' => 'Test Publication',
            'authors' => 'Test Author',
            'abstract' => 'Test Abstract',
            'keywords' => 'test',
            'college_id' => 1,
            'department_id' => 1,
            'program_id' => 1,
            'publication_date' => date('Y-m-d'),
            'publication_file' => new \CodeIgniter\Files\File($exeFile)
        ];

        $result = $this->post('publications/create', $data);
        $result->assertStatus(400);

        unlink($exeFile);
    }

    public function testFileSizeLimit()
    {
        // Create a large file
        $largeFile = WRITEPATH . 'uploads/large.pdf';
        file_put_contents($largeFile, str_repeat('Test content', 1000000));

        $data = [
            'title' => 'Test Publication',
            'authors' => 'Test Author',
            'abstract' => 'Test Abstract',
            'keywords' => 'test',
            'college_id' => 1,
            'department_id' => 1,
            'program_id' => 1,
            'publication_date' => date('Y-m-d'),
            'publication_file' => new \CodeIgniter\Files\File($largeFile)
        ];

        $result = $this->post('publications/create', $data);
        $result->assertStatus(400);

        unlink($largeFile);
    }

    public function testApiAuthentication()
    {
        $result = $this->get('api/departments/by-college/1');
        $result->assertStatus(401);
    }

    public function testRateLimiting()
    {
        // Make multiple requests in quick succession
        for ($i = 0; $i < 10; $i++) {
            $result = $this->get('publications/search?keyword=test');
            $result->assertOK();
        }

        // The 11th request should be rate limited
        $result = $this->get('publications/search?keyword=test');
        $result->assertStatus(429);
    }

    public function testAuthenticationRequired()
    {
        // Test authentication required
        $result = $this->get('publications/create');
        
        $result->assertRedirect();
        $result->assertRedirectTo('login');
    }

    public function testAuthorizationRequired()
    {
        $testConfig = config('Testing');
        $testFile = $this->createTestFile();
        
        // Create a test publication
        $data = [
            'title' => $testConfig->testPublication['title'],
            'authors' => $testConfig->testPublication['authors'],
            'abstract' => $testConfig->testPublication['abstract'],
            'keywords' => $testConfig->testPublication['keywords'],
            'college_id' => $testConfig->testPublication['college_id'],
            'department_id' => $testConfig->testPublication['department_id'],
            'program_id' => $testConfig->testPublication['program_id'],
            'publication_date' => $testConfig->testPublication['publication_date'],
            'publication_file' => $this->simulateFileUpload($testFile)
        ];
        
        $this->post('publications/create', $data);
        
        // Get the publication ID
        $publication = $this->publicationModel->where('title', $testConfig->testPublication['title'])->first();
        
        // Change user role to non-admin
        $_SESSION['user']['role'] = 'user';
        
        $result = $this->get('publications/edit/' . $publication->id);
        
        $result->assertStatus(403);
        $result->assertSee('Access denied');
    }
} 