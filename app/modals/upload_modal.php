<div id="uploadModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Upload Files</h3>
            <button onclick="document.getElementById('uploadModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="folder_id" value="<?php echo htmlspecialchars($current_folder_id); ?>">
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center mb-6">
                <input type="file" name="file_upload" id="file_upload" class="hidden" onchange="updateFileName(this)">
                <label for="file_upload" class="cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400 mb-3" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="text-gray-600 font-medium">Drag and drop files here</p>
                    <p class="text-gray-500 text-sm">or click to browse from your computer</p>
                    <button type="button" onclick="document.getElementById('file_upload').click()"
                        class="mt-4 bg-orange-500 text-white py-2 px-4 rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-200">
                        Browse Files
                    </button>
                </label>
                <p id="selected-file-name" class="mt-3 text-gray-700 text-sm"></p>
            </div>
            <div class="mb-4">
                <label for="custom_filename" class="block text-gray-700 text-sm font-medium mb-2">Custom Filename
                    (optional)</label>
                <input type="text" name="custom_filename" id="custom_filename"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                    placeholder="Leave empty to use original filename">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-medium mb-2">Description
                    (optional)</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                    placeholder="Add a description for your files..."></textarea>
            </div>
            <div class="mb-6">
                <label for="visibility" class="block text-gray-700 text-sm font-medium mb-2">Visibility</label>
                <select name="visibility" id="visibility"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="private">Private - Only you can access</option>
                    <option value="public">Public - Anyone can view and download</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')"
                    class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition duration-200">Cancel</button>
                <button type="submit"
                    class="bg-orange-500 text-white py-2 px-4 rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-200">Upload
                    Files</button>
            </div>
        </form>
    </div>
</div>