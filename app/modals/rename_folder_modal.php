<div id="renameFolderModal"
    class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Rename Folder</h3>
            <button onclick="document.getElementById('renameFolderModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="rename_folder.php" method="POST">
            <input type="hidden" name="folder_id" id="renameFolderId">
            <div class="mb-4">
                <label for="new_folder_name" class="block text-gray-700 text-sm font-medium mb-2">New Folder
                    Name</label>
                <input type="text" name="new_folder_name" id="newFolderName"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                    required>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('renameFolderModal').classList.add('hidden')"
                    class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition duration-200">Cancel</button>
                <button type="submit"
                    class="bg-orange-500 text-white py-2 px-4 rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-200">Rename</button>
            </div>
        </form>
    </div>
</div>