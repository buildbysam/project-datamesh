<?php

require_once __DIR__ . '/../app/includes/header.php';
require_once __DIR__ . '/../app/core/auth.php';
require_once __DIR__ . '/../app/core/file_operations.php';
;
require_once __DIR__ . '/../app/core/folder_operations.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$current_folder_id = (isset($_GET['folder_id']) && $_GET['folder_id'] !== '') ? (int) $_GET['folder_id'] : null;
$search_query = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';

$files = [];
$folders = [];

if ($search_query) {
    $my_files_search = getFilesByUserId($user_id, null, $search_query);
    $public_files_search = getPublicFiles($user_id, $search_query);

    $combined_files = [];
    $seen_file_ids = [];

    foreach ($my_files_search as $file) {
        if (!isset($seen_file_ids[$file['id']])) {
            $combined_files[] = $file;
            $seen_file_ids[$file['id']] = true;
        }
    }
    foreach ($public_files_search as $file) {
        if (!isset($seen_file_ids[$file['id']])) {
            $combined_files[] = $file;
            $seen_file_ids[$file['id']] = true;
        }
    }
    $files = $combined_files;
    $folders = [];
    $current_folder_name = 'Search Results for "' . $search_query . '"';
} else {
    $files = getFilesByUserId($user_id, $current_folder_id);
    $folders = getFoldersByUserId($user_id, $current_folder_id);

    $current_folder_name = 'My Files';
    $parent_folder_id = null;
    if ($current_folder_id) {
        $folder_info = getFolderById($current_folder_id);
        if ($folder_info && $folder_info['user_id'] == $user_id) {
            $current_folder_name = htmlspecialchars($folder_info['name']);
            $parent_folder_id = $folder_info['parent_id'];
        } else {
            header("Location: dashboard.php?error=invalid_folder");
            exit;
        }
    }
}

?>

