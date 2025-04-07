# EVSU Research Archive Tutorial

## PDF Upload Implementation & AdminLTE Setup

### Table of Contents

1. [Introduction](#1-introduction)
2. [Setting Up CodeIgniter](#2-setting-up-codeigniter)
3. [Model Implementation](#3-model-implementation)
4. [Controller Implementation](#4-controller-implementation)
5. [View Implementation](#5-view-implementation)
6. [AdminLTE Integration](#6-adminlte-integration)
7. [Testing the Implementation](#7-testing-the-implementation)

---

## 1. Introduction

This tutorial provides step-by-step instructions for implementing PDF upload functionality in the EVSU Research Archive application using CodeIgniter 4's MVC architecture. It also covers the integration of AdminLTE for a modern user interface.

The tutorial is designed for developers who want to understand how to:

-   Set up a CodeIgniter 4 project
-   Create models for handling publication data
-   Implement controllers for processing PDF uploads
-   Design views for user interaction
-   Integrate AdminLTE for a responsive UI
-   Test the implementation

---

## 2. Setting Up CodeIgniter

### Prerequisites

-   PHP 7.4 or higher
-   Composer
-   MySQL/MariaDB
-   Web server (Apache/Nginx)

### Installation Steps

1. **Install CodeIgniter 4 via Composer**

    ```bash
    composer create-project codeigniter4/appstarter evsu-research-archive
    cd evsu-research-archive
    ```

2. **Configure Environment**

    ```bash
    cp env .env
    ```

    Edit the `.env` file to set:

    ```
    CI_ENVIRONMENT = development
    database.default.hostname = localhost
    database.default.database = evsu_research_archive
    database.default.username = your_username
    database.default.password = your_password
    database.default.DBDriver = MySQLi
    ```

3. **Create Database**

    ```sql
    CREATE DATABASE evsu_research_archive;
    ```

4. **Run Migrations**

    ```bash
    php spark migrate
    ```

5. **Start the Development Server**
    ```bash
    php spark serve
    ```

---

## 3. Model Implementation

The model handles data operations for publications, including storing metadata and file paths.

### Create Publication Model

1. **Generate Model File**

    ```bash
    php spark make:model PublicationModel
    ```

2. **Implement the Model**

    ```php
    <?php

    namespace App\Models;

    use CodeIgniter\Model;

    class PublicationModel extends Model
    {
        protected $table = 'publications';
        protected $primaryKey = 'id';
        protected $useAutoIncrement = true;
        protected $returnType = 'array';
        protected $allowedFields = [
            'title', 'authors', 'abstract', 'keywords',
            'college_id', 'department_id', 'program_id',
            'publication_date', 'file_path', 'thumbnail_path'
        ];
        protected $useTimestamps = true;
        protected $createdField = 'created_at';
        protected $updatedField = 'updated_at';
        protected $validationRules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'authors' => 'required|min_length[3]|max_length[255]',
            'abstract' => 'required|min_length[10]',
            'college_id' => 'required|numeric',
            'department_id' => 'required|numeric',
            'program_id' => 'required|numeric',
            'publication_date' => 'required|valid_date'
        ];
    }
    ```

3. **Create Migration for Publications Table**

    ```bash
    php spark make:migration CreatePublicationsTable
    ```

4. **Implement the Migration**

    ```php
    <?php

    namespace App\Database\Migrations;

    use CodeIgniter\Database\Migration;

    class CreatePublicationsTable extends Migration
    {
        public function up()
        {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'title' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ],
                'authors' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ],
                'abstract' => [
                    'type' => 'TEXT',
                ],
                'keywords' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ],
                'college_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'department_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'program_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'publication_date' => [
                    'type' => 'DATE',
                ],
                'file_path' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ],
                'thumbnail_path' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('college_id', 'colleges', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('department_id', 'departments', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('program_id', 'programs', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('publications');
        }

        public function down()
        {
            $this->forge->dropTable('publications');
        }
    }
    ```

5. **Run the Migration**
    ```bash
    php spark migrate
    ```

---

## 4. Controller Implementation

The controller handles the logic for processing PDF uploads and managing publications.

### Create Publication Controller

1. **Generate Controller File**

    ```bash
    php spark make:controller Publication
    ```

2. **Implement the Controller**

    ```php
    <?php

    namespace App\Controllers;

    use App\Models\PublicationModel;
    use App\Models\CollegeModel;
    use App\Models\DepartmentModel;
    use App\Models\ProgramModel;

    class Publication extends BaseController
    {
        protected $publicationModel;
        protected $collegeModel;
        protected $departmentModel;
        protected $programModel;
        protected $uploadPath;
        protected $allowedTypes = 'pdf';
        protected $maxSize = 10240; // 10MB

        public function __construct()
        {
            $this->publicationModel = new PublicationModel();
            $this->collegeModel = new CollegeModel();
            $this->departmentModel = new DepartmentModel();
            $this->programModel = new ProgramModel();
            $this->uploadPath = WRITEPATH . 'uploads/publications/';

            // Create upload directory if it doesn't exist
            if (!is_dir($this->uploadPath)) {
                mkdir($this->uploadPath, 0777, true);
            }
        }

        public function index()
        {
            $data = [
                'title' => 'Publications',
                'publications' => $this->publicationModel->findAll(),
            ];

            return view('publications/index', $data);
        }

        public function create()
        {
            $data = [
                'title' => 'Add Publication',
                'colleges' => $this->collegeModel->findAll(),
                'departments' => $this->departmentModel->findAll(),
                'programs' => $this->programModel->findAll(),
            ];

            return view('publications/create', $data);
        }

        public function store()
        {
            // Validate form data
            $rules = [
                'title' => 'required|min_length[3]|max_length[255]',
                'authors' => 'required|min_length[3]|max_length[255]',
                'abstract' => 'required|min_length[10]',
                'college_id' => 'required|numeric',
                'department_id' => 'required|numeric',
                'program_id' => 'required|numeric',
                'publication_date' => 'required|valid_date',
                'publication_file' => 'uploaded[publication_file]|max_size[publication_file,10240]|ext_in[publication_file,pdf]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Get the uploaded file
            $file = $this->request->getFile('publication_file');

            // Generate a unique filename
            $filename = $file->getRandomName();

            // Move the file to the upload directory
            if (!$file->move($this->uploadPath, $filename)) {
                return redirect()->back()->withInput()->with('error', 'Failed to upload file.');
            }

            // Create thumbnail (if needed)
            $thumbnailPath = null;

            // Save publication data to database
            $data = [
                'title' => $this->request->getPost('title'),
                'authors' => $this->request->getPost('authors'),
                'abstract' => $this->request->getPost('abstract'),
                'keywords' => $this->request->getPost('keywords'),
                'college_id' => $this->request->getPost('college_id'),
                'department_id' => $this->request->getPost('department_id'),
                'program_id' => $this->request->getPost('program_id'),
                'publication_date' => $this->request->getPost('publication_date'),
                'file_path' => 'uploads/publications/' . $filename,
                'thumbnail_path' => $thumbnailPath,
            ];

            if ($this->publicationModel->insert($data)) {
                return redirect()->to('/publications')->with('success', 'Publication added successfully.');
            } else {
                // Delete the uploaded file if database insertion fails
                unlink($this->uploadPath . $filename);
                return redirect()->back()->withInput()->with('error', 'Failed to save publication data.');
            }
        }

        public function view($id)
        {
            $publication = $this->publicationModel->find($id);

            if (!$publication) {
                return redirect()->to('/publications')->with('error', 'Publication not found.');
            }

            $data = [
                'title' => $publication['title'],
                'publication' => $publication,
            ];

            return view('publications/view', $data);
        }

        public function download($id)
        {
            $publication = $this->publicationModel->find($id);

            if (!$publication) {
                return redirect()->to('/publications')->with('error', 'Publication not found.');
            }

            $filePath = FCPATH . $publication['file_path'];

            if (!file_exists($filePath)) {
                return redirect()->to('/publications')->with('error', 'File not found.');
            }

            return $this->response->download($filePath, null)->setFileName(basename($filePath));
        }
    }
    ```

3. **Update Routes**
   Edit `app/Config/Routes.php` to add routes for the publication controller:
    ```php
    $routes->get('publications', 'Publication::index');
    $routes->get('publications/create', 'Publication::create');
    $routes->post('publications', 'Publication::store');
    $routes->get('publications/view/(:num)', 'Publication::view/$1');
    $routes->get('publications/download/(:num)', 'Publication::download/$1');
    ```

---

## 5. View Implementation

The views provide the user interface for uploading and managing publications.

### Create Publication Views

1. **Create Index View**
   Create `app/Views/publications/index.php`:

    ```php
    <?= $this->extend('layouts/main') ?>

    <?= $this->section('content') ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Publications</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('publications/create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Publication
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Authors</th>
                                    <th>Publication Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($publications)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No publications found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($publications as $publication): ?>
                                        <tr>
                                            <td><?= esc($publication['title']) ?></td>
                                            <td><?= esc($publication['authors']) ?></td>
                                            <td><?= date('F d, Y', strtotime($publication['publication_date'])) ?></td>
                                            <td>
                                                <a href="<?= base_url('publications/view/' . $publication['id']) ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="<?= base_url('publications/download/' . $publication['id']) ?>" class="btn btn-success btn-sm">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->endSection() ?>
    ```

2. **Create Form View**
   Create `app/Views/publications/create.php`:

    ```php
    <?= $this->extend('layouts/main') ?>

    <?= $this->section('content') ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add Publication</h3>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('publications') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>

                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="authors">Authors</label>
                                <input type="text" class="form-control" id="authors" name="authors" value="<?= old('authors') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="abstract">Abstract</label>
                                <textarea class="form-control" id="abstract" name="abstract" rows="5" required><?= old('abstract') ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="keywords">Keywords (comma separated)</label>
                                <input type="text" class="form-control" id="keywords" name="keywords" value="<?= old('keywords') ?>">
                            </div>

                            <div class="form-group">
                                <label for="college_id">College</label>
                                <select class="form-control" id="college_id" name="college_id" required>
                                    <option value="">Select College</option>
                                    <?php foreach ($colleges as $college): ?>
                                        <option value="<?= $college['id'] ?>" <?= old('college_id') == $college['id'] ? 'selected' : '' ?>>
                                            <?= esc($college['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="department_id">Department</label>
                                <select class="form-control" id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?= $department['id'] ?>" <?= old('department_id') == $department['id'] ? 'selected' : '' ?>>
                                            <?= esc($department['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="program_id">Program</label>
                                <select class="form-control" id="program_id" name="program_id" required>
                                    <option value="">Select Program</option>
                                    <?php foreach ($programs as $program): ?>
                                        <option value="<?= $program['id'] ?>" <?= old('program_id') == $program['id'] ? 'selected' : '' ?>>
                                            <?= esc($program['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="publication_date">Publication Date</label>
                                <input type="date" class="form-control" id="publication_date" name="publication_date" value="<?= old('publication_date') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="publication_file">Publication File (PDF)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="publication_file" name="publication_file" accept=".pdf" required>
                                    <label class="custom-file-label" for="publication_file">Choose file</label>
                                </div>
                                <small class="form-text text-muted">Maximum file size: 10MB</small>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="<?= base_url('publications') ?>" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Display the selected filename
        document.getElementById('publication_file').addEventListener('change', function() {
            var fileName = this.files[0].name;
            var nextSibling = this.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    </script>
    <?= $this->endSection() ?>
    ```

3. **Create View Publication Page**
   Create `app/Views/publications/view.php`:

    ```php
    <?= $this->extend('layouts/main') ?>

    <?= $this->section('content') ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?= esc($publication['title']) ?></h3>
                        <div class="card-tools">
                            <a href="<?= base_url('publications/download/' . $publication['id']) ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                            <a href="<?= base_url('publications') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Authors</h5>
                                <p><?= esc($publication['authors']) ?></p>

                                <h5>Abstract</h5>
                                <p><?= nl2br(esc($publication['abstract'])) ?></p>

                                <h5>Keywords</h5>
                                <p>
                                    <?php
                                    $keywords = explode(',', $publication['keywords']);
                                    foreach ($keywords as $keyword):
                                        echo '<span class="badge badge-info mr-1">' . esc(trim($keyword)) . '</span>';
                                    endforeach;
                                    ?>
                                </p>

                                <h5>Publication Date</h5>
                                <p><?= date('F d, Y', strtotime($publication['publication_date'])) ?></p>
                            </div>

                            <div class="col-md-4">
                                <?php if ($publication['thumbnail_path']): ?>
                                    <img src="<?= base_url($publication['thumbnail_path']) ?>" class="img-fluid mb-3" alt="Publication Thumbnail">
                                <?php else: ?>
                                    <div class="text-center p-5 bg-light">
                                        <i class="fas fa-file-pdf fa-5x text-muted"></i>
                                        <p class="mt-3">No thumbnail available</p>
                                    </div>
                                <?php endif; ?>

                                <div class="mt-3">
                                    <a href="<?= base_url('publications/download/' . $publication['id']) ?>" class="btn btn-success btn-block">
                                        <i class="fas fa-download"></i> Download PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->endSection() ?>
    ```

---

## 6. AdminLTE Integration

AdminLTE provides a modern, responsive UI for the application.

### Setup AdminLTE

1. **Create Main Layout**
   Create `app/Views/layouts/main.php`:

    ```php
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>EVSU Research Archive</title>

      <!-- Google Font: Source Sans Pro -->
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
      <!-- Theme style -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
      <!-- Custom styles -->
      <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
    </head>
    <body class="hold-transition sidebar-mini">
    <div class="wrapper">

      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
              <i class="fas fa-expand-arrows-alt"></i>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="<?= base_url() ?>" class="brand-link">
          <img src="<?= base_url('assets/img/evsu-logo.png') ?>" alt="EVSU Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
          <span class="brand-text font-weight-light">EVSU Research</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
              <img src="<?= base_url('assets/img/user-default.png') ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
              <a href="#" class="d-block"><?= session()->get('username') ?? 'Guest' ?></a>
            </div>
          </div>

          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
              <li class="nav-item">
                <a href="<?= base_url() ?>" class="nav-link <?= current_url() == base_url() ? 'active' : '' ?>">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('publications') ?>" class="nav-link <?= strpos(current_url(), 'publications') !== false ? 'active' : '' ?>">
                  <i class="nav-icon fas fa-book"></i>
                  <p>Publications</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('about') ?>" class="nav-link <?= strpos(current_url(), 'about') !== false ? 'active' : '' ?>">
                  <i class="nav-icon fas fa-info-circle"></i>
                  <p>About</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('contact') ?>" class="nav-link <?= strpos(current_url(), 'contact') !== false ? 'active' : '' ?>">
                  <i class="nav-icon fas fa-envelope"></i>
                  <p>Contact</p>
                </a>
              </li>
            </ul>
          </nav>
          <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0"><?= $title ?? 'Dashboard' ?></h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                  <?php if (isset($breadcrumbs)): ?>
                    <?php foreach ($breadcrumbs as $label => $url): ?>
                      <li class="breadcrumb-item <?= $url ? '' : 'active' ?>">
                        <?php if ($url): ?>
                          <a href="<?= $url ?>"><?= $label ?></a>
                        <?php else: ?>
                          <?= $label ?>
                        <?php endif; ?>
                      </li>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
          <div class="container-fluid">
            <?= $this->renderSection('content') ?>
          </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->

      <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">
          v1.0.0
        </div>
        <strong>Copyright &copy; <?= date('Y') ?> <a href="https://www.evsu.edu.ph">Eastern Visayas State University</a>.</strong> All rights reserved.
      </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <!-- Custom scripts -->
    <script src="<?= base_url('assets/js/custom.js') ?>"></script>
    </body>
    </html>
    ```

2. **Create Custom CSS**
   Create `public/assets/css/custom.css`:

    ```css
    /* EVSU Research Archive Custom Styles */

    /* Brand colors */
    :root {
        --evsu-primary: #003366;
        --evsu-secondary: #0066cc;
        --evsu-accent: #ff9900;
        --evsu-light: #f8f9fa;
        --evsu-dark: #343a40;
    }

    /* Override AdminLTE primary colors */
    .sidebar-dark-primary {
        background-color: var(--evsu-primary) !important;
    }

    .bg-primary {
        background-color: var(--evsu-primary) !important;
    }

    .text-primary {
        color: var(--evsu-primary) !important;
    }

    .btn-primary {
        background-color: var(--evsu-primary) !important;
        border-color: var(--evsu-primary) !important;
    }

    .btn-primary:hover {
        background-color: var(--evsu-secondary) !important;
        border-color: var(--evsu-secondary) !important;
    }

    /* Custom card styles */
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
    }

    .card-header {
        background-color: var(--evsu-light);
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        padding: 1rem;
    }

    .card-title {
        color: var(--evsu-primary);
        font-weight: 600;
        margin-bottom: 0;
    }

    /* Publication card styles */
    .publication-card {
        transition: transform 0.3s ease;
    }

    .publication-card:hover {
        transform: translateY(-5px);
    }

    .publication-card .card-img-top {
        height: 200px;
        object-fit: cover;
    }

    .publication-card .card-title {
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
    }

    .publication-card .card-text {
        color: #6c757d;
    }

    .publication-card .badge {
        margin-right: 0.5rem;
    }

    /* Search form styles */
    .search-form {
        background-color: var(--evsu-light);
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .search-form .form-control {
        border-radius: 0.25rem;
    }

    .search-form .btn {
        border-radius: 0.25rem;
    }

    /* Table styles */
    .table thead th {
        background-color: var(--evsu-light);
        border-bottom: 2px solid #dee2e6;
        color: var(--evsu-primary);
        font-weight: 600;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 102, 204, 0.05);
    }

    /* Pagination styles */
    .pagination .page-item.active .page-link {
        background-color: var(--evsu-primary);
        border-color: var(--evsu-primary);
    }

    .pagination .page-link {
        color: var(--evsu-primary);
    }

    .pagination .page-link:hover {
        background-color: var(--evsu-light);
        color: var(--evsu-secondary);
    }

    /* Form styles */
    .form-control:focus {
        border-color: var(--evsu-secondary);
        box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
    }

    /* Alert styles */
    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
    }

    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }

    /* Footer styles */
    .main-footer {
        background-color: var(--evsu-light);
        border-top: 1px solid #dee2e6;
        color: var(--evsu-dark);
        padding: 1rem 0;
    }

    .main-footer a {
        color: var(--evsu-primary);
    }

    .main-footer a:hover {
        color: var(--evsu-secondary);
        text-decoration: none;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-title {
            font-size: 1.1rem;
        }

        .publication-card .card-img-top {
            height: 150px;
        }

        .search-form {
            padding: 1rem;
        }
    }
    ```

3. **Create Custom JavaScript**
   Create `public/assets/js/custom.js`:

    ```javascript
    // EVSU Research Archive Custom JavaScript

    $(document).ready(function() {
      // Initialize tooltips
      $('[data-toggle="tooltip"]').tooltip();

      // Initialize popovers
      $('[data-toggle="popover"]').popover();

      // Handle file input change
      $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
      });

      // Handle college change to load departments
      $('#college_id').on('change', function() {
        var collegeId = $(this).val();
        if (collegeId) {
          $.ajax({
            url: '<?= base_url('api/departments') ?>',
            type: 'GET',
            data: { college_id: collegeId },
            dataType: 'json',
            success: function(data) {
              $('#department_id').empty();
              $('#department_id').append('<option value="">Select Department</option>');
              $.each(data, function(key, value) {
                $('#department_id').append('<option value="' + value.id + '">' + value.name + '</option>');
              });
            }
          });
        } else {
          $('#department_id').empty();
          $('#department_id').append('<option value="">Select Department</option>');
        }
      });

      // Handle department change to load programs
      $('#department_id').on('change', function() {
        var departmentId = $(this).val();
        if (departmentId) {
          $.ajax({
            url: '<?= base_url('api/programs') ?>',
            type: 'GET',
            data: { department_id: departmentId },
            dataType: 'json',
            success: function(data) {
              $('#program_id').empty();
              $('#program_id').append('<option value="">Select Program</option>');
              $.each(data, function(key, value) {
                $('#program_id').append('<option value="' + value.id + '">' + value.name + '</option>');
              });
            }
          });
        } else {
          $('#program_id').empty();
          $('#program_id').append('<option value="">Select Program</option>');
        }
      });
    });
    ```

4. **Create Required Directories**

    ```bash
    mkdir -p public/assets/img
    mkdir -p public/assets/css
    mkdir -p public/assets/js
    ```

5. **Add EVSU Logo**
   Place the EVSU logo in `public/assets/img/evsu-logo.png`.

---

## 7. Testing the Implementation

### Create Test for PDF Upload

1. **Generate Test File**

    ```bash
    php spark make:test PublicationTest
    ```

2. **Implement the Test**

    ```php
    <?php

    namespace Tests;

    use CodeIgniter\Test\CIUnitTestCase;
    use CodeIgniter\Test\DatabaseTestTrait;
    use CodeIgniter\Test\FeatureTestTrait;

    class PublicationTest extends CIUnitTestCase
    {
        use DatabaseTestTrait;
        use FeatureTestTrait;

        protected $seed = 'TestSeeder';

        public function setUp(): void
        {
            parent::setUp();
            $this->createTestSession();
        }

        public function testCreatePublication()
        {
            $result = $this->post('publications', [
                'title' => 'Test Publication',
                'authors' => 'Test Author',
                'abstract' => 'This is a test abstract for the publication.',
                'keywords' => 'test, publication',
                'college_id' => 1,
                'department_id' => 1,
                'program_id' => 1,
                'publication_date' => date('Y-m-d'),
                'publication_file' => $this->createTestFile('test.pdf', 'application/pdf')
            ]);

            $result->assertOK();
            $result->assertRedirect();
            $result->assertSessionHas('success');

            $this->assertDatabaseHas('publications', [
                'title' => 'Test Publication',
                'authors' => 'Test Author'
            ]);
        }

        public function testViewPublication()
        {
            // Create a test publication first
            $publicationId = $this->createTestPublication();

            $result = $this->get('publications/view/' . $publicationId);

            $result->assertOK();
            $result->assertSee('Test Publication');
            $result->assertSee('Test Author');
        }

        public function testDownloadPublication()
        {
            // Create a test publication first
            $publicationId = $this->createTestPublication();

            $result = $this->get('publications/download/' . $publicationId);

            $result->assertOK();
            $result->assertHeader('Content-Type', 'application/pdf');
            $result->assertHeader('Content-Disposition', 'attachment');
        }

        protected function createTestSession()
        {
            $session = session();
            $session->set([
                'user_id' => 1,
                'username' => 'testuser',
                'role' => 'admin',
                'logged_in' => true
            ]);
        }

        protected function createTestFile($filename, $mimeType)
        {
            $path = WRITEPATH . 'uploads/' . $filename;
            file_put_contents($path, 'Test file content');
            return new \CodeIgniter\Files\File($path, true);
        }

        protected function createTestPublication()
        {
            $publicationModel = new \App\Models\PublicationModel();

            $data = [
                'title' => 'Test Publication',
                'authors' => 'Test Author',
                'abstract' => 'This is a test abstract for the publication.',
                'keywords' => 'test, publication',
                'college_id' => 1,
                'department_id' => 1,
                'program_id' => 1,
                'publication_date' => date('Y-m-d'),
                'file_path' => 'uploads/publications/test.pdf'
            ];

            $publicationId = $publicationModel->insert($data);
            return $publicationId;
        }
    }
    ```

3. **Run the Test**
    ```bash
    php spark test tests/PublicationTest.php
    ```

---

## Conclusion

This tutorial has covered the implementation of PDF upload functionality in the EVSU Research Archive application using CodeIgniter 4's MVC architecture. It has also included the integration of AdminLTE for a modern, responsive user interface.

The implementation includes:

-   Setting up a CodeIgniter 4 project
-   Creating a model for handling publication data
-   Implementing a controller for processing PDF uploads
-   Designing views for user interaction
-   Integrating AdminLTE for a responsive UI
-   Testing the implementation

By following this tutorial, you should have a fully functional PDF upload system in your EVSU Research Archive application.
