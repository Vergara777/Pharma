<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div>

        <button class="btn" v-if="$page.props.user.permissions.includes('Create roles')">Crear Roles</button>

        <button class="btn" v-if="$page.props.user.permissions.includes('Read products')">Leer Products</button>

    </div>
</template>
<style>
button.btn {
    background-color: #03d14b;
    /* Green */
    border: none;
    color: rgb(0, 0, 0);
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}
</style>
