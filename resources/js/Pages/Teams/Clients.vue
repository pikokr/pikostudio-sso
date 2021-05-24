<template>
<app-layout>
    <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            OAuth2 Clients
        </h2>
    </template>
    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8"><client-manager/>
        </div>
    </div>
</app-layout>
</template>

<script>
import AppLayout from "../../Layouts/AppLayout";
import ClientManager from "./ClientManager";
export default {
    components: {AppLayout, ClientManager},
}
</script>

