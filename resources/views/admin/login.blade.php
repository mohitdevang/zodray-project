<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full space-y-8 p-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Admin Panel</h2>
                <p class="text-gray-600 mb-8">Sign in to access your dashboard</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="admin@example.com"
                           value="{{ old('email') }}">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Enter your password">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember" type="checkbox"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign in
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    Test Credentials:<br>
                    Email: admin@example.com<br>
                    Password: password
                </p>
            </div>
        </div>

        <div class="text-center text-sm text-gray-600">
            <p>&copy; 2025 Zodray Technologies. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
