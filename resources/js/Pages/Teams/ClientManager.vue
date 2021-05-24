<template>
    <div>
        <jet-form-section @submitted="addClient">
            <template #title>
                애플리케이션 추가하기
            </template>
            <template #form>
                <div class="col-span-6 sm:col-span-4">
                    <jet-label for="name" value="애플리케이션 이름"/>
                    <jet-input v-model="addClientForm.name" required id="name" type="text" class="mt-1 block w-full"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <jet-label for="redirect" value="리다이렉트 URL"/>
                    <jet-input v-model="addClientForm.redirect" required id="redirect" type="url"
                               class="mt-1 block w-full"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <jet-checkbox id="confidential" v-model="addClientForm.confidential"/>
                    <label for="confidential" class="ml-2">시크릿을 이용한 인증만 허용</label>
                </div>
            </template>
            <template #actions>
                <jet-button :class="{ 'opacity-25': addClientForm.processing }" :disabled="addClientForm.processing">
                    추가하기
                </jet-button>
            </template>
        </jet-form-section>
    </div>
</template>
<script>
import JetFormSection from '@/Jetstream/FormSection'
import JetLabel from '@/Jetstream/Label'
import JetInput from '@/Jetstream/Input'
import JetButton from '@/Jetstream/Button'
import JetCheckbox from '@/Jetstream/Checkbox'

export default {
    components: {
        JetFormSection,
        JetLabel,
        JetInput,
        JetButton,
        JetCheckbox
    },
    data() {
        return {
            addClientForm: this.$inertia.form({
                name: '',
                redirect: '',
                confidential: true
            }),
        }
    },
    methods: {
        addClient() {
            this.addClientForm.post('/oauth/clients', {
                onSuccess: () =>
                    this.addClientForm.reset()

            })
        }
    }
}
</script>
