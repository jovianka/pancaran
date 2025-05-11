<template>
    <div id="filterDropdown" class="z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
        <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
        Category
        </h6>
        <ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault">
        <li class="flex items-center">
            <input id="apple" type="checkbox" value=""
            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
            <label for="apple" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
            Apple (56)
            </label>
        </li>
        <li class="flex items-center">
            <input id="fitbit" type="checkbox" value=""
            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
            <label for="fitbit" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
            Fitbit (56)
            </label>
        </li>
        <li class="flex items-center">
            <input id="dell" type="checkbox" value=""
            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
            <label for="dell" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
            Dell (56)
            </label>
        </li>
        <li class="flex items-center">
            <input id="asus" type="checkbox" value="" checked
            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
            <label for="asus" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
            Asus (97)
            </label>
        </li>
        </ul>
    </div>
</template>
