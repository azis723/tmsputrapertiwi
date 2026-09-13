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
            <p class="text-xs text-slate-400">Verifikasi Liveness Gerakan, Geofencing Lokasi, & Biometrik Wajah AI</p>
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
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">1. Geofencing Lokasi</p>
                    <p class="text-xs font-bold truncate" :class="insideGeofence ? 'text-emerald-400' : 'text-rose-400'" x-text="geofenceStatusText">
                        Mencari lokasi GPS...
                    </p>
                    <p class="text-[10px] text-slate-500 font-mono" x-show="userDistance !== null">
                        Jarak: <span x-text="userDistance + ' m'"></span> (Maks: {{ $major->radius_meters }}m)
                    </p>
                </div>
            </div>

            <!-- Face & Liveness Status -->
            <div :class="faceMatched ? 'bg-emerald-500/10 border-emerald-500/30' : (livenessPassed ? 'bg-amber-500/10 border-amber-500/30' : 'bg-indigo-500/10 border-indigo-500/30')"
                 class="p-4 rounded-2xl border transition flex items-center gap-3">
                <div :class="faceMatched ? 'text-emerald-400 bg-emerald-500/20' : (livenessPassed ? 'text-amber-400 bg-amber-500/20' : 'text-indigo-400 bg-indigo-500/20')"
                     class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="overflow-hidden">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">2. Biometrik & Liveness</p>
                    <p class="text-xs font-bold truncate" :class="faceMatched ? 'text-emerald-400' : (livenessPassed ? 'text-amber-400' : 'text-indigo-300')" x-text="faceMatchText">
                        Uji Gerakan Liveness...
                    </p>
                    <p class="text-[10px] text-slate-500 truncate" x-text="faceMatchSubtext">
                        Selesaikan 3 gerakan wajah
                    </p>
                </div>
            </div>
        </div>

        <!-- Liveness Motion Challenge Component -->
        <div class="p-5 rounded-2xl bg-slate-950/90 border border-slate-800 space-y-4 shadow-inner">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span x-show="!livenessPassed" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5" :class="livenessPassed ? 'bg-emerald-400' : 'bg-indigo-500'"></span>
                    </span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white">Uji Keaktifan Wajah (Liveness Detection)</h3>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-full"
                          :class="livenessPassed ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30'"
                          x-text="livenessPassed ? '3/3 Lolos' : (getCompletedCount() + '/3 Selesai')">
                    </span>

                    <button type="button" 
                            @click="resetLiveness()" 
                            title="Ulangi Gerakan Dari Awal" 
                            class="px-2 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition text-xs font-medium flex items-center gap-1 border border-slate-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Ulangi</span>
                    </button>
                </div>
            </div>

            <!-- 3 Motion Checklist Steps -->
            <div class="grid grid-cols-3 gap-2.5">
                <!-- Step 1: Kedipkan Mata -->
                <div class="p-3 rounded-2xl border transition-all duration-300 flex flex-col items-center justify-center text-center relative overflow-hidden"
                     :class="stepBlinkPassed ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-300' : (currentMotionStep === 'blink' ? 'bg-indigo-500/20 border-indigo-500 text-white ring-2 ring-indigo-500/30' : 'bg-slate-900/60 border-slate-800 text-slate-500')">
                    <div class="text-2xl mb-1 transition transform" :class="currentMotionStep === 'blink' ? 'scale-110' : ''">
                        👁️
                    </div>
                    <p class="text-xs font-bold leading-tight">1. Kedip</p>
                    <p class="text-[10px] mt-0.5" :class="stepBlinkPassed ? 'text-emerald-400 font-semibold' : 'text-slate-400'"
                       x-text="stepBlinkPassed ? '✓ Berhasil' : (currentMotionStep === 'blink' ? 'Kedipkan mata' : 'Menunggu')"></p>
                    
                    <template x-if="stepBlinkPassed">
                        <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-emerald-500 text-slate-950 rounded-full flex items-center justify-center text-[9px] font-black">✓</span>
                    </template>
                </div>

                <!-- Step 2: Buka Mulut (Mangap) -->
                <div class="p-3 rounded-2xl border transition-all duration-300 flex flex-col items-center justify-center text-center relative overflow-hidden"
                     :class="stepMouthPassed ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-300' : (currentMotionStep === 'mouth' ? 'bg-indigo-500/20 border-indigo-500 text-white ring-2 ring-indigo-500/30' : 'bg-slate-900/60 border-slate-800 text-slate-500')">
                    <div class="text-2xl mb-1 transition transform" :class="currentMotionStep === 'mouth' ? 'scale-110' : ''">
                        🗣️
                    </div>
                    <p class="text-xs font-bold leading-tight">2. Mangap</p>
                    <p class="text-[10px] mt-0.5" :class="stepMouthPassed ? 'text-emerald-400 font-semibold' : 'text-slate-400'"
                       x-text="stepMouthPassed ? '✓ Berhasil' : (currentMotionStep === 'mouth' ? 'Buka mulut' : 'Menunggu')"></p>

                    <template x-if="stepMouthPassed">
                        <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-emerald-500 text-slate-950 rounded-full flex items-center justify-center text-[9px] font-black">✓</span>
                    </template>
                </div>

                <!-- Step 3: Geleng Kepala -->
                <div class="p-3 rounded-2xl border transition-all duration-300 flex flex-col items-center justify-center text-center relative overflow-hidden"
                     :class="stepHeadPassed ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-300' : (currentMotionStep === 'head' ? 'bg-indigo-500/20 border-indigo-500 text-white ring-2 ring-indigo-500/30' : 'bg-slate-900/60 border-slate-800 text-slate-500')">
                    <div class="text-2xl mb-1 transition transform" :class="currentMotionStep === 'head' ? 'scale-110' : ''">
                        🔄
                    </div>
                    <p class="text-xs font-bold leading-tight">3. Geleng</p>
                    <p class="text-[10px] mt-0.5" :class="stepHeadPassed ? 'text-emerald-400 font-semibold' : 'text-slate-400'"
                       x-text="stepHeadPassed ? '✓ Berhasil' : (currentMotionStep === 'head' ? 'Tengok kiri/kanan' : 'Menunggu')"></p>

                    <template x-if="stepHeadPassed">
                        <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-emerald-500 text-slate-950 rounded-full flex items-center justify-center text-[9px] font-black">✓</span>
                    </template>
                </div>
            </div>

            <!-- Active Motion Live Instruction Prompt Banner -->
            <div class="p-3.5 rounded-xl border transition-all flex items-center gap-3"
                 :class="faceMatched ? 'bg-emerald-500/15 border-emerald-500/40 text-emerald-300' : (livenessPassed ? 'bg-indigo-500/20 border-indigo-500/30 text-indigo-200' : 'bg-indigo-600/15 border-indigo-500/30 text-slate-200')">
                <div class="shrink-0">
                    <template x-if="!livenessPassed">
                        <span class="w-6 h-6 rounded-full bg-indigo-500/30 text-indigo-400 border border-indigo-500/40 flex items-center justify-center text-xs font-bold animate-pulse">
                            !
                        </span>
                    </template>
                    <template x-if="livenessPassed && !faceMatched">
                        <span class="w-6 h-6 rounded-full bg-amber-500/30 text-amber-300 border border-amber-500/40 flex items-center justify-center text-xs font-bold">
                            ⏳
                        </span>
                    </template>
                    <template x-if="faceMatched">
                        <span class="w-6 h-6 rounded-full bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 flex items-center justify-center text-xs font-bold">
                            ✓
                        </span>
                    </template>
                </div>
                <div class="text-xs">
                    <p class="font-bold text-white" x-text="activeMotionTitle">Instruksi Gerakan:</p>
                    <p class="text-slate-300 text-[11px] mt-0.5" x-text="activeMotionPrompt">
                        Posisikan wajah Anda tegak di depan kamera.
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
                <p class="text-xs text-slate-400 mt-1">Memuat model biometrik & landmark 68-titik</p>
            </div>

            <!-- Video Feed -->
            <video id="webcam" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
            <!-- Hidden Capture Canvas -->
            <canvas id="capture-canvas" class="hidden"></canvas>
            <!-- Overlay Canvas -->
            <canvas id="overlay" class="absolute inset-0 w-full h-full pointer-events-none transform -scale-x-100"></canvas>

            <!-- Guide Circle / Target Oval -->
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                <div :class="faceMatched ? 'border-emerald-400 shadow-emerald-500/40 shadow-2xl' : (livenessPassed ? 'border-indigo-400 shadow-indigo-500/30' : 'border-slate-500/60')"
                     class="w-48 h-60 rounded-[50%] border-2 border-dashed transition-all duration-300"></div>
            </div>

            <!-- Match / Motion Banner Overlay -->
            <div class="absolute bottom-3 inset-x-3 z-10 text-center">
                <span :class="faceMatched ? 'bg-emerald-600/90 text-white' : (livenessPassed ? 'bg-indigo-600/90 text-white' : 'bg-slate-900/85 text-slate-300')"
                      class="px-3 py-1.5 rounded-full text-xs font-semibold backdrop-blur-md transition shadow-lg inline-flex items-center gap-1.5">
                    <span x-show="faceMatched">✅</span>
                    <span x-text="faceMatched ? 'Identitas Terverifikasi Cocok!' : (livenessPassed ? 'Mencocokkan dengan data siswa...' : 'Tahap Liveness: ' + getActiveStepName())"></span>
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

            // Liveness Motion State
            currentMotionStep: 'blink', // 'blink' | 'mouth' | 'head' | 'verified'
            stepBlinkPassed: false,
            stepMouthPassed: false,
            stepHeadPassed: false,
            livenessPassed: false,
            activeMotionTitle: 'Langkah 1: Kedipkan Mata',
            activeMotionPrompt: 'Kedipkan kedua mata Anda di depan kamera secara natural.',
            eyeOpenObserved: false,

            // Face Matching state
            enrolledDescriptor: {!! $student->face_embedding ?? 'null' !!},
            faceDetected: false,
            faceMatched: false,
            faceMatchText: 'Uji Gerakan Liveness...',
            faceMatchSubtext: 'Selesaikan 3 gerakan wajah',

            videoEl: null,
            canvasEl: null,
            captureCanvasEl: null,
            detectionInterval: null,
            geoWatchId: null,

            getCompletedCount() {
                let count = 0;
                if (this.stepBlinkPassed) count++;
                if (this.stepMouthPassed) count++;
                if (this.stepHeadPassed) count++;
                return count;
            },

            getActiveStepName() {
                if (this.currentMotionStep === 'blink') return 'Kedipkan Mata';
                if (this.currentMotionStep === 'mouth') return 'Buka Mulut (Mangap)';
                if (this.currentMotionStep === 'head') return 'Gelengkan Kepala';
                return 'Verifikasi Identitas Wajah';
            },

            get canSubmit() {
                return this.insideGeofence && this.livenessPassed && this.faceMatched && !this.loadingModel;
            },

            get submitButtonText() {
                if (this.submitting) return 'Mengirim Bukti Presensi...';
                if (!this.insideGeofence) return 'Di Luar Radius Sekolah (Mendekat ke Area Sekolah)';
                if (!this.livenessPassed) return 'Tuntaskan 3 Gerakan Wajah (Liveness) Terlebih Dahulu';
                if (!this.faceMatched) return 'Wajah Tidak Cocok dengan Data Siswa Terdaftar';
                return 'Kirim Presensi Sekarang';
            },

            resetLiveness() {
                this.stepBlinkPassed = false;
                this.stepMouthPassed = false;
                this.stepHeadPassed = false;
                this.livenessPassed = false;
                this.currentMotionStep = 'blink';
                this.eyeOpenObserved = false;
                this.faceMatched = false;
                this.activeMotionTitle = 'Langkah 1: Kedipkan Mata';
                this.activeMotionPrompt = 'Kedipkan kedua mata Anda di depan kamera secara natural.';
                this.faceMatchText = 'Uji Gerakan Liveness...';
                this.faceMatchSubtext = 'Selesaikan 3 gerakan wajah';
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
                    this.loadingText = 'Gagal memuat model neural network lokal.';
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
                    this.loadingText = 'Kamera tidak dapat diakses. Pastikan izin kamera aktif.';
                }
            },

            startFaceRecognitionLoop() {
                const displaySize = { width: this.videoEl.videoWidth || 640, height: this.videoEl.videoHeight || 480 };
                faceapi.matchDimensions(this.canvasEl, displaySize);

                const enrolledArr = this.enrolledDescriptor ? new Float32Array(this.enrolledDescriptor) : null;
                const distCalc = (p1, p2) => Math.hypot(p1.x - p2.x, p1.y - p2.y);

                this.detectionInterval = setInterval(async () => {
                    if (this.videoEl.paused || this.videoEl.ended) return;

                    const detection = await faceapi
                        .detectSingleFace(this.videoEl, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.45 }))
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    const ctx = this.canvasEl.getContext('2d');
                    ctx.clearRect(0, 0, this.canvasEl.width, this.canvasEl.height);

                    if (!detection) {
                        this.faceDetected = false;
                        if (!this.livenessPassed) {
                            this.faceMatchText = 'Mencari Wajah...';
                            this.faceMatchSubtext = 'Posisikan wajah Anda di depan kamera';
                        }
                        return;
                    }

                    this.faceDetected = true;
                    const resized = faceapi.resizeResults(detection, displaySize);
                    const positions = resized.landmarks.positions;

                    // Draw subtle facial landmark mesh
                    faceapi.draw.drawFaceLandmarks(this.canvasEl, resized);

                    // ==========================================
                    // 1. LIVENESS MOTION DETECTION
                    // ==========================================
                    if (!this.livenessPassed) {
                        // A. Calculate Eye Aspect Ratio (EAR) for Blink
                        const rightEAR = (distCalc(positions[37], positions[41]) + distCalc(positions[38], positions[40])) / (2.0 * (distCalc(positions[36], positions[39]) || 1));
                        const leftEAR = (distCalc(positions[43], positions[47]) + distCalc(positions[44], positions[46])) / (2.0 * (distCalc(positions[42], positions[45]) || 1));
                        const ear = (rightEAR + leftEAR) / 2.0;

                        // B. Calculate Mouth Aspect Ratio (MAR) for Mangap
                        const mouthWidth = distCalc(positions[48], positions[54]);
                        const mouthHeight = distCalc(positions[51], positions[57]);
                        const innerMouthHeight = distCalc(positions[62], positions[66]);
                        const mar = (mouthHeight + innerMouthHeight) / (2.0 * (mouthWidth || 1));

                        // C. Calculate Head Turn Yaw Ratio
                        const noseTip = positions[30];
                        const distToLeftJaw = distCalc(noseTip, positions[0]);
                        const distToRightJaw = distCalc(noseTip, positions[16]);
                        const yawRatio = distToLeftJaw / (distToRightJaw || 0.001);

                        // Process Current Motion Challenge Step
                        if (this.currentMotionStep === 'blink') {
                            if (ear >= 0.23) {
                                this.eyeOpenObserved = true;
                            }
                            // Detect blink (eye closed after being open)
                            if (this.eyeOpenObserved && ear < 0.20) {
                                this.stepBlinkPassed = true;
                                this.currentMotionStep = 'mouth';
                                this.activeMotionTitle = 'Langkah 2: Buka Mulut (Mangap)';
                                this.activeMotionPrompt = 'Hebat! Sekarang buka mulut Anda (mangap) secara jelas.';
                            }
                        } else if (this.currentMotionStep === 'mouth') {
                            // Detect open mouth (mangap)
                            if (mar > 0.32 || (innerMouthHeight / (mouthWidth || 1)) > 0.16) {
                                this.stepMouthPassed = true;
                                this.currentMotionStep = 'head';
                                this.activeMotionTitle = 'Langkah 3: Gelengkan Kepala';
                                this.activeMotionPrompt = 'Bagus! Sekarang tengok/gelengkan kepala ke kiri atau kanan perlahan.';
                            }
                        } else if (this.currentMotionStep === 'head') {
                            // Detect head turn (geleng kiri atau kanan)
                            if (yawRatio < 0.58 || yawRatio > 1.75) {
                                this.stepHeadPassed = true;
                                this.livenessPassed = true;
                                this.currentMotionStep = 'verified';
                                this.activeMotionTitle = 'Verifikasi Identitas Wajah';
                                this.activeMotionPrompt = 'Semua gerakan liveness selesai! Tatap lurus ke kamera untuk pencocokan wajah...';
                            }
                        }
                    }

                    // ==========================================
                    // 2. FACE IDENTITY MATCHING
                    // (Hanya dievaluasi setelah Liveness Motion Passed)
                    // ==========================================
                    if (this.livenessPassed && enrolledArr) {
                        const distFace = faceapi.euclideanDistance(detection.descriptor, enrolledArr);
                        const box = resized.detection.box;

                        if (distFace < 0.55) {
                            this.faceMatched = true;
                            const score = Math.round((1 - distFace) * 100);
                            this.faceMatchText = 'Wajah Cocok (Skor ' + score + '%)';
                            this.faceMatchSubtext = 'Liveness valid & wajah cocok dengan akun {{ $student->name }}';
                            this.activeMotionTitle = 'Verifikasi Berhasil!';
                            this.activeMotionPrompt = 'Identitas biometrik terverifikasi cocok dengan {{ $student->name }} (Skor ' + score + '%). Anda dapat mengirim presensi.';

                            // Draw Green Box
                            new faceapi.draw.DrawBox(box, {
                                label: '{{ $student->name }} (' + score + '%)',
                                boxColor: '#10b981'
                            }).draw(this.canvasEl);
                        } else {
                            this.faceMatched = false;
                            this.faceMatchText = 'Wajah Tidak Cocok';
                            this.faceMatchSubtext = 'Wajah tidak sesuai dengan biometrik {{ $student->name }}';
                            this.activeMotionTitle = 'Verifikasi Gagal!';
                            this.activeMotionPrompt = 'Wajah yang terdeteksi bukan milik siswa terdaftar ({{ $student->name }}).';

                            // Draw Red Box
                            new faceapi.draw.DrawBox(box, {
                                label: 'Wajah Tidak Cocok!',
                                boxColor: '#f43f5e'
                            }).draw(this.canvasEl);
                        }
                    } else if (!this.livenessPassed) {
                        // Draw Motion Instruction Tag over face box
                        const box = resized.detection.box;
                        new faceapi.draw.DrawBox(box, {
                            label: this.getActiveStepName(),
                            boxColor: '#6366f1'
                        }).draw(this.canvasEl);
                    }
                }, 160); // Responsive 160ms cycle
            },

            capturePhotoBase64() {
                const w = this.videoEl.videoWidth || 640;
                const h = this.videoEl.videoHeight || 480;
                this.captureCanvasEl.width = w;
                this.captureCanvasEl.height = h;
                const ctx = this.captureCanvasEl.getContext('2d');
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
