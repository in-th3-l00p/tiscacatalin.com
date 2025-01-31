<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
                    <x-application-logo class="block h-12 w-auto" />

                    <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
                        Welcome to Your Website Control Panel
                    </h1>

                    <p class="mt-6 text-gray-500 dark:text-gray-400 leading-relaxed">
                        Manage your website content, monitor performance, and access key features from this dashboard.
                    </p>
                </div>

                <!-- Quick Stats Section -->
                <div class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-6 lg:p-8">
                    <!-- Total Posts -->
                    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
                                Total Posts
                            </h2>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">
                            1,024
                        </p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            +12% from last month
                        </p>
                    </div>

                    <!-- Active Users -->
                    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
                                Active Users
                            </h2>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">
                            5,678
                        </p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            +8% from last month
                        </p>
                    </div>

                    <!-- Website Traffic -->
                    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
                                Website Traffic
                            </h2>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">
                            12.3K
                        </p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            +15% from last month
                        </p>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
                                Recent Activity
                            </h2>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">
                            32
                        </p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            New updates
                        </p>
                    </div>
                </div>

                <!-- Recent Content Section -->
                <div class="p-6 lg:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                        Recent Content
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Example Content Card -->
                        <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Blog Post Title
                            </h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Published 2 days ago
                            </p>
                            <p class="mt-4 text-gray-700 dark:text-gray-300">
                                This is a brief excerpt from the blog post...
                            </p>
                            <a href="#" class="mt-4 inline-flex items-center text-blue-500 dark:text-blue-300">
                                Read More
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        <!-- Repeat for other content items -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
