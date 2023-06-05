const imgUpload = document.querySelector("#imgUpload");
const video = document.querySelector("#videoElement");
const btnCapture = document.querySelector("#btn-capture");

let imgUploadProperties;
let imgUploadReady = false;
let faceDetedted = false;

const IDResult = {complete: false, result:null};
let IDIndex = -1;

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
    const ImageStructure = [];

    for(let index = 0; index < imgUpload.files.length; index++){
        const image = await faceapi.bufferToImage(imgUpload.files[index]);
        const detection = await faceapi.detectAllFaces(image);
        
        if(detection.length > 0)
            ImageStructure.push({hasFace: true});
        else
            ImageStructure.push({hasFace: false});
    }
    
    if(ImageStructure.length < 1){
        imgUpload.value = "";
        return;
    }else{
        let counter = 0;
        for(let i = 0; i < ImageStructure.length; i++){
            if(ImageStructure[i].hasFace){
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
       alert("You may now start capturing!");

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
        alert("Please upload img first");
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
            }    
        });
    }
    
    if(IDResult.complete){
        alert("Face Recognized from ID!");
        btnCapture.disabled = true;
        SetIDValue();
    }
}

function SetIDValue(){
    const OCR_VAL = [];
    for(let i = 0; i < imgUpload.files.length; i++){
        const formData = new FormData();
        formData.append('image', imgUpload.files[i]);

        fetch('https://api.api-ninjas.com/v1/imagetotext', {
            method: 'POST',
            headers: { 'x-api-key': 'Bsb6tj0moXhWqmCu01A7FQ==AVEYlPiR3kHQlvtr' },
            body: formData
        })
            .then(response => response.json())
            .then(result => {
                localStorage.setItem("OCR-" + i, JSON.stringify(result));
                //console.log(localStorage.getItem("OCR-" + i));
                OCR_VAL.push(result);
                return i;
            })
            .then((index)=>{
                //console.log("i=" + index);
                //console.log((parseInt(index) === parseInt(imgUpload.files.length - 1)));
                if(parseInt(index) === parseInt(imgUpload.files.length - 1)){
                    document.getElementById("id-ocr-result").value = JSON.stringify(OCR_VAL);
                    //console.log(OCR_VAL)
                }
            })
            .catch(error => {
                alert(error);
            });

    }
}
