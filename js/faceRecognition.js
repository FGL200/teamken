const imgUpload = document.querySelector("#imgUpload");
const video = document.querySelector("#videoElement");
const btnCapture = document.querySelector("#btn-capture");

let imgUploadProperties;
let imgUploadReady = false;
let faceDetedted = false;

let finalResult = {complete: false, result:null};

Promise.all([
    faceapi.nets.faceRecognitionNet.loadFromUri('./models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('./models'), 
    faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
    faceapi.nets.tinyFaceDetector.loadFromUri('/models')
]).then(startVideo);

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
    const image = await faceapi.bufferToImage(imgUpload.files[0]);
    const detection = await faceapi.detectAllFaces(image);

    if(detection.length > 0){
        imgUploadProperties = await loadImgAsPromise(image);
        alert("Face detected in file, start capturing!");
        imgUpload.style.display = "none"
        document.querySelector("#reUpload").style.display = "block"
        imgUploadReady = true;
    }else{
        alert("No Face detected in ID, reupload another ID");
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

    if(!finalResult.complete){
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
                finalResult.complete = true;
                finalResult.result = result;
            }else{
                alert("Face did not match from ID, Please recapture or upload another ID");
                faceDetedted = false;
            }    
        });
    }
    
    if(finalResult.complete){
        alert("Face Recognized from ID!");
        console.log(finalResult);
    }
}
