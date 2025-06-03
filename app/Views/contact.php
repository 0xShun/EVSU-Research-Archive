<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center mb-4">Contact Us</h1>
            
            <?php if (session()->has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h4 text-danger mb-4">Contact Information</h2>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <strong>Address:</strong><br>
                                    Eastern Visayas State University<br>
                                    Tacloban City, Leyte<br>
                                    Philippines
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-phone text-danger me-2"></i>
                                    <strong>Phone:</strong><br>
                                    (053) 321-1084
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-envelope text-danger me-2"></i>
                                    <strong>Email:</strong><br>
                                    research@evsu.edu.ph
                                </li>
                                <li>
                                    <i class="fas fa-clock text-danger me-2"></i>
                                    <strong>Office Hours:</strong><br>
                                    Monday - Friday: 8:00 AM - 5:00 PM
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4 text-danger mb-4">Send us a Message</h2>
                            <form action="<?= base_url('contact') ?>" method="post">
                                <?= csrf_field() ?>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="subject" name="subject" value="<?= old('subject') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required><?= old('message') ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 