@extends('student.layouts.student')

@section('title', 'Pendaftaran Biometrik Wajah')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="faceRegistrationApp()">
    <div class="flex items-center justify-between">
        <a href="{{ route('student.dashboard') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Dashboard</span>
        </a>

        @if ($student->hasEnrolledFace())
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Sudah Terdaftar (Dapat Daftar Ulang)
            </span>
        @endif
    </div>

    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        <div class="text-center space-y-1">
            <h2 class="text-xl font-bold text-white">Pendaftaran Biometrik Wajah</h2>
            <p class="text-xs text-slate-400">Wajah Anda akan dipindai menggunakan teknologi AI untuk verifikasi saat presensi mandiri.</p>
        </div>

        <!-- Camera Container -->
        <div class="relative w-full max-w-md mx-auto aspect-[4/3] rounded-2xl overflow-hidden bg-slate-950 border-2 border-slate-700 flex items-center justify-center shadow-inner">
            <!-- Loading Overlay -->
            <div x-show="loadingModel" class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-slate-950/90 text-center p-4">
                <svg class="w-8 h-8 text-indigo-500 animate-spin mb-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-semibold text-white" x-text="loadingText">Memuat Model AI...</p>
                <p class="text-xs text-slate-400 mt-1">Mengunduh neural network biometrik</p>
            </div>

            <!-- Error Camera Overlay -->
            <div x-show="cameraError" x-cloak class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-slate-950/95 text-center p-6 space-y-2">
                <svg class="w-10 h-10 text-rose-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="text-sm font-bold text-white">Gagal Mengakses Kamera</p>
                <p class="text-xs text-slate-400" x-text="cameraErrorMessage">Pastikan izin kamera aktif pada peramban Anda.</p>
                <button type="button" @click="startCamera()" class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold">Coba Lagi</button>
            </div>

            <!-- Video Feed -->
            <video id="webcam" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
            <!-- Canvas Overlay for face mesh -->
            <canvas id="overlay" class="absolute inset-0 w-full h-full pointer-events-none transform -scale-x-100"></canvas>

            <!-- Target Face Oval Guide -->
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                <div :class="faceDetected ? 'border-emerald-400 shadow-emerald-500/20' : 'border-indigo-400/60'"
                     class="w-48 h-60 rounded-[50%] border-2 border-dashed transition duration-300 shadow-2xl"></div>
            </div>

            <!-- Live Status Tag -->
            <div class="absolute top-3 left-3 z-10">
                <span :class="faceDetected ? 'bg-emerald-500/80 text-white' : 'bg-slate-900/80 text-slate-400'"
                      class="px-2.5 py-1 rounded-full text-[11px] font-semibold backdrop-blur-md transition flex items-center gap-1.5">
                    <span :class="faceDetected ? 'bg-white' : 'bg-slate-500'" class="w-1.5 h-1.5 rounded-full"></span>
                    <span x-text="faceDetected ? 'Wajah Terdeteksi' : 'Mencari Wajah...'"></span>
                </span>
            </div>
        </div>

        <!-- Instructions Tips -->
        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 text-xs text-slate-300 space-y-1.5">
            <p class="font-semibold text-white">Petunjuk Pendaftaran Wajah:</p>
            <ul class="list-disc list-inside space-y-1 text-slate-400">
                <li>Posisikan wajah Anda tegak lurus di dalam bingkai oval.</li>
                <li>Pastikan ruangan memiliki pencahayaan yang cukup dan tidak membelakangi lampu terang (backlight).</li>
                <li>Lepaskan masker, kacamata hitam, atau penutup wajah lainnya.</li>
            </ul>
        </div>

        <!-- Action Button -->
        <div>
            <button type="button" 
                    @click="captureAndSave()"
                    :disabled="!faceDetected || saving || loadingModel"
                    class="w-full py-4 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 disabled:pointer-events-none text-white font-bold text-sm shadow-xl shadow-indigo-600/30 transition duration-200 active:scale-95 flex items-center justify-center gap-2">
                <template x-if="saving">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <span x-text="saving ? 'Menyimpan Biometrik Wajah...' : 'Rekam & Simpan Biometrik Wajah'"></span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
    function faceRegistrationApp() {
        return {
            loadingModel: true,
            loadingText: 'Memuat Model AI Face Detection...',
            cameraError: false,
            cameraErrorMessage: '',
            faceDetected: false,
            saving: false,
            videoEl: null,
            canvasEl: null,
            latestDescriptor: null,
            detectionInterval: null,

            async init() {
                this.videoEl = document.getElementById('webcam');
                this.canvasEl = document.getElementById('overlay');
                await this.loadModels();
                await this.startCamera();
            },

            async loadModels() {
                try {
                    this.loadingText = 'Memuat model bobot biometrik...';
                    const modelPath = '/models';
                    await faceapi.nets.tinyFaceDetector.loadFromUri(modelPath);
                    await faceapi.nets.faceLandmark68Net.loadFromUri(modelPath);
                    await faceapi.nets.faceRecognitionNet.loadFromUri(modelPath);
                    this.loadingModel = false;
                } catch (e) {
                    console.error('Error loading face-api models:', e);
                    this.loadingText = 'Gagal memuat model. Periksa koneksi lokal.';
                }
            },

            async startCamera() {
                this.cameraError = false;
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                        audio: false
                    });
                    this.videoEl.srcObject = stream;

                    this.videoEl.onloadedmetadata = () => {
                        this.videoEl.play();
                        this.startDetectionLoop();
                    };
                } catch (err) {
                    console.error('Camera access error:', err);
                    this.cameraError = true;
                    this.cameraErrorMessage = err.message || 'Kamera tidak dapat diakses atau izin ditolak.';
                }
            },

            startDetectionLoop() {
                const displaySize = { width: this.videoEl.videoWidth || 640, height: this.videoEl.videoHeight || 480 };
                faceapi.matchDimensions(this.canvasEl, displaySize);

                this.detectionInterval = setInterval(async () => {
                    if (this.videoEl.paused || this.videoEl.ended) return;

                    const detections = await faceapi
                        .detectSingleFace(this.videoEl, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 }))
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (detections) {
                        this.faceDetected = true;
                        this.latestDescriptor = Array.from(detections.descriptor);
                        const resizedDetections = faceapi.resizeResults(detections, displaySize);
                        const ctx = this.canvasEl.getContext('2d');
                        ctx.clearRect(0, 0, this.canvasEl.width, this.canvasEl.height);
                        faceapi.draw.drawDetections(this.canvasEl, resizedDetections);
                    } else {
                        this.faceDetected = false;
                        this.latestDescriptor = null;
                        const ctx = this.canvasEl.getContext('2d');
                        ctx.clearRect(0, 0, this.canvasEl.width, this.canvasEl.height);
                    }
                }, 300);
            },

            async captureAndSave() {
                if (!this.latestDescriptor || this.latestDescriptor.length < 68) {
                    alert('Wajah belum terdeteksi sempurna. Posisikan wajah Anda menghadap kamera.');
                    return;
                }

                this.saving = true;

                try {
                    const response = await fetch('{{ route('student.face-registration.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            face_embedding: JSON.stringify(this.latestDescriptor)
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Stop camera track
                        if (this.videoEl.srcObject) {
                            this.videoEl.srcObject.getTracks().forEach(t => t.stop());
                        }
                        clearInterval(this.detectionInterval);
                        alert('Pendaftaran biometrik wajah Anda berhasil disimpan!');
                        window.location.href = data.redirect || '{{ route('student.dashboard') }}';
                    } else {
                        alert(data.message || 'Gagal menyimpan biometrik wajah.');
                        this.saving = false;
                    }
                } catch (err) {
                    console.error('Save error:', err);
                    alert('Terjadi kesalahan saat mengirim data ke server.');
                    this.saving = false;
                }
            }
        };
    }
</script>
@endpush
