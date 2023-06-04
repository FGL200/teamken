function validateFiles(input) {
  var imgUpload = document.getElementById("imgUpload");
  var files = imgUpload.files;
  var validExtensions = ["jpeg", "jpg", "png"];

  for (var i = 0; i < files.length; i++) {
    var file = files[i];
    var fileName = file.name;
    var fileExtension = fileName.split(".").pop().toLowerCase();

    //Validates the file type submitted.
    if (!validExtensions.includes(fileExtension)) {
      alert("Invalid file type. Only JPEG, JPG, and PNG files are allowed.");
      input.value = "";
      return false;
    }
  }

  //Verifies that exactly two files have been submitted.
  if (input.files.length !== 2) {
    alert("Please choose 2 image files.");
    input.value = "";
  }
  return true;
}
