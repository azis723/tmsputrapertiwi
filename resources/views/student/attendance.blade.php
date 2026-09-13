@extends('student.layouts.student')

@section('title', 'Presensi Mandiri')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="attendanceApp()">
    <div class="flex items-center justify-between">
        <a href="{{ route('student.dashboard') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Dashboard</span>
        </a>

        <div class="text-xs text-slate-400 font-mono">
            Batas Hadir: <span class="text-amber-400 font-bold">{{ $lateCutoff }} WIB</span>
        </div>
    </div>

    <!-- Attendance Card -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        <div class="text-center space-y-1">
            <h2 class="text-xl font-bold text-white">Presensi Mandiri Siswa</h2>
            <p class="text-xs text-slate-400">Verifikasi Geofencing lokasi jurusan & Biometrik Wajah AI secara real-time</p>
        </div>

        <!-- Two Validation Pillars Status -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Geofence Status -->
            <div :class="insideGeofence ? 'bg-emerald-500/10 border-emerald-500/30' : 'bg-rose-500/10 border-rose-500/30'"
                 class="p-4 rounded-2xl border transition flex items-center gap-3">
                <div :class="insideGeofence ? 'text-emerald-400 bg-emerald-500/20' : 'text-rose-400 bg-rose-500/20'"
                     class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="overflow-hidden">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Status Geofencing</p>
                    <p class="text-xs font-bold truncate" :class="insideGeofence ? 'text-emerald-400' : 'text-rose-400'" x-text="geofenceStatusText">
                        Mencari lokasi GPS...
                    </p>
                    <p class="text-[10px] text-slate-500 font-mono" x-show="userDistance !== null">
                        Jarak: <span x-text="userDistance + ' m'"></span> (Maks: {{ $major->radius_meters }}m)
                    </p>
                </div>
            </div>

            <!-- Face Match Status -->
            <div :class="faceMatched ? 'bg-emerald-500/10 border-emerald-500/30' : 'bg-amber-500/10 border-amber-500/30'"
                 class="p-4 rounded-2xl border transition flex items-center gap-3">
                <div :class="faceMatched ? 'text-emerald-400 bg-emerald-500/20' : 'text-amber-400 bg-amber-500/20'"
                     class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="overflow-hidden">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Verifikasi Wajah AI</p>
                    <p class="text-xs font-bold truncate" :class="faceMatched ? 'text-emerald-400' : 'text-amber-400'" x-text="faceMatchText">
                        Memindai Wajah...
                    </p>
                    <p class="text-[10px] text-slate-500" x-text="faceMatchSubtext">
                        Posisikan wajah di kamera
                    </p>
                </div>
            </div>
        </div>

        <!-- Camera Container -->
        <div class="relative w-full max-w-md mx-auto aspect-[4/3] rounded-2xl overflow-hidden bg-slate-950 border-2 border-slate-700 flex items-center justify-center shadow-inner">
            <!-- Loading Overlay -->
            <div x-show="loadingModel" class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-slate-950/90 text-center p-4">
                <svg class="w-8 h-8 text-indigo-500 animate-spin mb-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-semibold text-white" x-text="loadingText">Menyiapkan Kamera & AI...</p>
            </div>

            <!-- Video Feed -->
            <video id="webcam" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
            <!-- Hidden Capture Canvas -->
            <canvas id="capture-canvas" class="hidden"></canvas>
            <!-- Overlay Canvas -->
            <canvas id="overlay" class="absolute inset-0 w-full h-full pointer-events-none transform -scale-x-100"></canvas>

            <!-- Guide Circle -->
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                <div :class="faceMatched ? 'border-emerald-400 shadow-emerald-500/30' : 'border-indigo-400/50'"
                     class="w-48 h-60 rounded-[50%] border-2 border-dashed transition duration-300 shadow-2xl"></div>
            </div>

            <!-- Match Banner -->
            <div class="absolute bottom-3 inset-x-3 z-10 text-center">
                <span :class="faceMatched ? 'bg-emerald-600/90 text-white' : 'bg-slate-900/80 text-slate-300'"
                      class="px-3 py-1.5 rounded-full text-xs font-semibold backdrop-blur-md transition shadow-lg">
                    <span x-text="faceMatched ? 'Wajah Terverifikasi Cocok!' : 'Arahkan wajah ke kamera...'"></span>
                </span>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="button" 
                    @click="submitAttendance()"
                    :disabled="!canSubmit || submitting"
                    class="w-full py-4 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-sm shadow-xl shadow-indigo-600/30 transition duration-200 active:scale-95 flex items-center justify-center gap-2">
                <template x-if="submitting">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <span x-text="submitButtonText"></span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
    function attendanceApp() {
        return {
            loadingModel: true,
            loadingText: 'Memuat Model AI & Kamera...',
            submitting: false,

            // Geofence state
            targetLat: {{ $major->latitude }},
            targetLng: {{ $major->longitude }},
            targetRadius: {{ $major->radius_meters }},
            userLat: null,
            userLng: null,
            userDistance: null,
            insideGeofence: false,
            geofenceStatusText: 'Mengambil Lokasi GPS...',

            // Face state
            enrolledDescriptor: {!! $student->face_embedding ?? 'null' !!},
            faceDetected: false,
            faceMatched: false,
            faceMatchText: 'Memindai wajah...',
            faceMatchSubtext: 'Posisikan wajah di lingkaran',

            videoEl: null,
            canvasEl: null,
            captureCanvasEl: null,
            detectionInterval: null,
            geoWatchId: null,

            get canSubmit() {
                return this.insideGeofence && this.faceMatched && !this.loadingModel;
            },

            get submitButtonText() {
                if (this.submitting) return 'Mengirim Bukti Presensi...';
                if (!this.insideGeofence) return 'Di Luar Radius Sekolah (Mendekat ke Area Sekolah)';
                if (!this.faceMatched) return 'Verifikasi Wajah Terlebih Dahulu';
                return 'Kirim Presensi Sekarang';
            },

            async init() {
                this.videoEl = document.getElementById('webcam');
                this.canvasEl = document.getElementById('overlay');
                this.captureCanvasEl = document.getElementById('capture-canvas');

                this.startGeolocation();
                await this.loadModels();
                await this.startCamera();
            },

            startGeolocation() {
                if (!navigator.geolocation) {
                    this.geofenceStatusText = 'Geolokasi tidak didukung peramban.';
                    return;
                }

                this.geoWatchId = navigator.geolocation.watchPosition(
                    (position) => {
                        this.userLat = position.coords.latitude;
                        this.userLng = position.coords.longitude;
                        this.calculateDistance();
                    },
                    (error) => {
                        console.error('Geolocation error:', error);
                        this.geofenceStatusText = 'Akses GPS ditolak/tidak aktif.';
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 5000 }
                );
            },

            calculateDistance() {
                if (this.userLat === null || this.userLng === null) return;

                // Haversine formula
                const R = 6371000; // meters
                const dLat = (this.targetLat - this.userLat) * Math.PI / 180;
                const dLon = (this.targetLng - this.userLng) * Math.PI / 180;
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                          Math.cos(this.userLat * Math.PI / 180) * Math.cos(this.targetLat * Math.PI / 180) *
                          Math.sin(dLon/2) * Math.sin(dLon/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                const d = R * c;

                this.userDistance = Math.round(d * 10) / 10;

                if (this.userDistance <= this.targetRadius) {
                    this.insideGeofence = true;
                    this.geofenceStatusText = 'Dalam Radius Sekolah (' + this.userDistance + ' m)';
                } else {
                    this.insideGeofence = false;
                    this.geofenceStatusText = 'Di Luar Radius (' + this.userDistance + ' m)';
                }
            },

            async loadModels() {
                try {
                    const modelPath = '/models';
                    await faceapi.nets.tinyFaceDetector.loadFromUri(modelPath);
                    await faceapi.nets.faceLandmark68Net.loadFromUri(modelPath);
                    await faceapi.nets.faceRecognitionNet.loadFromUri(modelPath);
                    this.loadingModel = false;
                } catch (e) {
                    console.error('Model load error:', e);
                }
            },

            async startCamera() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                        audio: false
                    });
                    this.videoEl.srcObject = stream;
                    this.videoEl.onloadedmetadata = () => {
                        this.videoEl.play();
                        this.startFaceRecognitionLoop();
                    };
                } catch (err) {
                    console.error('Camera error:', err);
                }
            },

            startFaceRecognitionLoop() {
                const displaySize = { width: this.videoEl.videoWidth || 640, height: this.videoEl.videoHeight || 480 };
                faceapi.matchDimensions(this.canvasEl, displaySize);

                const enrolledArr = this.enrolledDescriptor ? new Float32Array(this.enrolledDescriptor) : null;

                this.detectionInterval = setInterval(async () => {
                    if (this.videoEl.paused || this.videoEl.ended) return;

                    const detection = await faceapi
                        .detectSingleFace(this.videoEl, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 }))
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    const ctx = this.canvasEl.getContext('2d');
                    ctx.clearRect(0, 0, this.canvasEl.width, this.canvasEl.height);

                    if (detection && enrolledArr) {
                        this.faceDetected = true;
                        const dist = faceapi.euclideanDistance(detection.descriptor, enrolledArr);

                        // If euclidean distance < 0.55, consider matched
                        if (dist < 0.55) {
                            this.faceMatched = true;
                            this.faceMatchText = 'Wajah Cocok (Skor ' + Math.round((1 - dist) * 100) + '%)';
                            this.faceMatchSubtext = 'Identitas biometrik terverifikasi';
                        } else {
                            this.faceMatched = false;
                            this.faceMatchText = 'Wajah Tidak Cocok';
                            this.faceMatchSubtext = 'Pastikan pencahayaan cukup';
                        }

                        const resized = faceapi.resizeResults(detection, displaySize);
                        faceapi.draw.drawDetections(this.canvasEl, resized);
                    } else {
                        this.faceDetected = false;
                        this.faceMatched = false;
                        this.faceMatchText = 'Mencari wajah...';
                        this.faceMatchSubtext = 'Posisikan wajah di kamera';
                    }
                }, 300);
            },

            capturePhotoBase64() {
                const w = this.videoEl.videoWidth || 640;
                const h = this.videoEl.videoHeight || 480;
                this.captureCanvasEl.width = w;
                this.captureCanvasEl.height = h;
                const ctx = this.captureCanvasEl.getContext('2d');
                // Draw unmirrored or mirrored
                ctx.drawImage(this.videoEl, 0, 0, w, h);
                return this.captureCanvasEl.toDataURL('image/jpeg', 0.85);
            },

            async submitAttendance() {
                if (!this.canSubmit) return;

                this.submitting = true;
                const photoBase64 = this.capturePhotoBase64();

                try {
                    const res = await fetch('{{ route('student.attendance.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            latitude: this.userLat,
                            longitude: this.userLng,
                            photo: photoBase64,
                            face_verified: true
                        })
                    });

                    const data = await res.json();

                    if (data.success) {
                        if (this.videoEl.srcObject) {
                            this.videoEl.srcObject.getTracks().forEach(t => t.stop());
                        }
                        if (this.geoWatchId) navigator.geolocation.clearWatch(this.geoWatchId);
                        clearInterval(this.detectionInterval);

                        alert(data.message);
                        window.location.href = data.redirect || '{{ route('student.dashboard') }}';
                    } else {
                        alert(data.message || 'Presensi gagal.');
                        this.submitting = false;
                    }
                } catch (err) {
                    console.error('Submit error:', err);
                    alert('Terjadi kesalahan jaringan saat mengirim presensi.');
                    this.submitting = false;
                }
            }
        };
    }
</script>
@endpush
