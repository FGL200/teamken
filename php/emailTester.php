<?php

session_start();

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Application Form</title>
<body>
<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">
                        <h5>User Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="emailing.php" method="POST">
                        <div class="form-group mb-3">
                            <center><h4>Applicant Information</h4></center> <br>

                            <input type="text" name="sname" class="form-control" placeholder="Surname:" required>
                            <input type="text" name="fname" class="form-control" placeholder="First Name:" required>
                            <input type="text" name="mname" class="form-control" placeholder="Middle Name:" required>
                            <input type="number" name="age" class="form-control" placeholder="Age:" required>
                            <input type="text" name="birthday" class="form-control" placeholder="Birthday:" required>
                            <textarea type="text" name="address" class="form-control" placeholder="Address:" required></textarea><br>

                            <input type="text" name="email" class="form-control" placeholder="Email: name@example.com" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="" class="btn btn-primary">Submit Application</button>
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

include('alert.php');

?>