<template>
    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo />
        </template>
        {{message}}
        <inertia-link href="/login" v-if="!login">
            <jet-button class="w-full">로그인하기</jet-button>
        </inertia-link>
        <inertia-link href="/user/profile" v-else-if="login">
            <jet-button class="w-full">연동하러 가기</jet-button>
        </inertia-link>
    </jet-authentication-card>
</template>

<script>
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard'
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo'
import JetButton from '@/Jetstream/Button'


export default {
    components: {
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        JetButton
    },
    props: [
        'message',
        'login'
    ]
}
</script>

<style scoped>

</style>
