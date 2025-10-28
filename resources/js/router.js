import { createRouter, createWebHistory } from 'vue-router';
import Home from './Pages/Home.vue';
import Slots from './Pages/Slots.vue';
import Pay from './Pages/Pay.vue';
import Withdraw from './Pages/Withdraw.vue';

const routes = [
  { path: '/', component: Home },
  { path: '/slots', component: Slots },
  { path: '/pay', component: Pay },
  { path: '/withdraw', component: Withdraw },
  { path: '/:pathMatch(.*)*', redirect: '/' },
];

export default createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(){ return { top: 0 } }
});
