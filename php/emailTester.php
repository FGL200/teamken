<?php

session_start();
include('alert.php');

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">

<body>
<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">
                        <h5>CDSSA Internship</h5>
                    </div>
                    <div class="card-body">
                        <form action="../backend/code.php" method="POST">
                        <div class="form-group mb-3">
                            <center><h4>Internship Application</h4></center> <br>
                            <h5>Applicant Information</h5>

                            <input type="text" name="name" class="form-control" placeholder="Name: (Last Name, First Name, M.I.)    " required>
                            <input type="text" name="email" class="form-control" placeholder="Email:" required>
                            <input type="number" name="age" class="form-control" placeholder="Age:" required>
                            <textarea type="text" name="address" class="form-control" placeholder="Home Address:" required></textarea>
                            <input type="text" name="institution" class="form-control" placeholder="Institution:" required>
                            <input type="text" name="college" class="form-control" placeholder="College:" required>
                            <input type="text" name="department" class="form-control" placeholder="Department:" required>
                            <input type="text" name="degree" class="form-control" placeholder="Degree Program:" required><br>
                            <label>Attach your Cover Letter and Curriculum Vitae</label><br>
                            <input type="file">
                        </div>
                        <div class="form-group">
                            <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn btn-primary">Back</a>
                            <button type="submit" name="intern_btn" class="btn btn-primary">Submit Application</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

<?php

include('../includes/scripts.php');

?>