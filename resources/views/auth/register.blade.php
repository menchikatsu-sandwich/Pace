@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="face_data" class="col-md-4 col-form-label text-md-right">{{ __('Face Data') }}</label>

                            <div class="col-md-6">
                                <video id="video" width="720" height="560" autoplay muted></video>
                                <canvas id="canvas" style="display: none;"></canvas>
                                <input type="hidden" name="face_data" id="face_data">
                                <button type="button" class="btn btn-primary mt-2" id="capture">Capture Face</button>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
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
            await faceapi.nets.ssdMobilenetv1.loadFromUri('/models'); // Ganti ke SSD Mobilenet
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

        const detections = await faceapi.detectAllFaces(video, new faceapi.SsdMobilenetv1Options())
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

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            document.getElementById('video').srcObject = stream;
            setTimeout(() => {
                console.log("Camera is ready!");
            }, 2000); // Tunggu 2 detik sebelum capture
        })
        .catch(err => {
            console.error("Error accessing the camera: ", err);
            alert("Please allow camera access in your browser settings.");
        });

    loadModels();
</script>
@endsection