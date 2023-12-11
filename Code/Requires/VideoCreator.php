<dialog id="createVideoDialog">
    <button id="closebtn" onclick="closeCreateVideoDialog()">cancel</button>
    <form action="processing.php" method="post" id="videoSubmitForm" enctype="multipart/form-data">
        <h1>Submit your video here!</h1>
        <input type="hidden" name="action" value="upload_video">
        <input type="text" placeholder="Video Title" name="videoName" id="videoName" maxlength="55" required>
        <div class="File-Select-Preview">
            <video id="videoPreview" src="" autoplay muted loop></video>
            <input type="file" name="video" accept="video/*" required onchange="previewFile('video', 'videoPreview')">
        </div>
        <div class="File-Select-Preview">
            <img id="thumbnailPreview" src="" alt="please select a thumbnail">
            <input type="file" name="videoThumbnail" accept="image/*" required onchange="previewFile('videoThumbnail', 'thumbnailPreview')">
        </div>
        <input type="text" placeholder="Video description" name="videoDescription" id="videoDescription" required>
        <input type="hidden" name="MAX_FILE_SIZE" value="41943040">
        <input type="submit" value="Upload video" id="submitBtn">
    </form>
</dialog>
<script>
    document.getElementById('videoSubmitForm').addEventListener('submit', function(event) {
        var videoFile = document.querySelector('input[name="video"]').files[0];
        var videoThumbnailFile = document.querySelector('input[name="videoThumbnail"]').files[0];

        // Check grootte van de video file
        if (videoFile && videoFile.size > 40000000) {
            alert('Video file size exceeds the limit (40 MB). Please choose a smaller file.');
            event.preventDefault(); // Form word niet gesubmit
        }

        // Check grootte van de thumbnail file
        if (videoThumbnailFile && videoThumbnailFile.size > 10000000) {
            alert('Video thumbnail file size exceeds the limit (10 MB). Please choose a smaller file.');
            event.preventDefault();
        }
    });

    function openCreateVideoDialog() {
        var dialog = document.getElementById('createVideoDialog');
        dialog.showModal();
    }

    function closeCreateVideoDialog() {
        var dialog = document.getElementById('createVideoDialog');
        dialog.close();
    }

    function toggleForm(formId) {
        var form = document.getElementById(formId);
        if (form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }

    function previewFile(inputName, previewElementId) {
            var input = document.querySelector('input[name="' + inputName + '"]');
            var previewElement = document.getElementById(previewElementId);
            
            var file = input.files[0];
            var reader = new FileReader();

            reader.onloadend = function () {
                previewElement.src = reader.result;
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                previewElement.src = "";
            }
        }
</script>