<div class="flex flex-1">
    <!-- Sidebar -->
    <aside class="w-64 bg-white p-6 shadow-md flex flex-col justify-between">
        <div>
            <div class="flex items-center space-x-3 mb-8">
                <div class="bg-orange-100 text-orange-500 p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                </div>
                <span class="text-xl font-semibold text-gray-800">DataMesh</span>
            </div>
            <nav>
                <ul>
                    <li class="mb-2">
                        <a href="dashboard.php"
                            class="flex items-center p-3 rounded-lg text-orange-500 bg-orange-50 font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            My Files
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#"
                            class="flex items-center p-3 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Shared With Me
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#"
                            class="flex items-center p-3 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Recent
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#"
                            class="flex items-center p-3 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.324 1.118l1.519 4.674c.3.921-.755 1.688-1.539 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.539-1.118l1.519-4.674a1 1 0 00-.324-1.118L2.285 9.101c-.783-.57-.381-1.81.588-1.81h4.915a1 1 0 00.95-.69l1.519-4.674z" />
                            </svg>
                            Favorites
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#"
                            class="flex items-center p-3 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Trash
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="border-t border-gray-200 pt-4">
            <div class="flex items-center p-3 rounded-lg">
                <div>
                    <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($username); ?></p>
                    <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($username); ?>@example.com</p>
                </div>
                <a href="logout.php" class="ml-auto text-gray-500 hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <div class="relative w-full max-w-md">
                <form action="dashboard.php" method="GET" class="flex items-center">
                    <input type="hidden" name="folder_id" value="<?php echo htmlspecialchars($current_folder_id); ?>">
                    <input type="text" name="search" placeholder="Search files..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                        value="<?php echo htmlspecialchars($search_query); ?>">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>
            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
                class="bg-orange-500 text-white py-2 px-4 rounded-md flex items-center hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Upload
            </button>
        </div>

        <div class="flex items-center mb-4">
            <?php if ($current_folder_id && !$search_query): ?>
                <a href="dashboard.php<?php echo $parent_folder_id ? '?folder_id=' . $parent_folder_id : ''; ?>"
                    class="text-orange-500 hover:underline flex items-center mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>
                <span class="text-gray-500">/</span>
            <?php endif; ?>
            <h1 class="text-2xl font-semibold text-gray-800 ml-2"><?php echo $current_folder_name; ?></h1>
            <span class="text-gray-500 ml-3 text-sm"><?php echo count($files) + count($folders); ?> items</span>
            <?php if (!$search_query): ?>
                <button onclick="document.getElementById('createFolderModal').classList.remove('hidden')"
                    class="ml-4 bg-gray-200 text-gray-700 py-1 px-3 rounded-md text-sm hover:bg-gray-300">
                    New Folder
                </button>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($folders as $folder): ?>
                <div
                    class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col items-center justify-center text-center cursor-pointer hover:shadow-md transition duration-150 relative">
                    <a href="dashboard.php?folder_id=<?php echo $folder['id']; ?>"
                        class="flex flex-col items-center justify-center w-full h-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-orange-400 mb-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        <p class="font-medium text-gray-800 text-sm truncate w-full px-2">
                            <?php echo htmlspecialchars($folder['name']); ?>
                        </p>
                    </a>
                    <div class="absolute top-2 right-2">
                        <button onclick="event.stopPropagation(); toggleFolderActions(<?php echo $folder['id']; ?>)"
                            class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>
                        </button>
                        <div id="folder-actions-<?php echo $folder['id']; ?>"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                            <a href="#"
                                onclick="event.preventDefault(); openRenameFolderModal(<?php echo $folder['id']; ?>, '<?php echo htmlspecialchars($folder['name']); ?>')"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Rename</a>
                            <a href="#"
                                onclick="event.preventDefault(); openDeleteFolderModal(<?php echo $folder['id']; ?>, '<?php echo htmlspecialchars($folder['name']); ?>')"
                                class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php foreach ($files as $file): ?>
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col items-center justify-center text-center cursor-pointer hover:shadow-md transition duration-150 relative"
                    onclick="openFileDetailsModal(<?php echo htmlspecialchars(json_encode($file)); ?>)">
                    <?php
                    $file_extension = pathinfo($file['original_filename'], PATHINFO_EXTENSION);
                    $icon = '';
                    switch (strtolower($file_extension)) {
                        case 'pdf':
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>';
                            break;
                        case 'jpg':
                        case 'jpeg':
                        case 'png':
                        case 'gif':
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>';
                            break;
                        case 'zip':
                        case 'rar':
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-purple-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>';
                            break;
                        case 'txt':
                        case 'doc':
                        case 'docx':
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>';
                            break;
                        default:
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>';
                            break;
                    }
                    echo $icon;
                    ?>
                    <p class="font-medium text-gray-800 text-sm truncate w-full px-2">
                        <?php echo htmlspecialchars($file['original_filename']); ?>
                    </p>
                    <p class="text-gray-500 text-xs mt-1"><?php echo round($file['file_size'] / (1024 * 1024), 2); ?> MB</p>
                    <p class="text-gray-500 text-xs"><?php echo date('d/m/Y', strtotime($file['uploaded_at'])); ?></p>
                </div>
            <?php endforeach; ?>

            <?php if (empty($files) && empty($folders)): ?>
                <div class="col-span-full text-center py-12 text-gray-500">
                    <?php if ($search_query): ?>
                        No results found for "<?php echo htmlspecialchars($search_query); ?>".
                    <?php else: ?>
                        No files or folders found.
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php
// Include modal HTML from separate files
require_once __DIR__ . '/../app/modals/upload_modal.php';
require_once __DIR__ . '/../app/modals/file_details_modal.php';
require_once __DIR__ . '/../app/modals/create_folder_modal.php';
require_once __DIR__ . '/../app/modals/rename_folder_modal.php';
require_once __DIR__ . '/../app/modals/delete_folder_modal.php';
require_once __DIR__ . '/../app/modals/move_file_modal.php';
?>

<script>
    const userId = <?php echo $_SESSION['user_id'] ?? 'null'; ?>;
</script>
<script src="js/dashboard.js"></script>

<?php require_once __DIR__ . '/../app/includes/footer.php'; ?>