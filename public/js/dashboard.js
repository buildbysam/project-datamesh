let currentFileDetails = null;

function updateFileName(input) {
  const fileNameDisplay = document.getElementById("selected-file-name");
  if (input.files.length > 0) {
    fileNameDisplay.textContent = "Selected file: " + input.files[0].name;
  } else {
    fileNameDisplay.textContent = "";
  }
}

function openFileDetailsModal(file) {
  currentFileDetails = file;
  document.getElementById("fileDetailsFilename").textContent =
    file.original_filename;
  document.getElementById("fileDetailsDescription").textContent =
    file.description || "No description provided.";
  document.getElementById("fileDetailsSize").textContent =
    (file.file_size / (1024 * 1024)).toFixed(2) + " MB";
  document.getElementById("fileDetailsOwner").textContent = file.username;
  document.getElementById("fileDetailsModified").textContent = new Date(
    file.uploaded_at
  ).toLocaleDateString("en-GB");

  const visibilitySpan = document.getElementById("fileDetailsVisibility");
  const visibilitySelect = document.getElementById(
    "fileDetailsVisibilitySelect"
  );
  const fileDetailsDeleteBtn = document.getElementById("fileDetailsDeleteBtn");
  const fileDetailsMoveBtn = document.getElementById("fileDetailsMoveBtn");
  const fileDetailsShareBtn = document.getElementById("fileDetailsShareBtn");
  const fileDetailsCopyLinkBtn = document.getElementById(
    "fileDetailsCopyLinkBtn"
  );

  visibilitySpan.textContent =
    file.visibility.charAt(0).toUpperCase() + file.visibility.slice(1);
  visibilitySelect.value = file.visibility;

  // Check if the current logged-in user is the owner of the file
  if (file.user_id == userId) {
    visibilitySpan.classList.add("hidden");
    visibilitySelect.classList.remove("hidden");
    fileDetailsDeleteBtn.onclick = () =>
      openDeleteFileModal(file.id, file.original_filename);
    fileDetailsDeleteBtn.classList.remove("hidden");
    fileDetailsMoveBtn.onclick = () =>
      openMoveFileModal(file.id, file.original_filename);
    fileDetailsMoveBtn.classList.remove("hidden");
  } else {
    visibilitySpan.classList.remove("hidden");
    visibilitySelect.classList.add("hidden");
    fileDetailsDeleteBtn.classList.add("hidden");
    fileDetailsMoveBtn.classList.add("hidden");
  }

  if (file.visibility === "public") {
    fileDetailsShareBtn.classList.remove("hidden");
    fileDetailsCopyLinkBtn.classList.remove("hidden");
    fileDetailsShareBtn.onclick = () =>
      alert("Share functionality not implemented.");
    fileDetailsCopyLinkBtn.onclick = () =>
      copyToClipboard(window.location.origin + "/download.php?id=" + file.id);
  } else {
    fileDetailsShareBtn.classList.add("hidden");
    fileDetailsCopyLinkBtn.classList.add("hidden");
  }

  const fileExtension = file.original_filename.split(".").pop().toLowerCase();
  const filePreviewIcon = document.getElementById("filePreviewIcon");
  filePreviewIcon.innerHTML = ""; // Clear previous content

  let previewContent = "";
  let downloadLink = `download.php?id=${file.id}`;

  if (["jpg", "jpeg", "png", "gif"].includes(fileExtension)) {
    previewContent = `<img src="preview.php?id=${file.id}" alt="${file.original_filename}" class="max-w-full h-auto rounded-md mb-4 max-h-48 object-contain">`;
  } else if (fileExtension === "txt") {
    previewContent = `<iframe src="preview.php?id=${file.id}" class="w-full h-48 border border-gray-300 rounded-md mb-4"></iframe><p class="text-xs text-gray-500 mt-2">Text content preview.</p>`;
  } else if (["pdf", "doc", "docx"].includes(fileExtension)) {
    previewContent = `<div class="bg-gray-100 text-gray-600 p-4 rounded-md text-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm">Direct preview not available for this file type.</p>
                            <p class="text-xs text-gray-500">You can download it to view.</p>
                        </div>`;
  } else {
    previewContent = `<div class="bg-gray-100 text-gray-600 p-4 rounded-md text-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm">No preview available for this file type.</p>
                            <p class="text-xs text-gray-500">Please download to view.</p>
                        </div>`;
  }
  filePreviewIcon.innerHTML = previewContent;
  document.getElementById("fileDetailsDownloadBtn").href = downloadLink;
  document.getElementById("fileDetailsModal").classList.remove("hidden");
}

function updateFileVisibility(newVisibility) {
  if (!currentFileDetails) return;
  const fileId = currentFileDetails.id;
  fetch("update_visibility.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `file_id=${fileId}&visibility=${newVisibility}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        currentFileDetails.visibility = newVisibility;
        document.getElementById("fileDetailsVisibility").textContent =
          newVisibility.charAt(0).toUpperCase() + newVisibility.slice(1);
        document.getElementById("fileDetailsVisibilitySelect").value =
          newVisibility;
        if (newVisibility === "public") {
          document
            .getElementById("fileDetailsShareBtn")
            .classList.remove("hidden");
          document
            .getElementById("fileDetailsCopyLinkBtn")
            .classList.remove("hidden");
        } else {
          document
            .getElementById("fileDetailsShareBtn")
            .classList.add("hidden");
          document
            .getElementById("fileDetailsCopyLinkBtn")
            .classList.add("hidden");
        }
      } else {
        alert("Failed to update visibility.");
      }
    })
    .catch((error) => console.error("Error:", error));
}

function openDeleteFileModal(fileId, filename) {
  if (
    confirm(
      `Are you sure you want to delete "${filename}"? This action cannot be undone.`
    )
  ) {
    window.location.href = `delete_file.php?id=${fileId}`;
  }
}

function openRenameFolderModal(folderId, folderName) {
  document.getElementById("renameFolderId").value = folderId;
  document.getElementById("newFolderName").value = folderName;
  document.getElementById("renameFolderModal").classList.remove("hidden");
}

function openDeleteFolderModal(folderId, folderName) {
  document.getElementById("deleteFolderId").value = folderId;
  document.getElementById("deleteFolderName").textContent = folderName;
  document.getElementById("deleteFolderModal").classList.remove("hidden");
}

function openMoveFileModal(fileId, fileName) {
  document.getElementById("moveFileId").value = fileId;
  document.getElementById("moveFileName").textContent = fileName;
  document.getElementById("moveFileModal").classList.remove("hidden");
}

function toggleFolderActions(folderId) {
  const actionsDiv = document.getElementById(`folder-actions-${folderId}`);
  actionsDiv.classList.toggle("hidden");
}

// Close folder action dropdowns when clicking outside
document.addEventListener("click", function (event) {
  document.querySelectorAll('[id^="folder-actions-"]').forEach(function (div) {
    if (
      !div.classList.contains("hidden") &&
      !div.previousElementSibling.contains(event.target)
    ) {
      div.classList.add("hidden");
    }
  });
});

function copyToClipboard(text) {
  const textarea = document.createElement("textarea");
  textarea.value = text;
  document.body.appendChild(textarea);
  textarea.select();
  try {
    document.execCommand("copy");
    alert("Link copied to clipboard!");
  } catch (err) {
    console.error("Failed to copy: ", err);
    alert("Failed to copy link.");
  }
  document.body.removeChild(textarea);
}
