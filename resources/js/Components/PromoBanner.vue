<template>
  <section class="relative bg-gradient-to-br from-purple-700 to-fuchsia-600">
    <div class="max-w-6xl mx-auto px-4 py-16 text-center">
      <div v-motion="{ initial:{ opacity:0, y:-20 }, enter:{ opacity:1, y:0 } }">
        <h1 class="text-4xl md:text-5xl font-black drop-shadow mb-3">Welcome to Socia</h1>
        <p class="text-white/90 md:text-lg">Play & Win — and help charities with every spin.</p>
        <div class="mt-6 flex items-center justify-center gap-3">
          <router-link to="/slots"
            class="px-5 py-3 rounded-xl bg-gold text-black font-semibold hover:brightness-110 btn-glossy">
            Play Now
          </router-link>
          <router-link to="/"
            class="px-5 py-3 rounded-xl border border-white/20 hover:bg-white/10">
            Learn More
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>
