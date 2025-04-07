<?php

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\PublicationModel;
use App\Models\CollegeModel;
use App\Models\DepartmentModel;
use App\Models\ProgramModel;

class DatabaseTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $seed = 'TestDataSeeder';

    public function testDatabaseConnection()
    {
        $db = \Config\Database::connect();
        $this->assertTrue($db->connect(false) !== false);
    }

    public function testPublicationsTable()
    {
        $model = new PublicationModel();
        $this->assertTrue($model->tableExists());
        
        // Test required fields
        $fields = $model->getFields();
        $this->assertArrayHasKey('title', $fields);
        $this->assertArrayHasKey('authors', $fields);
        $this->assertArrayHasKey('abstract', $fields);
        $this->assertArrayHasKey('keywords', $fields);
        $this->assertArrayHasKey('college_id', $fields);
        $this->assertArrayHasKey('department_id', $fields);
        $this->assertArrayHasKey('program_id', $fields);
        $this->assertArrayHasKey('publication_date', $fields);
        $this->assertArrayHasKey('file_path', $fields);
        $this->assertArrayHasKey('thumbnail', $fields);
    }

    public function testCollegesTable()
    {
        $model = new CollegeModel();
        $this->assertTrue($model->tableExists());
        
        // Test required fields
        $fields = $model->getFields();
        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('code', $fields);
        $this->assertArrayHasKey('description', $fields);
    }

    public function testDepartmentsTable()
    {
        $model = new DepartmentModel();
        $this->assertTrue($model->tableExists());
        
        // Test required fields
        $fields = $model->getFields();
        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('code', $fields);
        $this->assertArrayHasKey('college_id', $fields);
        $this->assertArrayHasKey('description', $fields);
    }

    public function testProgramsTable()
    {
        $model = new ProgramModel();
        $this->assertTrue($model->tableExists());
        
        // Test required fields
        $fields = $model->getFields();
        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('code', $fields);
        $this->assertArrayHasKey('department_id', $fields);
        $this->assertArrayHasKey('description', $fields);
    }

    public function testForeignKeyConstraints()
    {
        // Test department-college relationship
        $departmentModel = new DepartmentModel();
        $collegeModel = new CollegeModel();
        
        $college = $collegeModel->first();
        $this->assertNotNull($college);
        
        $department = $departmentModel->where('college_id', $college->id)->first();
        $this->assertNotNull($department);
        
        // Test program-department relationship
        $programModel = new ProgramModel();
        $program = $programModel->where('department_id', $department->id)->first();
        $this->assertNotNull($program);
    }

    public function testPublicationValidation()
    {
        $model = new PublicationModel();
        
        // Test required fields
        $data = [
            'title' => '',
            'authors' => '',
            'abstract' => '',
            'college_id' => '',
            'department_id' => '',
            'program_id' => '',
            'publication_date' => ''
        ];
        
        $this->assertFalse($model->validate($data));
        
        // Test valid data
        $data = [
            'title' => 'Test Publication',
            'authors' => 'Test Author',
            'abstract' => 'Test Abstract',
            'college_id' => 1,
            'department_id' => 1,
            'program_id' => 1,
            'publication_date' => date('Y-m-d')
        ];
        
        $this->assertTrue($model->validate($data));
    }
} 