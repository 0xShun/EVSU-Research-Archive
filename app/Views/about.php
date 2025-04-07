<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h1 class="text-center mb-4">About EVSU Research Archive</h1>
            
            <div class="card mb-5">
                <div class="card-body">
                    <h2 class="h4 text-danger">Our Mission</h2>
                    <p>The EVSU Research Archive is dedicated to preserving and disseminating the scholarly work of Eastern Visayas State University's academic community. Our mission is to provide open access to research publications, fostering knowledge sharing and academic collaboration.</p>
                </div>
            </div>

            <div class="card mb-5">
                <div class="card-body">
                    <h2 class="h4 text-danger">About EVSU</h2>
                    <p>Eastern Visayas State University (EVSU) is a state university in Tacloban City, Philippines. As a leading institution of higher learning in the region, EVSU is committed to:</p>
                    <ul>
                        <li>Providing quality education</li>
                        <li>Conducting innovative research</li>
                        <li>Promoting community engagement</li>
                        <li>Developing globally competitive graduates</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-5">
                <div class="card-body">
                    <h2 class="h4 text-danger">Research Focus Areas</h2>
                    <p>Our research archive covers various disciplines including:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul>
                                <li>Engineering and Technology</li>
                                <li>Natural Sciences</li>
                                <li>Social Sciences</li>
                                <li>Education</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li>Business and Management</li>
                                <li>Arts and Humanities</li>
                                <li>Health Sciences</li>
                                <li>Environmental Studies</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="h4 text-danger">Contact Information</h2>
                    <p>For inquiries about the EVSU Research Archive, please contact:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt text-danger me-2"></i> Eastern Visayas State University</li>
                        <li><i class="fas fa-location-dot text-danger me-2"></i> Tacloban City, Leyte, Philippines</li>
                        <li><i class="fas fa-envelope text-danger me-2"></i> research@evsu.edu.ph</li>
                        <li><i class="fas fa-phone text-danger me-2"></i> (053) 321-1084</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
