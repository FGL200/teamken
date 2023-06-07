const imgUpload = document.querySelector("#imgUpload");
const video = document.querySelector("#videoElement");
const btnCapture = document.querySelector("#btn-capture");

let imgUploadProperties = null;
let imgUploadReady = false;
let faceDetedted = false;
let OCR_VALUE = null;
const imgStructure = [];

const IDResult = {complete: false, result:null};
let IDIndex = -1;

let FORM = {
    fname : null,
    mname : null,
    lname : null,
    age : null,
    bdate : null,
    sex : null,
    address : null,
    zip : null
};

Promise.all([
    faceapi.nets.faceRecognitionNet.loadFromUri('./models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('./models'), 
    faceapi.nets.ssdMobilenetv1.loadFromUri('./models'),
    faceapi.nets.tinyFaceDetector.loadFromUri('./models')
]).then(()=>{
    localStorage.clear();

    const element = document.createElement("p");
    element.setAttribute("id", "id-validation-result")
    element.style.textAlign = "center";
    document.querySelector(".step1").appendChild(element);
    
    const hiddenInput = document.createElement("input");
    hiddenInput.setAttribute("id", "id-ocr-result")
    hiddenInput.setAttribute("name", "ocr-result")
    hiddenInput.setAttribute("type", "hidden")
    hiddenInput.setAttribute("value", "{}")
    document.querySelector(".main").appendChild(hiddenInput);
}).catch(err=>{alert("Error: " + err);});

function loadImgAsPromise(img){
    const labels = ["faceFound"];
    return Promise.all(
        labels.map(async (label) => {
            const detection = await faceapi.detectSingleFace(img)
                .withFaceLandmarks()
                .withFaceDescriptor();
            return new faceapi.LabeledFaceDescriptors(label, [detection.descriptor]);
        })
    );
}

imgUpload.addEventListener("change", async (e)=>{
    for(let index = 0; index < imgUpload.files.length; index++){
        const file = imgUpload.files[index];
    
        if (file.size > 1024 * 1024) { // Check if file size is more than 1MB (1MB = 1024 * 1024 bytes)
          alert('File size exceeds 1MB.');
          imgUpload.value = ''; // Clear the input value if the file size is too large
          return;
        }
    }

    for(let index = 0; index < imgUpload.files.length; index++){
        const image = await faceapi.bufferToImage(imgUpload.files[index]);
        const detection = await faceapi.detectAllFaces(image);
        
        if(detection.length > 0)
            imgStructure.push({hasFace: true});
        else
            imgStructure.push({hasFace: false});
    }
    
    if(imgStructure.length < 1){
        imgUpload.value = "";
        return;
    }else{
        let counter = 0;
        for(let i = 0; i < imgStructure.length; i++){
            if(imgStructure[i].hasFace){
                IDIndex = i;
                counter ++;
            }
        }
        if(counter != 1 || IDIndex == -1){
            alert("Invalid ID, please upload another ID (front and back)")
            imgUpload.value = "";
            return;
        }
    }

    const image = await faceapi.bufferToImage(imgUpload.files[IDIndex]);
    const detection = await faceapi.detectAllFaces(image);

    const element = document.querySelector("#id-validation-result");

    if(detection.length > 0){
       element.innerHTML = "Reading your ID...";

       imgUploadProperties = await loadImgAsPromise(image);
       //alert("You may now start capturing!");

       element.style.color = "#29b198";
       element.style.fontWeight = "bold"
       element.innerHTML = "ID uploaded!";

       imgUpload.style.display = "none"
       btnLabel.style.display = "none"
       document.querySelector("#reUpload").style.display = "block"
       imgUploadReady = true;
       startVideo();
    }else{
       element.style.color = "red";
       element.innerHTML = "Invalid ID";
       alert("Invalid ID, please reupload another ID");
       imgUploadReady = false;
    }
});

function stopVideo(){
    const mediaStream = video.srcObject;
    if (mediaStream) {
        const videoTrack = mediaStream.getVideoTracks()[0];
        videoTrack.stop();
        video.srcObject = null;
    }
}

function startVideo(){
    navigator.mediaDevices
        .getUserMedia({
            video : true,
            audio : false
        }).then(stream =>{
            video.srcObject = stream;
        }).catch(err=>{
            alert(err);
        });
}

btnCapture.addEventListener("click", async (e)=>{
    if(imgUploadReady){
        const detection = await faceapi
            .detectSingleFace(video)
            .withFaceLandmarks()
            .withFaceDescriptor();
        
        if(detection){
            if(detection.length < 1){
                alert("Try again");
            }else{
                faceDetedted = true;
                faceDetectionHandler();
            }
        }else{
            alert("No face detected.");
        }
    
    }else{
        alert("Please upload an ID first");
    }
});

async function faceDetectionHandler(){

    if(!faceDetedted) return;
    
    var faceMatcher = new faceapi.FaceMatcher(imgUploadProperties);
    const displaySizeOfVideo = { width: 320, height: 240 };

    if(!IDResult.complete){
        const detections = await faceapi
            .detectAllFaces(video)
            .withFaceLandmarks()
            .withFaceDescriptors();
        const resizedDetections = faceapi.resizeResults(detections, displaySizeOfVideo);
        
        const results = resizedDetections.map((d) => {
            return faceMatcher.findBestMatch(d.descriptor);
        });

        results.forEach((result) => {
            if(String(result.label) === String("faceFound")){
                IDResult.complete = true;
                IDResult.result = result;
                faceDetedted = false;
            }else{
                alert("Face did not match from ID, Please recapture or upload another ID");
                faceDetedted = false;

                // Bypass ID Validation
                IDResult.complete = true;
                IDResult.result = result;
            }    
        });
    }
    
    if(IDResult.complete){
        alert("Face Recognized from ID!");
        btnCapture.disabled = true;
        btnCapture.innerHTML = "Validating...";
        SetIDValue();
    }
}

function SetIDValue(){
    const OCR = [];
    //for(let i = 0; i < imgUpload.files.length; i++){

        // get the image file
        const fileToUpload = imgUpload.files[IDIndex] 
        
        // API Key
        const API_KEY = "K85037790488957";

        var formData = new FormData();
        formData.append("file", fileToUpload);
        //formData.append("url", "URL-of-Image-or-PDF-file");
        formData.append("language", "eng");
        formData.append("apikey", API_KEY);
        formData.append("isOverlayRequired", true);

        fetch('https://api.ocr.space/parse/image', {
            method: 'POST',
            body: formData
        })
        .then((response) => {
            if (response.ok) {
                return response.json();
            } else {
                alert('OCR request failed. Page will auto reload');
                window.location.reload();
            }
        })
        .then((ocrParsedResult) => {
            const result = {
                parsedResults : ocrParsedResult["ParsedResults"],
                ocrExitCode : ocrParsedResult["OCRExitCode"],
                isErroredOnProcessing : ocrParsedResult["IsErroredOnProcessing"],
                errorMessage : ocrParsedResult["ErrorMessage"]
            }

            OCR.push(result.parsedResults[0].TextOverlay.Lines)
            return true;
        })
        .then(index => {
            //if(parseInt(index) === parseInt(imgUpload.files.length - 1)){
            if(index){
                stopVideo();
            
                document.querySelector(".main").style.display = "none";
                document.querySelector(".container").style.display = "flex";
                
                while(!OCR_VALUE){
                    OCR_VALUE = OCR;
                }
                   
                AutoFillField();
            }
        })
        .catch((error) => {
            console.error(error);
        });

    //}
}

function AutoFillField(){
    // console.log(imgStructure);
    // console.log("idIndex=" + IDIndex);
    // console.log(OCR_VALUE);

    if(GetKindOfID() === String("University_ID")){
        FORM.fname = String(
            OCR_VALUE[0][4].Words[1].WordText
            );
        FORM.lname = String(
            OCR_VALUE[0][4].Words[0].WordText
            );
        FORM.mname = String(
            OCR_VALUE[0][4].Words[2].WordText
            );
        FORM.age = null;
        FORM.bdate = null;
        FORM.sex = null;
        FORM.address = null;
        FORM.zip = null;

    } else if (GetKindOfID() === String("PRC_ID")){
        FORM.fname = String(
            OCR_VALUE[0][5].LineText
            );
        FORM.lname = String(
            OCR_VALUE[0][4].LineText
            );
        FORM.mname = String(
            OCR_VALUE[0][6].LineText
            );
        FORM.age = null;
        FORM.bdate = null;
        FORM.sex = null;
        FORM.address = null;
        FORM.zip = null;

    } else if(GetKindOfID() === String("PASSPORT_ID")){

        const rawDate = new Date(OCR_VALUE[0][11].LineText);
        const BDate = {
            y : rawDate.getFullYear(),
            m : ('0' + (rawDate.getMonth() + 1)).slice(-2),
            d : ('0' + rawDate.getDate()).slice(-2)
        }
        

        FORM.fname = String(
            OCR_VALUE[0][7].LineText
            );
        FORM.lname = String(
            OCR_VALUE[0][6].LineText
            );
        FORM.mname = String(
            OCR_VALUE[0][9].LineText
            );
        FORM.age = null;
        FORM.bdate = `${BDate.y}-${BDate.m}-${BDate.d}`;
        FORM.sex = null;
        FORM.address = null;
        FORM.zip = null;
    }
    else{
        alert("ID provided not yet supported. Auto fill did not run!")
    }

    PutToForm();
}

function PutToForm(){

    console.log(FORM);

    if(FORM.fname) document.getElementById("first-name").value = FORM.fname;
    if(FORM.mname) document.getElementById("middle-name").value = FORM.mname;
    if(FORM.lname) document.getElementById("last-name").value = FORM.lname;
    if(FORM.age) document.getElementById("age").value = FORM.age;
    if(FORM.bdate) document.getElementById("date-of-birth").value = FORM.bdate;
    if(FORM.sex) document.getElementById("sex").value = FORM.sex;
    if(FORM.address) document.getElementById("address").value = FORM.address;
    if(FORM.zip) document.getElementById("zip-code").value = FORM.zip;
}

function GetKindOfID(){

    for(let i = 0; i < OCR_VALUE.length; i++){
        for(let j = 0; j < OCR_VALUE[i].length; j++){
            if(String(OCR_VALUE[i][j].LineText).includes("UNIVERSITY")){
                return String("University_ID");
            }
        }
    }

    for(let i = 0; i < OCR_VALUE.length; i++){
        for(let j = 0; j < OCR_VALUE[i].length; j++){
            if(String(OCR_VALUE[i][j].LineText).includes("PASSPORT")){
                return String("PASSPORT_ID");
            }
            if(String(OCR_VALUE[i][j].LineText).includes("PASAPORTE/")){
                return String("PASSPORT_ID");
            }
        }
    }

    for(let i = 0; i < OCR_VALUE.length; i++){
        for(let j = 0; j < OCR_VALUE[i].length; j++){
            if(String(OCR_VALUE[i][j].LineText).includes("PROFESSIONAL REGULATION COMMISSION")){
                return String("PRC_ID");
            }
        }
    }

    return String("Other_ID");
}