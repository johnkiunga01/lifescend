{{-- Cropper Modal --}}
<div x-show="showModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm" style="display: none;">
    <div class="relative mx-4 w-full max-w-2xl rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900">Crop <span x-text="label"></span></h3>
            <button @@click="cancelCrop" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="bg-slate-900 flex items-center justify-center" style="height: 420px;">
            <img x-ref="cropImage" class="max-w-full max-h-full block">
        </div>
        <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50">
            <p class="text-xs text-slate-500">Drag to move · Scroll to zoom · <span x-text="w+'×'+h+'px'"></span></p>
            <div class="flex items-center gap-3">
                <button @@click="cancelCrop" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer">Cancel</button>
                <button @@click="doCrop" class="rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white hover:bg-gold-600 shadow-lg shadow-gold-500/25 transition-all cursor-pointer">Crop & Apply</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('imageCropper', (config) => ({
        name: config.name,
        ratio: config.ratio,
        w: config.width,
        h: config.height,
        label: config.label,
        showModal: false,
        previewUrl: null,
        cropperInstance: null,

        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => {
                this.showModal = true;
                this.$nextTick(() => {
                    const img = this.$refs.cropImage;
                    img.src = ev.target.result;
                    if (this.cropperInstance) this.cropperInstance.destroy();
                    const aspectRatio = this.ratio || NaN;
                    this.cropperInstance = new Cropper(img, {
                        aspectRatio: aspectRatio,
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true,
                        background: false,
                        modal: true,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxResizable: true,
                        cropBoxMovable: true,
                    });
                });
            };
            reader.readAsDataURL(file);
        },

        doCrop() {
            if (!this.cropperInstance) return;
            const opts = { imageSmoothingQuality: 'high' };
            if (this.w && this.h) { opts.width = this.w; opts.height = this.h; }
            const canvas = this.cropperInstance.getCroppedCanvas(opts);
            canvas.toBlob((blob) => {
                const croppedFile = new File([blob], this.name + '.jpg', { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(croppedFile);
                this.$refs.fileInput.files = dt.files;

                this.previewUrl = URL.createObjectURL(blob);
                this.showModal = false;
                this.cropperInstance.destroy();
                this.cropperInstance = null;
            }, 'image/jpeg', 0.9);
        },

        cancelCrop() {
            this.showModal = false;
            if (this.cropperInstance) {
                this.cropperInstance.destroy();
                this.cropperInstance = null;
            }
            this.$refs.fileInput.value = '';
        },

        clearCover() {
            this.previewUrl = null;
            this.$refs.fileInput.value = '';
        },
    }));
});
</script>
