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

            <!-- Insert new Terms and Service card below the About EVSU Research Archive card -->
            <div class="card mt-4">
                <div class="card-header" id="termsHeader" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#termsCollapse" aria-expanded="false" aria-controls="termsCollapse">
                    <h5 class="mb-0">Terms and Service</h5>
                </div>
                <div class="collapse" id="termsCollapse" aria-labelledby="termsHeader">
                    <div class="card-body">
                        <p><strong>Terms and Service</strong> – By using the EVSU Research Archive, you agree to comply with the following terms. You may only upload research publications (including manuscripts, papers, and supplementary materials) that you have authored or for which you have obtained the necessary permissions. You are responsible for ensuring that your uploads do not infringe copyright or other intellectual property rights. The EVSU Research Archive reserves the right to review, moderate, and (if necessary) remove submissions that violate these terms or are deemed inappropriate. In addition, you agree to provide accurate metadata (such as title, authors, abstract, keywords, college, department, program, and publication date) for your uploads. Any personal information (for example, your profile picture and research interests) that you provide shall be used solely for the purposes of the archive and shall not be shared with third parties without your consent. By uploading, you grant EVSU Research Archive a non-exclusive, royalty-free license to host, archive, and publicly display your research (subject to any embargo or access restrictions you specify). EVSU Research Archive is provided "as is" and we do not guarantee that the service will be uninterrupted or error-free. We reserve the right to update these terms at any time; continued use of the archive after such changes constitutes your acceptance of the updated terms.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
