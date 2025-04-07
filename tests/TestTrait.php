<?php

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

trait TestTrait
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $seed = 'TestDataSeeder';
    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set environment to testing
        putenv('CI_ENVIRONMENT=testing');
        
        // Load test configuration
        $this->loadTestConfig();
        
        // Create test directories
        $this->createTestDirectories();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clean up test files
        $this->cleanupTestFiles();
    }

    protected function loadTestConfig(): void
    {
        // Load test configuration
        $testConfig = new \Config\Testing();
        
        // Set test configuration
        config('Testing', $testConfig);
    }

    protected function createTestDirectories(): void
    {
        $testConfig = config('Testing');
        
        $directories = [
            $testConfig->testUploadPath,
            $testConfig->testFilePath,
            $testConfig->testImagePath,
            $testConfig->testThumbnailPath
        ];
        
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
        }
    }

    protected function cleanupTestFiles(): void
    {
        $testConfig = config('Testing');
        
        $directories = [
            $testConfig->testUploadPath,
            $testConfig->testFilePath,
            $testConfig->testImagePath,
            $testConfig->testThumbnailPath
        ];
        
        foreach ($directories as $directory) {
            if (is_dir($directory)) {
                $files = glob($directory . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }

    protected function createTestFile(string $content = 'Test content', string $extension = 'pdf'): string
    {
        $testConfig = config('Testing');
        $filename = 'test_' . uniqid() . '.' . $extension;
        $filepath = $testConfig->testUploadPath . $filename;
        
        file_put_contents($filepath, $content);
        
        return $filepath;
    }

    protected function createTestImage(int $width = 800, int $height = 600): string
    {
        $testConfig = config('Testing');
        $filename = 'test_' . uniqid() . '.jpg';
        $filepath = $testConfig->testImagePath . $filename;
        
        $image = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgColor);
        
        imagejpeg($image, $filepath);
        imagedestroy($image);
        
        return $filepath;
    }

    protected function simulateFileUpload(string $filepath): array
    {
        return [
            'name' => basename($filepath),
            'type' => mime_content_type($filepath),
            'tmp_name' => $filepath,
            'error' => 0,
            'size' => filesize($filepath)
        ];
    }

    protected function createTestSession(): void
    {
        $testConfig = config('Testing');
        $session = session();
        
        $session->set([
            'user_id' => $testConfig->testUser['id'],
            'username' => $testConfig->testUser['username'],
            'email' => $testConfig->testUser['email'],
            'role' => $testConfig->testUser['role'],
            'logged_in' => true
        ]);
    }

    protected function clearTestSession(): void
    {
        session()->destroy();
    }

    protected function assertSuccessMessage(string $message): void
    {
        $this->assertStringContainsString($message, $this->response->getBody());
    }

    protected function assertErrorMessage(string $message): void
    {
        $this->assertStringContainsString($message, $this->response->getBody());
    }

    protected function assertJsonResponse(): void
    {
        $this->assertResponseHeader('Content-Type', 'application/json');
    }

    protected function assertHtmlResponse(): void
    {
        $this->assertResponseHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    protected function assertResponseStatus(int $status): void
    {
        $this->assertResponseStatus($status);
    }

    protected function assertDatabaseHas(string $table, array $data): void
    {
        $db = \Config\Database::connect();
        $builder = $db->table($table);
        
        foreach ($data as $key => $value) {
            $builder->where($key, $value);
        }
        
        $this->assertGreaterThan(0, $builder->countAllResults());
    }

    protected function assertDatabaseMissing(string $table, array $data): void
    {
        $db = \Config\Database::connect();
        $builder = $db->table($table);
        
        foreach ($data as $key => $value) {
            $builder->where($key, $value);
        }
        
        $this->assertEquals(0, $builder->countAllResults());
    }
} 