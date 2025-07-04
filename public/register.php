<?php

require_once __DIR__ . '/../app/includes/header.php';
require_once __DIR__ . '/../app/core/auth.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $message = 'All fields are required.';
        $message_type = 'error';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match.';
        $message_type = 'error';
    } else {
        if (registerUser($username, $password)) {
            $message = 'Registration successful! You can now log in.';
            $message_type = 'success';
        } else {
            $message = 'Registration failed. Username might already exist.';
            $message_type = 'error';
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
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Create Account</h2>
        <p class="text-gray-600 mb-6">Sign up to get started</p>

        <?php if ($message): ?>
                    <div
                        class="<?php echo $message_type === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?> p-3 rounded-md mb-4 text-sm">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="mb-4 text-left">
                <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                    required>
            </div>
            <div class="mb-4 text-left">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                    required>
            </div>
            <div class="mb-6 text-left">
                <label for="confirm_password" class="block text-gray-700 text-sm font-medium mb-2">Confirm
                    Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                    required>
            </div>
            <button type="submit"
                class="w-full bg-orange-500 text-white py-2 px-4 rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-200">Sign
                Up</button>
        </form>
        <p class="mt-4 text-sm text-gray-600">
            Already have an account? <a href="login.php" class="text-orange-500 hover:underline">Sign In</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../app/includes/footer.php'; ?>