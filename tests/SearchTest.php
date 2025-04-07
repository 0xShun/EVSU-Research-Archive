<?php

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\PublicationModel;

class SearchTest extends CIUnitTestCase
{
    use TestTrait;

    protected $publicationModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->publicationModel = new PublicationModel();
        
        $this->createTestSession();
    }

    public function testBasicSearch()
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
        
        // Test basic search
        $result = $this->get('publications/search?keyword=' . urlencode($testConfig->testPublication['title']));
        
        $result->assertOK();
        $result->assertSee($testConfig->testPublication['title']);
        $result->assertSee($testConfig->testPublication['authors']);
    }

    public function testAdvancedSearch()
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
        
        // Test advanced search
        $searchData = [
            'keyword' => $testConfig->testPublication['title'],
            'college_id' => $testConfig->testPublication['college_id'],
            'department_id' => $testConfig->testPublication['department_id'],
            'program_id' => $testConfig->testPublication['program_id'],
            'start_date' => date('Y-m-d', strtotime('-1 year')),
            'end_date' => date('Y-m-d')
        ];
        
        $result = $this->post('publications/search', $searchData);
        
        $result->assertOK();
        $result->assertSee($testConfig->testPublication['title']);
        $result->assertSee($testConfig->testPublication['authors']);
    }

    public function testSearchFilters()
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
        
        // Test search with college filter
        $result = $this->get('publications/search?college_id=' . $testConfig->testPublication['college_id']);
        
        $result->assertOK();
        $result->assertSee($testConfig->testPublication['title']);
        
        // Test search with department filter
        $result = $this->get('publications/search?department_id=' . $testConfig->testPublication['department_id']);
        
        $result->assertOK();
        $result->assertSee($testConfig->testPublication['title']);
        
        // Test search with program filter
        $result = $this->get('publications/search?program_id=' . $testConfig->testPublication['program_id']);
        
        $result->assertOK();
        $result->assertSee($testConfig->testPublication['title']);
        
        // Test search with date range filter
        $result = $this->get('publications/search?start_date=' . date('Y-m-d', strtotime('-1 year')) . '&end_date=' . date('Y-m-d'));
        
        $result->assertOK();
        $result->assertSee($testConfig->testPublication['title']);
    }

    public function testSearchResults()
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
        
        // Test search results
        $result = $this->get('publications/search?keyword=' . urlencode($testConfig->testPublication['title']));
        
        $result->assertOK();
        $result->assertSee($testConfig->testPublication['title']);
        $result->assertSee($testConfig->testPublication['authors']);
        $result->assertSee($testConfig->testPublication['abstract']);
        $result->assertSee('Download');
        $result->assertSee('View');
    }

    public function testSearchPagination()
    {
        $testConfig = config('Testing');
        
        // Create multiple test publications
        for ($i = 1; $i <= 15; $i++) {
            $testFile = $this->createTestFile();
            
            $data = [
                'title' => $testConfig->testPublication['title'] . ' ' . $i,
                'authors' => $testConfig->testPublication['authors'] . ' ' . $i,
                'abstract' => $testConfig->testPublication['abstract'] . ' ' . $i,
                'keywords' => $testConfig->testPublication['keywords'] . ',test' . $i,
                'college_id' => $testConfig->testPublication['college_id'],
                'department_id' => $testConfig->testPublication['department_id'],
                'program_id' => $testConfig->testPublication['program_id'],
                'publication_date' => $testConfig->testPublication['publication_date'],
                'publication_file' => $this->simulateFileUpload($testFile)
            ];
            
            $this->post('publications/create', $data);
        }
        
        // Test pagination
        $result = $this->get('publications/search?keyword=test');
        
        $result->assertOK();
        $result->assertSee('Showing 1 to 10 of 15 entries');
        $result->assertSee('Next');
        
        // Test second page
        $result = $this->get('publications/search?keyword=test&page=2');
        
        $result->assertOK();
        $result->assertSee('Showing 11 to 15 of 15 entries');
        $result->assertSee('Previous');
    }
} 