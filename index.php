<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Custom CSS files-->
  <link rel="stylesheet" href="./css/landingPage.css" />
  <link rel="stylesheet" href="./css/responsive.css" />
  <!-- Font library -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lexend" />
  <!-- Website Favicon -->
  <link rel="Icon" href="./images/Converge-Favicon.png" />

  <title>Welcome | Converge</title>
</head>

<body>
  <!-- Navigation Bar -->
  <nav class="navbar-top">
    <!-- HREF attribute currently empty. -->
    <a href="#">
      <img src="images/converge_ICT_logo_SVG.svg" alt="Converge Logo" width="200" height="66" />
    </a>
  </nav>
  <div class="main">
    <p class="header">Fiber Connected in 4 Hours</p>
    <p class="sub-text">Streamlining the customer application process</p>

    <div class="container">
      <form action="faceRecognition.html">
        <label for="terms-checkbox">
          <input type="checkbox" id="terms-checkbox" name="terms-checkbox" required />
          I have read and agree to the
          <a href="#" id="show-modal-link">Terms and Conditions.</a>
        </label>
        <button class="button" id="proceed-button" disabled>Proceed</button>
      </form>
    </div>
    <div id="modal-overlay" class="modal-overlay">
      <div class="modal-box">
        <span class="close-button">&times;</span>
        <ol>
          <br />
          <h3>Terms and Conditions (End-User License Agreement)</h3>
          <p>
            Please read these terms and conditions carefully before using our
            web-based application.
          </p>
          <br />
          <li>
            <p><strong>Acceptance of Terms</strong></p>
            <p>
              By accessing or using our web-based application, you agree to be
              bound by these terms and conditions. If you do not agree with
              any part of these terms, please do not proceed to use the
              application.
            </p>
          </li>
          <br />
          <li>
            <p><strong>Use of the Application</strong></p>
            <ol>
              <li>
                <strong>Grant of License:</strong>
                <p>
                  We grant you a non-exclusive, non-transferable license to
                  use the application for personal or business purposes,
                  solely as intended by the functionality of the application.
                </p>
              </li>
              <br />
              <li>
                <strong>User Responsibilities:</strong>
                <p>
                  You are responsible for ensuring the accuracy and legality
                  of any information or data you provide through the
                  application. You agree not to use the application for any
                  unlawful or unauthorized purposes.
                </p>
              </li>
            </ol>
          </li>
          <br />
          <li>
            <p><strong>Privacy and Data Security</strong></p>
            <ol>
              <li>
                <strong>Data Collection and Usage:</strong>
                <p>
                  The application may collect personal information and user
                  data as necessary for its functionality. We will handle your
                  data in accordance with our privacy policy, which is
                  incorporated into these terms and conditions.
                </p>
              </li>
              <br />
              <li>
                <strong>Data Protection:</strong>
                <p>
                  We implement reasonable security measures to protect your
                  data. However, we cannot guarantee the absolute security of
                  data transmitted through the internet.
                </p>
              </li>
            </ol>
          </li>
          <br />
          <li>
            <p><strong>Intellectual Property</strong></p>
            <ol>
              <li>
                <strong>Ownership:</strong>
                <p>
                  The application and all associated intellectual property
                  rights belong to us. You acknowledge and agree not to
                  reproduce, modify, or distribute any part of the application
                  without our prior written consent.
                </p>
              </li>
              <br />
              <li>
                <strong>Feedback:</strong>
                <p>
                  If you provide any feedback or suggestions regarding the
                  application, you grant us a non-exclusive, perpetual,
                  irrevocable, royalty-free license to use, modify, and
                  incorporate your feedback for any purpose.
                </p>
              </li>
            </ol>
          </li>
          <br />
          <li>
            <p><strong>Limitation of Liability</strong></p>
            <p>
              To the extent permitted by applicable law, we shall not be
              liable for any indirect, incidental, consequential, or punitive
              damages arising out of or in connection with the use or
              inability to use the application, even if we have been advised
              of the possibility of such damages.
            </p>
          </li>
          <br />
          <li>
            <p><strong>Modifications and Termination</strong></p>
            <p>
              We reserve the right to modify, suspend, or terminate the
              application at any time without prior notice. We may also update
              these terms and conditions from time to time, and your continued
              use of the application after such modifications shall signify
              your acceptance of the updated terms.
            </p>
          </li>
          <br />
          <li>
            <p><strong>Governing Law</strong></p>
            <p>
              These terms and conditions shall be governed by and construed in
              accordance with the laws of [Jurisdiction], without regard to
              its conflict of laws principles.
            </p>
          </li>
          <br />
          <li>
            <p><strong>Contact Information</strong></p>
            <p>
              If you have any questions or concerns regarding these terms and
              conditions, please contact us at [Contact Email].
            </p>
          </li>
        </ol>
      </div>
    </div>

    <script>
      // button JS
      var checkbox = document.getElementById("terms-checkbox");
      var proceedButton = document.getElementById("proceed-button");

      checkbox.addEventListener("change", function() {
        proceedButton.disabled = !checkbox.checked;
      });

      // terms and condition modal JS
      var showModalLink = document.getElementById("show-modal-link");
      var modalOverlay = document.getElementById("modal-overlay");
      var closeButton = document.getElementsByClassName("close-button")[0];
      var termsCheckbox = document.getElementById("terms-checkbox");
      var proceedButton = document.getElementById("proceed-button");

      showModalLink.addEventListener("click", function(event) {
        event.preventDefault();
        modalOverlay.style.display = "block";
      });

      closeButton.addEventListener("click", function() {
        modalOverlay.style.display = "none";
      });

      modalOverlay.addEventListener("click", function(event) {
        if (event.target === modalOverlay) {
          modalOverlay.style.display = "none";
        }
      });

      termsCheckbox.addEventListener("change", function() {
        proceedButton.disabled = !this.checked;
      });
    </script>
  </div>
</body>

</html>

<?php

include('valid.php');

?>