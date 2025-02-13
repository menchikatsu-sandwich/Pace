@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-3 flex justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-800 text-white text-center py-4">
                <h2 class="text-2xl font-semibold">{{ __('Login') }}</h2>
            </div>
            <div class="p-6">
                <form id="login-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="flex flex-col items-center">
                        <div class="w-full max-w-sm md:max-w-lg lg:max-w-2xl">
                            <video id="video" class="w-full h-auto aspect-square rounded-full object-cover" autoplay muted></video>
                            <canvas id="canvas" class="hidden"></canvas>
                            <input type="hidden" name="face_data" id="face_data">
                        </div>
                        <button type="button" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-lg" id="capture">
                            Capture Face
                        </button>
                    </div>


                    <div class="flex justify-center mt-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            {{ __('Login') }}
                        </button>
                    </div>
                </form>
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

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const displaySize = { width: video.videoWidth, height: video.videoHeight };
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
            window.location.href = "/"; // Redirect ke home
        } else {
            alert(result.message);
        }
    });

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            document.getElementById('video').srcObject = stream;
            setTimeout(() => {
                console.log("Camera is ready!");
            }, 2000);
        })
        .catch(err => {
            console.error("Error accessing the camera: ", err);
            alert("Please allow camera access in your browser settings.");
        });

    loadModels();
</script>
@endsection
