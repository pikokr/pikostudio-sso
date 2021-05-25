<template>
    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo/>
        </template>

        <div>
            <div>
                <h1 class="text-center text-2xl font-bold">{{client.name}}</h1>
                <h1 class="text-center text-lg mb-2">이(가) PIKOSTUDIO 계정에 접근하려고 합니다.</h1>
                <hr>
                <div class="mt-2">
                    <div class="font-bold">{{client.name}}의 개발자에게 다음 권한을 허용합니다.</div>
                </div>
                <div class="flex flex-col gap-1 my-2">
                    <div class="flex items-center border rounded-lg" v-for="scope in scopes">
                        <div class="p-2 border-r">
                            <i style="color: #4287f5" class="fas fa-2x fa-check-circle"></i>
                        </div>
                        <div class="text-lg mx-2 font-bold">
                            {{scope.description}}
                        </div>
                    </div>
                </div>
                <hr>
                <div class="my-2 font-bold">
                    <p>개발자: {{team}}</p>
                    <p>인증 후 다음 위치로 이동합니다: {{host}}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <jet-button @click="cancel" class="flex-grow" :class="{ 'opacity-25': authorizeForm.processing || cancelForm.processing }" :disabled="authorizeForm.processing || cancelForm.processing">
                    취소
                </jet-button>
                <jet-button @click="authorize" class="flex-grow" :class="{ 'opacity-25': authorizeForm.processing || cancelForm.processing }" :disabled="authorizeForm.processing || cancelForm.processing">
                    승인
                </jet-button>
            </div>
        </div>
    </jet-authentication-card>
</template>

<script>
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard'
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo'
import JetButton from '@/Jetstream/Button'
import JetSectionBorder from '@/Jetstream/SectionBorder'

export default {
    components: {
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        JetButton,
        JetSectionBorder
    },
    data() {
        return {
            authorizeForm: this.$inertia.form(),
            cancelForm: this.$inertia.form()
        }
    },
    methods: {
        authorize() {
            this.authorizeForm.post(route('passport.authorizations.approve'))
        },
        cancel() {
            this.authorizeForm.delete(route('passport.authorizations.deny'))
        }
    },
    props: [
        'client',
        'scopes',
        'team'
    ],
    computed: {
        host() {
            return new URL(this.client.redirect).host
        }
    }
}
</script>
