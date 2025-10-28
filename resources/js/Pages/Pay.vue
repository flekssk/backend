<template>
  <section class="max-w-xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">Deposit Funds</h1>
    <div class="bg-gray-900 rounded-2xl p-5 border border-white/10 space-y-4">
      <div>
        <label class="block text-gray-300 mb-1">Payment Method</label>
        <el-select v-model="method" placeholder="Select method" class="w-full">
          <el-option label="Visa" value="Visa" />
          <el-option label="MasterCard" value="MasterCard" />
          <el-option label="SBP" value="SBP" />
          <el-option label="Crypto" value="Crypto" />
        </el-select>
      </div>
      <div>
        <label class="block text-gray-300 mb-1">Amount</label>
        <el-input v-model.number="amount" type="number" placeholder="Enter amount" />
        <div class="flex gap-2 mt-2 flex-wrap">
          <button v-for="p in presets" :key="p" @click="amount=p"
                  class="px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 border border-white/10 text-sm">
            {{ p }}
          </button>
        </div>
      </div>
      <el-button type="primary" class="!bg-purple-600 hover:!bg-purple-700 btn-glossy" @click="deposit">
        Continue to Pay
      </el-button>
    </div>
  </section>
</template>

<script>
import { ref } from 'vue';
export default {
  setup(){
    const method = ref('');
    const amount = ref(null);
    const presets = [200, 500, 1500, 3000, 5000];
    const deposit = () => {
      if(method.value && amount.value){
        alert(`Depositing ${amount.value} via ${method.value}`);
        method.value = ''; amount.value = null;
      } else {
        alert('Select method and enter amount.');
      }
    };
    return { method, amount, presets, deposit };
  }
}
</script>
