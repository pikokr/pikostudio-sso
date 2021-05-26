<template>
    <div>
        <jet-section-border/>

        <jet-action-section class="mt-10 sm:mt-0">
            <template #title>
                연동된 계정
            </template>

            <template #content>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fab fa-discord fa-2x" style="color: #5865F2;"></i>
                            <div class="ml-4">디스코드</div>
                        </div>

                        <div class="flex items-center">
                            <a href="/auth/discord" class="ml-2 text-sm text-gray-400">
                                {{$page.props.user.discord_id ? '계정 이전' : '연동하기'}}
                            </a>
                            <button v-if="$page.props.user.discord_id" class="ml-2 text-sm text-gray-400">
                                연동 해제
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </jet-action-section>
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
    }
}
</script>
