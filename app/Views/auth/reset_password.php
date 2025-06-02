<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">Set New Password</h3>
                </div>
                <div class="card-body p-4">
                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                        <p class="text-muted">
                            Please enter your new password below.
                        </p>
                    </div>

                    <form action="<?= base_url('auth/reset-password') ?>" method="post" class="needs-validation" novalidate>
                        <?= csrf_field() ?>
                        
                        <input type="hidden" name="email" value="<?= $email ?>">
                        <input type="hidden" name="token" value="<?= $token ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength mt-2">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="form-text text-muted mt-1">
                                    Password must contain:
                                    <ul class="mb-0 ps-3">
                                        <li id="length" class="text-danger">At least 8 characters</li>
                                        <li id="uppercase" class="text-danger">One uppercase letter</li>
                                        <li id="lowercase" class="text-danger">One lowercase letter</li>
                                        <li id="number" class="text-danger">One number</li>
                                        <li id="special" class="text-danger">One special character</li>
                                    </ul>
                                </small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirm" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password_confirm" 
                                       name="password_confirm" required>
                            </div>
                            <div class="invalid-feedback">Passwords do not match.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Reset Password
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">
                        Remember your password? 
                        <a href="<?= base_url('auth/login') ?>" class="text-decoration-none">
                            Login here
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.needs-validation');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');
    const togglePassword = document.getElementById('togglePassword');
    const progressBar = document.querySelector('.progress-bar');
    const requirements = {
        length: document.getElementById('length'),
        uppercase: document.getElementById('uppercase'),
        lowercase: document.getElementById('lowercase'),
        number: document.getElementById('number'),
        special: document.getElementById('special')
    };

    // Form validation
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity() || password.value !== passwordConfirm.value) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Password visibility toggle
    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Password strength checker
    password.addEventListener('input', function() {
        const value = this.value;
        let strength = 0;
        let totalRequirements = 0;

        // Check length
        if (value.length >= 8) {
            requirements.length.classList.remove('text-danger');
            requirements.length.classList.add('text-success');
            strength += 20;
        } else {
            requirements.length.classList.remove('text-success');
            requirements.length.classList.add('text-danger');
        }
        totalRequirements++;

        // Check uppercase
        if (/[A-Z]/.test(value)) {
            requirements.uppercase.classList.remove('text-danger');
            requirements.uppercase.classList.add('text-success');
            strength += 20;
        } else {
            requirements.uppercase.classList.remove('text-success');
            requirements.uppercase.classList.add('text-danger');
        }
        totalRequirements++;

        // Check lowercase
        if (/[a-z]/.test(value)) {
            requirements.lowercase.classList.remove('text-danger');
            requirements.lowercase.classList.add('text-success');
            strength += 20;
        } else {
            requirements.lowercase.classList.remove('text-success');
            requirements.lowercase.classList.add('text-danger');
        }
        totalRequirements++;

        // Check number
        if (/[0-9]/.test(value)) {
            requirements.number.classList.remove('text-danger');
            requirements.number.classList.add('text-success');
            strength += 20;
        } else {
            requirements.number.classList.remove('text-success');
            requirements.number.classList.add('text-danger');
        }
        totalRequirements++;

        // Check special character
        if (/[^A-Za-z0-9]/.test(value)) {
            requirements.special.classList.remove('text-danger');
            requirements.special.classList.add('text-success');
            strength += 20;
        } else {
            requirements.special.classList.remove('text-success');
            requirements.special.classList.add('text-danger');
        }
        totalRequirements++;

        // Update progress bar
        progressBar.style.width = strength + '%';
        
        // Update progress bar color
        if (strength <= 40) {
            progressBar.className = 'progress-bar bg-danger';
        } else if (strength <= 80) {
            progressBar.className = 'progress-bar bg-warning';
        } else {
            progressBar.className = 'progress-bar bg-success';
        }
    });

    // Password confirmation validation
    passwordConfirm.addEventListener('input', function() {
        if (this.value !== password.value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
<?= $this->endSection() ?> 