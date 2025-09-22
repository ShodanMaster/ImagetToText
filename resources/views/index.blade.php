@extends('layout.app')
@section('content')

<!-- Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addModalLabel">Add Image</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="imageForm">
                    <div class="mb-3">
                        <label for="imageInput" class="form-label">Choose or Capture Image</label>
                        <input type="file" class="form-control" id="imageInput" accept="image/*" capture="environment">
                    </div>
                    <div id="webcam-container" class="mb-3" style="display: none;">
                        <label class="form-label">Webcam Preview</label>
                        <video id="webcam" autoplay playsinline style="width: 100%; max-height: 300px;"></video>
                        <button type="button" class="btn btn-sm btn-success mt-2" id="takeSnapshotBtn">Take Snapshot</button>
                    </div>

                    <div id="preview-container" class="mb-3" style="display: none;">
                        <label class="form-label">Image Preview</label>
                        <div>
                            <img id="previewImage" style="max-width: 100%; height: auto;" />
                        </div>
                        <small class="text-muted">Crop the image before submitting.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" id="captureImageBtn">Capture Image</button>
                <button type="button" class="btn btn-primary" id="uploadImageBtn">Upload Image</button>
            </div>
        </div>
    </div>
</div>

    <div class="d-flex justify-content-between">
        <h2>Image to Text</h2>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            Add
        </button>
    </div>
@endsection
@push('scripts')

<script>
  let cropper;
  let stream;
  const imageInput = document.getElementById('imageInput');
  const previewImage = document.getElementById('previewImage');
  const previewContainer = document.getElementById('preview-container');
  const webcamContainer = document.getElementById('webcam-container');
  const webcam = document.getElementById('webcam');
  const takeSnapshotBtn = document.getElementById('takeSnapshotBtn');
  const captureImageBtn = document.getElementById('captureImageBtn');
  const uploadImageBtn = document.getElementById('uploadImageBtn');

  // 📷 Open webcam when "Capture Image" is clicked
  captureImageBtn.addEventListener('click', async function () {
    previewContainer.style.display = 'none';
    webcamContainer.style.display = 'block';

    try {
      stream = await navigator.mediaDevices.getUserMedia({ video: true });
      webcam.srcObject = stream;
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Camera Error',
        text: 'Unable to access the camera.',
      });
      console.error(error);
    }
  });

  // 📸 Take snapshot from webcam
  takeSnapshotBtn.addEventListener('click', function () {
    const canvas = document.createElement('canvas');
    canvas.width = webcam.videoWidth;
    canvas.height = webcam.videoHeight;
    canvas.getContext('2d').drawImage(webcam, 0, 0);

    const dataUrl = canvas.toDataURL('image/jpeg');

    previewImage.src = dataUrl;
    previewContainer.style.display = 'block';
    webcamContainer.style.display = 'none';

    if (cropper) cropper.destroy();

    cropper = new Cropper(previewImage, {
      aspectRatio: 1,
      viewMode: 1,
      autoCropArea: 1,
    });

    // Stop webcam
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
    }
  });

  // 📁 Handle file input change
  imageInput.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function (event) {
        previewImage.src = event.target.result;
        previewContainer.style.display = 'block';
        webcamContainer.style.display = 'none';

        if (cropper) cropper.destroy();

        cropper = new Cropper(previewImage, {
          aspectRatio: 1,
          viewMode: 1,
          autoCropArea: 1,
        });
      };
      reader.readAsDataURL(file);
    }
  });

  // ⬆️ Upload cropped image
  uploadImageBtn.addEventListener('click', function () {
    if (!cropper) {
      Swal.fire({
        icon: 'warning',
        title: 'No Image',
        text: 'Please select or capture and crop an image first.',
      });
      return;
    }

    cropper.getCroppedCanvas().toBlob(function (blob) {
      const formData = new FormData();
      formData.append('image', blob, 'cropped.jpg');

      axios.post('{{ route('image-to-text.store') }}', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      .then(response => {
        console.log('Upload successful', response.data);
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: 'Image uploaded successfully!',
        });
        document.getElementById('imageForm').reset();
        previewContainer.style.display = 'none';
        if (cropper) cropper.destroy();
        cropper = null;
        const modal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
        modal.hide();
      })
      .catch(error => {
        console.error('Upload failed', error);
        Swal.fire({
          icon: 'error',
          title: 'Upload Failed',
          text: 'Something went wrong while uploading the image.',
        });
      });
    });
  });

  // 🔄 Reset modal content when it's closed
  document.getElementById('addModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('imageForm').reset();
    previewContainer.style.display = 'none';
    webcamContainer.style.display = 'none';

    if (cropper) {
      cropper.destroy();
      cropper = null;
    }

    if (stream) {
      stream.getTracks().forEach(track => track.stop());
      stream = null;
    }

    previewImage.src = '';
  });
</script>


@endpush

