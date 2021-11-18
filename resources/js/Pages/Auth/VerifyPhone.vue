<template>
    <Head title="Phone Verification" />

    <div class="mb-4 text-sm text-gray-600">
        Thanks for signing up! Before getting started, please enter the code you just received on your phone.
        {{phoneNumber}}
    </div>

    <form @submit.prevent="submit">
        <div>
            <BreezeLabel for="otp" value="otp" />
            <BreezeInput id="otp" type="tel" class="mt-1 block w-full" v-model="form.otp" required autofocus autocomplete="off" />
        </div>
        <div class="flex items-center justify-end mt-4">
            <BreezeButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
              Verify
            </BreezeButton>
        </div>
    </form>

</template>

<script>
import BreezeButton from '@/Components/Button.vue'
import BreezeGuestLayout from '@/Layouts/Guest.vue'
import BreezeInput from '@/Components/Input.vue'
import BreezeLabel from '@/Components/Label.vue'
import { Head, Link } from '@inertiajs/inertia-vue3';

export default {
    layout: BreezeGuestLayout,

    components: {
        BreezeButton,
        Head,
        Link,
        BreezeInput,
        BreezeLabel
    },

    props: {
        status: String,
        phoneNumber: String,
        loginHash: String
    },

    data() {
        return {
            form: this.$inertia.form({
                otp: '',
                loginHash: ''
            })
        }
    },

    methods: {
        submit() {
            this.form.loginHash = this.loginHash;
            this.form.post(this.route('phoneverification.verify'))
        },
    }
}
</script>
