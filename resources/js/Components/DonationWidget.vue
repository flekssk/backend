<template>
    <div class="bg-gray-900 rounded-2xl p-5 border border-white/10 text-center">
        <h2 class="text-2xl font-bold mb-2">Главное о нас</h2>
        <p class="text-gray-300 mb-4">Мы отдаем большую часть прибыли на благотворительность и собираемся это делать и
            дальше. Спасибо что выбираете нас, в будущем мы вместе будем выбирать организации которым будем
            помогать.</p>
        <el-progress :percentage="progress" status="success" class="mb-3"/>
        <p class="text-gray-400 text-sm mb-4">
            Собрано <span class="text-gold font-semibold">${{ raised.toLocaleString() }}</span> из
            ${{ goal.toLocaleString() }}
        </p>
    </div>
</template>

<script>
export default {
    data() {
        return {raised: 101, goal: 10000}
    },
    computed: {
        progress() {
            return Math.round(this.raised / this.goal * 100)
        }
    },
    methods: {
        donate() {
            alert('Thank you for your support!')
        }
    }
}
</script>
