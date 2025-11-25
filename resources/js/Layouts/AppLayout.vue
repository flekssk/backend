<template>
    <div class="min-h-screen flex flex-col">
        <Header/>
        <main class="flex-1">
            <slot/>
        </main>
        <Footer/>
        <AuthModal/>
        <PaymentModal/>
        <Help telegram-username="socia_casino" />
        <Toast position="bottom-right" group="app"/>
    </div>
</template>

<script lang="ts">
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import AuthModal from '@/Components/Modals/AuthModal.vue'
import PaymentModal from "@/Components/Modals/PaymentModal.vue";
import Toast from 'primevue/toast';
import {defineComponent} from "vue";
import Help from "@/Components/Modals/Help.vue";

export default defineComponent({
    components: {Help, PaymentModal, Header, Footer, AuthModal, Toast},
    mounted(): any {
        window.Echo.listen('slots', 'SlotWinEvent', () => console.log(1111))
    }
});
</script>
