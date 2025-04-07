<?php

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\PublicationModel;

class ViewTest extends CIUnitTestCase
{
    use TestTrait;

    protected $publicationModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->publicationModel = new PublicationModel();
        
        $this->createTestSession();
    }

    public function testHomePage()
    {
        $result = $this->get('/');
        
        $result->assertOK();
        $result->assertSee('EVSU Research Archive');
        $result->assertSee('Home');
        $result->assertSee('Publications');
        $result->assertSee('About');
        $result->assertSee('Contact');
        $result->assertSee('Search');
    }

    public function testAboutPage()
    {
        $result = $this->get('about');
        
        $result->assertOK();
        $result->assertSee('About EVSU Research Archive');
        $result->assertSee('Mission');
        $result->assertSee('Vision');
        $result->assertSee('Objectives');
    }

    public function testContactPage()
    {
        $result = $this->get('contact');
        
        $result->assertOK();
        $result->assertSee('Contact Us');
        $result->assertSee('Name');
        $result->assertSee('Email');
        $result->assertSee('Subject');
        $result->assertSee('Message');
        $result->assertSee('Send Message');
    }

    public function testPublicationListPage()
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
        
        $result = $this->get('publications');
        
        $result->assertOK();
        $result->assertSee('Publications');
        $result->assertSee('Search');
        $result->assertSee('Create Publication');
        $result->assertSee($testConfig->testPublication['title']);
        $result->assertSee($testConfig->testPublication['authors']);
    }

    public function testPublicationCreatePage()
    {
        $result = $this->get('publications/create');
        
        $result->assertOK();
        $result->assertSee('Create Publication');
        $result->assertSee('Title');
        $result->assertSee('Authors');
        $result->assertSee('Abstract');
        $result->assertSee('Keywords');
        $result->assertSee('College');
        $result->assertSee('Department');
        $result->assertSee('Program');
        $result->assertSee('Publication Date');
        $result->assertSee('File');
        $result->assertSee('Submit');
    }

    public function testPublicationViewPage()
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
        
        $result = $this->get('publications/view/' . $publication->id);
        
        $result->assertOK();
        $result->assertSee($testConfig->testPublication['title']);
        $result->assertSee($testConfig->testPublication['authors']);
        $result->assertSee($testConfig->testPublication['abstract']);
        $result->assertSee($testConfig->testPublication['keywords']);
        $result->assertSee('Download');
        $result->assertSee('Edit');
        $result->assertSee('Delete');
    }

    public function testPublicationEditPage()
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
        
        $result = $this->get('publications/edit/' . $publication->id);
        
        $result->assertOK();
        $result->assertSee('Edit Publication');
        $result->assertSee($testConfig->testPublication['title']);
        $result->assertSee($testConfig->testPublication['authors']);
        $result->assertSee($testConfig->testPublication['abstract']);
        $result->assertSee($testConfig->testPublication['keywords']);
        $result->assertSee('Submit');
    }

    public function testPublicationSearchPage()
    {
        $result = $this->get('publications/search');
        
        $result->assertOK();
        $result->assertSee('Search Publications');
        $result->assertSee('Keyword');
        $result->assertSee('College');
        $result->assertSee('Department');
        $result->assertSee('Program');
        $result->assertSee('Date Range');
        $result->assertSee('Search');
    }
} 