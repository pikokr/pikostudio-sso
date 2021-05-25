<template>
    <div>
        <jet-form-section @submitted="addClient" v-if="permissions.canAddClient">
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

        <div v-if="clients.length > 0">
            <jet-section-border/>

            <!-- Manage Team Members -->
            <jet-action-section class="mt-10 sm:mt-0">
                <template #title>
                    애플리케이션 목록
                </template>

                <!-- Team Member List -->
                <template #content>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between" v-for="client in clients" :key="client.id">
                            <div class="flex items-center">
                                <img class="w-8 h-8 rounded-full"
                                     :src="encodeURI('https://ui-avatars.com/api/?color=7F9CF5&background=EBF4FF&name=' + client.name)"
                                     :alt="client.name">
                                <div class="ml-4">{{ client.name }}</div>
                            </div>

                            <div class="flex items-center">
                                <!-- Manage Team Member Role -->
                                <button class="ml-2 text-sm text-gray-400"
                                        @click="(manageClient)(client)">
                                    {{ permissions.canEditClient ? '관리' : '보기' }}
                                </button>

                                <!-- Remove Team Member -->
                                <button class="cursor-pointer ml-6 text-sm text-red-500"
                                        @click="(deleteClient)(client)"
                                        v-if="permissions.canDeleteClient">
                                    제거
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </jet-action-section>
        </div>

        <jet-dialog-modal :show="!!client" @close="client = null">
            <template #title>
                애플리케이션 관리
            </template>

            <template #content>
                <div class="col-span-6 sm:col-span-4">
                    <jet-label for="edit_name" value="애플리케이션 이름"/>
                    <jet-input :readonly="!permissions.canEditClient" v-model="manageClientForm.name" required id="edit_name" type="text" class="mt-1 block w-full"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <jet-label for="edit_redirect" value="리다이렉트 URL"/>
                    <jet-input :readonly="!permissions.canEditClient" v-model="manageClientForm.redirect" required id="edit_redirect" type="url"
                               class="mt-1 block w-full"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <jet-label for="client_id" value="클라이언트 ID"/>
                    <jet-input readonly v-model="client.id" required id="client_id" type="text"
                               class="mt-1 block w-full"/>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <jet-label for="secret" value="클라이언트 시크릿"/>
                    <jet-input readonly v-model="client.secret" required id="secret" type="text"
                               class="mt-1 block w-full"/>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="client = null">
                    닫기
                </jet-secondary-button>

                <jet-button class="ml-2" @click="updateClient" :class="{ 'opacity-25': manageClientForm.processing }"
                            :disabled="manageClientForm.processing" v-if="permissions.canEditClient">
                    저장
                </jet-button>
            </template>
        </jet-dialog-modal>

        <jet-confirmation-modal :show="!!confirmClientDelete" @close="confirmClientDelete = null">
            <template #title>
                삭제하기
            </template>

            <template #content>
                애플리케이션을 삭제할까요?
            </template>

            <template #footer>
                <jet-secondary-button @click="confirmClientDelete = null">
                    취소
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click="processClientDelete" :class="{ 'opacity-25': clientDeleteForm.processing }" :disabled="clientDeleteForm.processing">
                    삭제하기
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
    </div>
</template>
<script>
import JetFormSection from '@/Jetstream/FormSection'
import JetLabel from '@/Jetstream/Label'
import JetInput from '@/Jetstream/Input'
import JetButton from '@/Jetstream/Button'
import JetCheckbox from '@/Jetstream/Checkbox'
import JetActionSection from '@/Jetstream/ActionSection'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import JetDialogModal from '@/Jetstream/DialogModal'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
import JetDangerButton from '@/Jetstream/DangerButton'

export default {
    components: {
        JetFormSection,
        JetLabel,
        JetInput,
        JetButton,
        JetCheckbox,
        JetActionSection,
        JetSectionBorder,
        JetDialogModal,
        JetSecondaryButton,
        JetDangerButton,
        JetConfirmationModal
    },
    data() {
        return {
            addClientForm: this.$inertia.form({
                name: '',
                redirect: '',
                confidential: true
            }),
            manageClientForm: this.$inertia.form({
                name: '',
                redirect: '',
                confidential: true
            }),
            clientDeleteForm: this.$inertia.form({}),
            client: null,
            confirmClientDelete: null
        }
    },
    methods: {
        addClient() {
            this.addClientForm.post('/oauth/clients', {
                onSuccess: () =>
                    this.addClientForm.reset()

            })
        },
        manageClient(client) {
            this.manageClientForm.name = client.name
            this.manageClientForm.redirect = client.redirect
            this.manageClientForm.confidential = client.confidential
            this.client = client
        },
        updateClient() {
            this.manageClientForm.put('/oauth/clients/' + this.client.id, {
                onSuccess: () => {
                    this.client = null
                    this.manageClientForm.reset()
                }
            })
        },
        deleteClient(client) {
            this.confirmClientDelete = client
        },
        processClientDelete() {
            this.clientDeleteForm.delete('/oauth/clients/' + this.confirmClientDelete.id, {
                onSuccess: () => {
                    this.confirmClientDelete = null
                }
            })
        }
    },
    props: [
        'clients',
        'permissions'
    ],
}
</script>
