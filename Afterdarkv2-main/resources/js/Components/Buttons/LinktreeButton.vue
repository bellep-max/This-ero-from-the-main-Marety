<script setup>
import { $t } from '@/i18n';
import { BButton, BCollapse, BFormInput, BInputGroup } from 'bootstrap-vue-next';
import { computed, defineAsyncComponent, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
const Icon = defineAsyncComponent(() => import('@/Components/Icons/Icon.vue'));
const DefaultButton = defineAsyncComponent(() => import('@/Components/Buttons/DefaultButton.vue'));

const props = defineProps({
    ownProfile: {
        type: Boolean,
        required: true,
    },
    linktree_link: {
        type: String,
        default: '',
    },
});

const state = ref(false);

const form = useForm({
    linktree_link: props.linktree_link,
});

const hasLink = computed(() => props.linktree_link?.length > 0);

const isValidHttpUrl = (urlString) => {
    try {
        const newUrl = new URL(urlString);
        return newUrl.protocol === 'http:' || newUrl.protocol === 'https:';
    } catch (err) {
        return false;
    }
};

const updateLinktreeLink = () => {
    form.put(route('profiles.linktree.update'), {
        onSuccess: () => {
            form.reset('linktree_link');
        },
    });
};

const removeLinktreeLink = () => {
    form.delete(route('profiles.linktree.destroy'), {
        onSuccess: () => {
            form.reset('linktree_link');
        },
    });
};

watch(
    () => form.linktree_link,
    (newValue) => {
        state.value = isValidHttpUrl(newValue);
    },
);
</script>

<template>
    <template v-if="ownProfile && hasLink">
        <a
            class="btn-default btn-outline w-100 text-decoration-none"
            :href="linktree_link"
            target="_blank"
            rel="noopener"
        >
            <Icon :icon="['fas', 'up-right-from-square']" />
            {{ $t('buttons.linktree.visit') }}
        </a>
        <DefaultButton class-list="btn-outline w-100" @click="removeLinktreeLink">
            <Icon :icon="['fas', 'link-slash']" />
            {{ $t('buttons.linktree.remove') }}
        </DefaultButton>
    </template>
    <template v-else-if="ownProfile && !hasLink">
        <DefaultButton v-b-toggle.collapse-1 class-list="btn-outline w-100">
            <Icon :icon="['fas', 'link']" />
            {{ $t('buttons.linktree.set') }}
        </DefaultButton>
        <BCollapse id="collapse-1">
            <BInputGroup class="my-2">
                <BFormInput v-model="form.linktree_link" :state="state" />
                <BButton variant="success" :disabled="!state" @click="updateLinktreeLink">
                    {{ $t('buttons.save') }}
                </BButton>
            </BInputGroup>
        </BCollapse>
    </template>
    <a
        :href="linktree_link"
        v-else-if="!ownProfile && hasLink"
        class="btn-default btn-outline w-100 text-decoration-none"
    >
        <Icon :icon="['fas', 'up-right-from-square']" />
        {{ $t('buttons.linktree.visit') }}
    </a>
    <DefaultButton v-else class-list="btn-outline w-100 disabled" disabled>
        <Icon :icon="['fas', 'up-right-from-square']" />
        {{ $t('buttons.linktree.not_set') }}
    </DefaultButton>
    <!--  <DefaultButton-->
    <!--      v-if="(ownProfile && hasLink) || (!ownProfile && hasLink)"-->
    <!--      class-list="btn-outline w-100"-->
    <!--  >-->
    <!--    <Icon :icon="['fas', 'up-right-from-square']"/>-->
    <!--    {{ $t('buttons.linktree.visit') }}-->
    <!--  </DefaultButton>-->
    <!--  <DefaultButton-->
    <!--      v-else-if="ownProfile && !hasLink"-->
    <!--      class-list="btn-outline w-100"-->
    <!--  >-->
    <!--    <Icon :icon="['fas', 'up-right-from-square']"/>-->
    <!--    {{ $t('buttons.linktree.set') }}-->
    <!--  </DefaultButton>-->
</template>

<style scoped></style>
