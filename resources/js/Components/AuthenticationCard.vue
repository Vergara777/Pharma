<template>
    <div class="min-h-screen h-screen flex flex-col justify-center items-center bg-gray-100">
        <div class="flex flex-col items-center w-full sm:max-w-md">
            <div class="mb-6">
                <slot name="logo" />
            </div>
            <div class="w-full px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg flex flex-col justify-center">
                <slot />
            </div>
        </div>
    </div>
</template>
