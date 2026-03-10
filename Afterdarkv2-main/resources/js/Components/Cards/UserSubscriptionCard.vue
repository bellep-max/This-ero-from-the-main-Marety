<script setup>
import { $t } from '@/i18n.js';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="col-12 p-1">
        <div class="playlist-subscriber-card d-flex flex-row justify-content-start align-items-center gap-4 p-3">
            <a :href="route('users.show', user)">
                <img :src="user.artwork" class="playlist-subscriber-card-avatar" :alt="user.title" />
            </a>
            <div class="d-flex flex-column justify-content-start gap-2 w-100">
                <a
                    :href="route('users.show', user)"
                    class="title font-default default-text-color text-start text-decoration-none"
                >
                    {{ user.username }}
                </a>
                <div class="d-flex flex-row justify-content-between align-items-start w-100">
                    <div class="d-flex flex-column justify-content-start align-items-start w-100">
                        <span class="font-merge color-grey">
                            {{ $t('pages.user.my_orders.status', { status: user.status }) }}
                        </span>
                        <span class="font-merge color-grey">
                            {{ $t('pages.user.my_orders.amount', { amount: user.amount }) }}
                        </span>
                    </div>
                    <div class="d-flex flex-column justify-content-between align-items-start w-100">
                        <span class="font-merge color-grey">
                            {{
                                $t('pages.user.my_orders.last_payment_date', {
                                    date: user.last_payment_date,
                                })
                            }}
                        </span>
                        <span class="font-merge color-grey">
                            {{
                                $t('pages.user.my_orders.next_payment_date', {
                                    date: user.next_payment_date,
                                })
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
