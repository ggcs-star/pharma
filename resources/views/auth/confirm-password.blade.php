<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Password</title>

    <!-- Tailwind CDN (optional but recommended for styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex items-center justify-center">
    
    <div class="w-full max-w-md bg-white p-6 rounded-lg shadow">
        
        <!-- Message -->
        <div class="mb-4 text-sm text-gray-600">
            This is a secure area of the application. Please confirm your password before continuing.
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password
                </label>

                <input id="password"
                       type="password"
                       name="password"
                       class="block mt-1 w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring focus:ring-blue-200"
                       required
                       autocomplete="current-password">

                @if ($errors->has('password'))
                    <p class="text-red-500 text-sm mt-2">
                        {{ $errors->first('password') }}
                    </p>
                @endif
            </div>

            <!-- Button -->
            <div class="flex justify-end mt-4">
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Confirm
                </button>
            </div>

        </form>

    </div>

</div>

</body>
</html>