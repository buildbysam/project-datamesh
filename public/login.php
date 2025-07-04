<?php

require_once __DIR__ . '/../app/includes/header.php';
require_once __DIR__ . '/../app/core/auth.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error_message = 'Username and password are required.';
    } else {
        if (loginUser($username, $password)) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error_message = 'Invalid username or password.';
        }
    }
}

?>

<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md text-center">
        <div class="flex justify-center mb-6">
            <div class="bg-orange-100 text-orange-500 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
            </div>
        </div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Welcome Back</h2>
        <p class="text-gray-600 mb-6">Sign in to your account</p>

        <?php if ($error_message): ?>
                    <div class="bg-red-100 text-red-700 p-3 rounded-md mb-4 text-sm">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-4 text-left">
                <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                    required>
            </div>
            <div class="mb-6 text-left">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                    required>
            </div>
            <button type="submit"
                class="w-full bg-orange-500 text-white py-2 px-4 rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-200">Sign
                In</button>
        </form>
        <p class="mt-4 text-sm text-gray-600">
            <a href="#" class="text-orange-500 hover:underline">Forgot your password?</a>
        </p>
        <p class="mt-2 text-sm text-gray-600">
            Don't have an account? <a href="register.php" class="text-orange-500 hover:underline">Sign Up</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../app/includes/footer.php'; ?>