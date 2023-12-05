<?php
include 'Header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurTube</title>
    <link rel="stylesheet" href="Header.css">
    <link rel="stylesheet" href="AccountLogin.css">
</head>

<body>
    <div class="CenterDiv">
        <div id="div1">
            <h1>Submit your video here!</h1>
            <!-- <h2>please log into a account</h2> -->
        </div>
        <div id="div2">
            <form action="VideoProcessing.php" method="post" id="videoSubmitForm" enctype="multipart/form-data">
                <input type="text" name="videoName" id="email" required><br>
                <input type="file" name="video" id="email" accept="video/*" required><br>
                <input type="file" name="videoThumbnail" id="email" accept="image/*" required><br>
                <input type="text" name="videoDescription" id="email" required><br>
                <input type="hidden" name="MAX_FILE_SIZE" value="41943040">
            </form>
            <div id="div3">
                <button class="Button" id="submitBtn" form="videoSubmitForm">Upload video</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('videoSubmitForm').addEventListener('submit', function(event) {
            var videoFile = document.querySelector('input[name="video"]').files[0];
            var videoThumbnailFile = document.querySelector('input[name="videoThumbnail"]').files[0];

            // Check the size of the video file
            if (videoFile && videoFile.size > 100000000) { // Adjust the size limit as needed
                alert('Video file size exceeds the limit (100 MB). Please choose a smaller file.');
                event.preventDefault(); // Prevent the form from being submitted
            }

            // Check the size of the video thumbnail file
            if (videoThumbnailFile && videoThumbnailFile.size > 10000000) {
                alert('Video thumbnail file size exceeds the limit (10 MB). Please choose a smaller file.');
                event.preventDefault();
            }
        });
    </script>
    </form>
</body>