<?php

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\PublicationModel;

class PerformanceTest extends CIUnitTestCase
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

    public function testHomePageLoadTime()
    {
        $startTime = microtime(true);
        
        $result = $this->get('/');
        
        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;
        
        $result->assertOK();
        $this->assertLessThan(1.0, $loadTime, 'Home page should load in less than 1 second');
    }

    public function testPublicationListLoadTime()
    {
        $testConfig = config('Testing');
        $testFile = $this->createTestFile();
        
        // Create multiple test publications
        for ($i = 1; $i <= 50; $i++) {
            $data = [
                'title' => $testConfig->testPublication['title'] . ' ' . $i,
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
        }
        
        $startTime = microtime(true);
        
        $result = $this->get('publications');
        
        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;
        
        $result->assertOK();
        $this->assertLessThan(2.0, $loadTime, 'Publication list should load in less than 2 seconds');
    }

    public function testSearchPerformance()
    {
        $testConfig = config('Testing');
        $testFile = $this->createTestFile();
        
        // Create multiple test publications
        for ($i = 1; $i <= 100; $i++) {
            $data = [
                'title' => $testConfig->testPublication['title'] . ' ' . $i,
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
        }
        
        $startTime = microtime(true);
        
        $result = $this->get('publications/search?keyword=' . $testConfig->testPublication['title']);
        
        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;
        
        $result->assertOK();
        $this->assertLessThan(3.0, $loadTime, 'Search should complete in less than 3 seconds');
    }

    public function testFileUploadPerformance()
    {
        $testConfig = config('Testing');
        $testFile = $this->createTestFile();
        
        $startTime = microtime(true);
        
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
        
        $result = $this->post('publications/create', $data);
        
        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;
        
        $result->assertOK();
        $this->assertLessThan(5.0, $loadTime, 'File upload should complete in less than 5 seconds');
    }

    public function testDatabaseQueryPerformance()
    {
        $testConfig = config('Testing');
        
        $startTime = microtime(true);
        
        $publications = $this->publicationModel->findAll();
        
        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;
        
        $this->assertLessThan(1.0, $loadTime, 'Database query should complete in less than 1 second');
    }

    public function testApiResponseTime()
    {
        $testConfig = config('Testing');
        
        $startTime = microtime(true);
        
        $result = $this->get('api/departments/' . $testConfig->testPublication['college_id']);
        
        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;
        
        $result->assertOK();
        $this->assertLessThan(0.5, $loadTime, 'API response should be received in less than 0.5 seconds');
    }

    public function testPaginationPerformance()
    {
        // Test pagination performance with large dataset
        $startTime = microtime(true);
        $result = $this->get('publications?page=1');
        $endTime = microtime(true);
        
        $executionTime = $endTime - $startTime;
        $this->assertLessThan(0.5, $executionTime); // Should complete within 0.5 seconds
    }

    public function testRelatedPublicationsPerformance()
    {
        $startTime = microtime(true);
        $result = $this->get('publications/view/1');
        $endTime = microtime(true);
        
        $executionTime = $endTime - $startTime;
        $this->assertLessThan(0.5, $executionTime); // Should complete within 0.5 seconds
    }

    public function testCachePerformance()
    {
        // First request (uncached)
        $startTime = microtime(true);
        $result1 = $this->get('api/departments/by-college/1');
        $endTime = microtime(true);
        $firstRequestTime = $endTime - $startTime;

        // Second request (cached)
        $startTime = microtime(true);
        $result2 = $this->get('api/departments/by-college/1');
        $endTime = microtime(true);
        $secondRequestTime = $endTime - $startTime;

        // Cached request should be faster
        $this->assertLessThan($firstRequestTime, $secondRequestTime);
    }

    public function testImageOptimization()
    {
        // Test thumbnail generation performance
        $testImage = WRITEPATH . 'uploads/test.jpg';
        file_put_contents($testImage, str_repeat('Test image content', 1000));

        $startTime = microtime(true);
        $result = $this->post('publications/create', [
            'title' => 'Test Publication',
            'authors' => 'Test Author',
            'abstract' => 'Test Abstract',
            'keywords' => 'test',
            'college_id' => 1,
            'department_id' => 1,
            'program_id' => 1,
            'publication_date' => date('Y-m-d'),
            'thumbnail' => new \CodeIgniter\Files\File($testImage)
        ]);
        $endTime = microtime(true);
        
        $executionTime = $endTime - $startTime;
        $this->assertLessThan(1.0, $executionTime); // Should complete within 1 second

        unlink($testImage);
    }

    public function testConcurrentRequests()
    {
        $startTime = microtime(true);
        
        // Simulate concurrent requests
        $requests = [];
        for ($i = 0; $i < 10; $i++) {
            $requests[] = $this->get('publications/search?keyword=test');
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // All requests should complete within 2 seconds
        $this->assertLessThan(2.0, $executionTime);
    }
} 