@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form id="login-form" method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('Face Recognition') }}</label>
                            <div class="col-md-6">
                                <video id="video" width="720" height="560" autoplay muted></video>
                                <canvas id="canvas" style="display: none;"></canvas>
                                <button type="button" id="capture" class="btn btn-primary mt-2">Capture Face</button>
                                <input type="hidden" name="face_data" id="face_data">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js/dist/face-api.min.js"></script>
<script>
    async function loadModels() {
        try {
            await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
            console.log('Models loaded successfully');
        } catch (error) {
            console.error('Error loading models:', error);
            alert('Failed to load face detection models. Please check the console for details.');
        }
    }

    async function captureFace() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const faceDataInput = document.getElementById('face_data');

        const displaySize = { width: video.width, height: video.height };
        faceapi.matchDimensions(canvas, displaySize);

        const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptors();

        if (detections.length > 0) {
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            faceapi.draw.drawDetections(canvas, resizedDetections);
            faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);

            const faceDescriptor = detections[0].descriptor;
            faceDataInput.value = JSON.stringify(faceDescriptor);
            alert('Face detected successfully!');
        } else {
            alert('No face detected. Please ensure your face is clearly visible and try again.');
        }
    }

    document.getElementById('capture').addEventListener('click', captureFace);

    document.getElementById('login-form').addEventListener('submit', async function(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        });
        const result = await response.json();
        if (result.success) {
            window.location.href = result.redirect;
        } else {
            alert(result.message);
        }
    });

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            document.getElementById('video').srcObject = stream;
            setTimeout(() => {
                console.log("Camera is ready!");
            }, 2000); // Wait 2 seconds before capture
        })
        .catch(err => {
            console.error("Error accessing the camera: ", err);
            alert("Please allow camera access in your browser settings.");
        });

    loadModels();
</script>
@endsection