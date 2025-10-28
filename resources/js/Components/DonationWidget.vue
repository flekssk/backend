<template>
  <div class="bg-gray-900 rounded-2xl p-5 border border-white/10 text-center">
    <h2 class="text-2xl font-bold mb-2">Our Mission</h2>
    <p class="text-gray-300 mb-4">We donate a share of profits to charity. Thank you for playing with purpose.</p>
    <el-progress :percentage="progress" status="success" class="mb-3" />
    <p class="text-gray-400 text-sm mb-4">
      Raised <span class="text-gold font-semibold">${{ raised.toLocaleString() }}</span> of ${{ goal.toLocaleString() }}
    </p>
    <el-button type="warning" class="animate__animated animate__heartBeat animate__repeat-2" @click="donate">
      Donate Now
    </el-button>
  </div>
</template>

<script>
export default {
  data(){ return { raised: 5000, goal: 10000 } },
  computed:{ progress(){ return Math.round(this.raised / this.goal * 100) } },
  methods:{ donate(){ alert('Thank you for your support!') } }
}
</script>
