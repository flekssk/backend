<template>
  <div class="min-h-screen flex flex-col">
    <Header/>
    <main class="flex-1">
        <slot />
    </main>
    <Footer/>
    <AuthModal/>
    <PaymentModal/>
  </div>
</template>

<script>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import AuthModal from '@/Components/Modals/AuthModal.vue'
import PaymentModal from "@/Components/Modals/PaymentModal.vue";

export default {components: {PaymentModal, Header, Footer, AuthModal}};
</script>
