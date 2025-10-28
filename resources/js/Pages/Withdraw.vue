<template>
  <section class="max-w-xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">Withdraw Funds</h1>
    <div class="bg-gray-900 rounded-2xl p-5 border border-white/10 space-y-4">
      <div>
        <label class="block text-gray-300 mb-1">Withdrawal Method</label>
        <el-select v-model="method" placeholder="Select method" class="w-full">
          <el-option label="Bank Transfer" value="Bank" />
          <el-option label="Crypto" value="Crypto" />
          <el-option label="SBP" value="SBP" />
        </el-select>
      </div>
      <div>
        <label class="block text-gray-300 mb-1">Amount</label>
        <el-input v-model.number="amount" type="number" placeholder="Enter amount" />
        <p class="text-xs text-gray-400 mt-1">Fee: 5% • You will receive: <strong>{{ netAmount }}</strong></p>
      </div>
      <el-button type="primary" class="!bg-gold hover:brightness-110 text-black btn-glossy" @click="withdraw">
        Create Withdrawal
      </el-button>
    </div>
  </section>
</template>

<script>
import { ref, computed } from 'vue';
export default {
  setup(){
    const method = ref('');
    const amount = ref(null);
    const netAmount = computed(() => amount.value ? (amount.value * 0.95).toFixed(2) : '0.00');
    const withdraw = () => {
      if(method.value && amount.value){
        alert(`Withdrawing ${amount.value} via ${method.value}`);
        method.value = ''; amount.value = null;
      } else {
        alert('Select method and enter amount.');
      }
    };
    return { method, amount, netAmount, withdraw };
  }
}
</script>
