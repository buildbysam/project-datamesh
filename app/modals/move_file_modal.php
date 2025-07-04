<div id="moveFileModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Move File: <span id="moveFileName" class="font-bold"></span>
            </h3>
            <button onclick="document.getElementById('moveFileModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="move_file.php" method="POST">
            <input type="hidden" name="file_id" id="moveFileId">
            <div class="mb-4">
                <label for="target_folder" class="block text-gray-700 text-sm font-medium mb-2">Select Destination
                    Folder</label>
                <select name="target_folder" id="targetFolder"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">Root Folder</option>
                    <?php
                    // Fetch all folders for dropdown
                    $all_folders = getFoldersByUserId($user_id);
                    function displayFoldersAsOptions($folders, $level = 0, $exclude_folder_id = null)
                    {
                        foreach ($folders as $folder) {
                            if ($folder['id'] == $exclude_folder_id)
                                continue;
                            echo '<option value="' . $folder['id'] . '">' . str_repeat('&nbsp;&nbsp;&nbsp;', $level) . htmlspecialchars($folder['name']) . '</option>';
                            // Recursive call for subfolders
                            $subfolders = getFoldersByUserId($_SESSION['user_id'], $folder['id']);
                            displayFoldersAsOptions($subfolders, $level + 1, $exclude_folder_id);
                        }
                    }
                    displayFoldersAsOptions($all_folders);
                    ?>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('moveFileModal').classList.add('hidden')"
                    class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition duration-200">
                    Cancel
                </button>
                <button type="submit"
                    class="bg-orange-500 text-white py-2 px-4 rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-200">
                    Move File
                </button>
            </div>
        </form>
    </div>
</div>