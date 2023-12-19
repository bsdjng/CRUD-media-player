<?php
$thumbnail = "data:image/png;base64," . base64_encode($video['thumbnail_image']);
?>
<dialog id="editVideoDialog">
    <form id="UpdateVideoform" action="processing.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="ChangeVideoInfo">
        <input type="hidden" name="editVideoId" value="<?php echo $video['id'] ?>">

        <li class="accountFormLi">
            <p class="liText">change video title:</p>
            <input type="text" name="newVideoTitle" class="FormText" placeholder="<?php echo $video['video_name']; ?>">
        </li>

        <li class="accountFormLi">
            <p class="liText">Change thumbnail:</p>
            <div id="profilePicturePreview" class="image-preview">
                <img id="previewProfilePicture" src="<?php echo $thumbnail; ?>" alt="Preview">
            </div>
            <input type="file" name="newThumbnail" accept="image/*" onchange="previewThumbnailImage(this, 'previewProfilePicture')">
        </li>

        <li class="accountFormLi">
            <p class="liText">change description:</p>
            <input type="text" name="newDescription" class="FormText" placeholder="<?php echo !empty($video['video_description']) ? $video['video_description'] : 'write about yourself'; ?>" name="newDescription">
        </li>
    </form>
    <form method="post" action="processing.php" id="deleteVideoForm">
        <input type="hidden" name="action" value="deleteVideo">
        <input type="hidden" name="deleteVideoId" value="<?php echo $video['id'] ?>">
    </form>
    <li class="accountFormLi" id="SubmitBtns">
        <input class="btns" type="submit" form="UpdateVideoform" value="Edit video">
        <input class="btns" type="submit" form="deleteVideoForm" value="Delete video">
    </li>
</dialog>
<script>
    function openEditVideoDialog() {
        var dialog = document.getElementById('editVideoDialog');
        dialog.showModal();
    }

    function previewThumbnailImage(input, previewId) {
        var preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block'; // Show the preview
                preview.alt = "<?php echo $profilePicture; ?>"; // Set alt attribute
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.style.display = 'none'; // Hide the preview
            preview.alt = ''; // Clear alt attribute
        }
    }
</script>