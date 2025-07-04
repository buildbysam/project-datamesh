<div id="deleteFolderModal"
    class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Delete Folder: <span id="deleteFolderName"
                    class="font-bold"></span></h3>
            <button onclick="document.getElementById('deleteFolderModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="delete_folder.php" method="POST">
            <input type="hidden" name="folder_id" id="deleteFolderId">
            <p class="text-gray-700 mb-4">What do you want to do with the contents of this folder?</p>
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="action" value="delete_contents" class="form-radio text-orange-500"
                        checked>
                    <span class="ml-2 text-gray-700">Delete contents with folder</span>
                </label>
            </div>
            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="radio" name="action" value="move_to_root" class="form-radio text-orange-500">
                    <span class="ml-2 text-gray-700">Move contents to root folder</span>
                </label>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('deleteFolderModal').classList.add('hidden')"
                    class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition duration-200">Cancel</button>
                <button type="submit"
                    class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200">Delete
                    Folder</button>
            </div>
        </form>
    </div>
</div>