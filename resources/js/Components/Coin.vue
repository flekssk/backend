<template>
  <div ref="wrap" class="relative" :style="{ width: size + 'px', height: size + 'px' }">
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import * as THREE from 'three'
import coinTexUrl from '@/assets/images/heroes/coin.png'

const props = defineProps({
  size: { type: Number, default: 90 },       // размер в px
  thickness: { type: Number, default: 0.12 }, // толщина монеты (в условных единицах)
  autoSpin: { type: Boolean, default: true }  // автоспин с живой динамикой
})

const wrap = ref(null)

let scene, camera, renderer, coin
let raf = 0
let currentSpeed = 0
let targetSpeed = 0.01
let spinTimer = 0

function makeScene() {
  scene = new THREE.Scene()

  // камера
  const aspect = 1
  camera = new THREE.PerspectiveCamera(35, aspect, 0.1, 50)
  camera.position.set(0, 0, 4)

  // рендер
  renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true })
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
  renderer.setSize(props.size, props.size)
  renderer.outputColorSpace = THREE.SRGBColorSpace
  wrap.value.appendChild(renderer.domElement)

  // свет
  const amb = new THREE.AmbientLight(0xffffff, 1)
  scene.add(amb)

  const dir1 = new THREE.DirectionalLight(0xffffff, 1)
  dir1.position.set(3, 4, 2)
  scene.add(dir1)

  const dir2 = new THREE.DirectionalLight(0xffe28a, 1) // тёплый блик
  dir2.position.set(-3, -2, 1.5)
  scene.add(dir2)

  // геометрия монеты
  const radius = 1
  const height = props.thickness
  const radialSegments = 256
  const geo = new THREE.CylinderGeometry(radius, radius, height, radialSegments, 1, false)

  // текстуры лиц (одну и ту же с обеих сторон)
  const loader = new THREE.TextureLoader()
  const faceTex = loader.load(coinTexUrl)
  faceTex.colorSpace = THREE.SRGBColorSpace
  faceTex.anisotropy = 8

  // материалы: [0] — торец, [1] — верх, [2] — низ
  const edgeMat = new THREE.MeshStandardMaterial({
    color: 0xFFD24A,       // золото
    metalness: 0.85,
    roughness: 0.25
  })
  const faceMat = new THREE.MeshStandardMaterial({
    map: faceTex,
    metalness: 0,
    roughness: 1
  })

  coin = new THREE.Mesh(geo, [edgeMat, faceMat, faceMat])
  // немного повернём, чтобы было «живее»
  coin.rotation.x = Math.PI * 0.4
  scene.add(coin)

  // анимация
  spinTimer = performance.now()
  animate()
}

function animate() {
  raf = requestAnimationFrame(animate)

  // «живая» скорость: плавно тянемся к targetSpeed
  currentSpeed += (targetSpeed - currentSpeed) * 0.08
  coin.rotation.y += currentSpeed

  renderer.render(scene, camera)

  // каждые ~0.6–1.6с меняем скорость и направление
  const now = performance.now()
  if (props.autoSpin && now - spinTimer > THREE.MathUtils.randInt(600, 1600)) {
    const dir = Math.random() > 0.5 ? 1 : -1
    const speed = THREE.MathUtils.randFloat(0.01, 0.18) // от медленной до быстрой
    targetSpeed = dir * speed
    spinTimer = now
  }
}

function resize() {
  if (!renderer) return
  const s = props.size
  renderer.setSize(s, s)
  camera.aspect = 1
  camera.updateProjectionMatrix()
}

onMounted(() => {
  makeScene()
  window.addEventListener('resize', resize)
})
onBeforeUnmount(() => {
  cancelAnimationFrame(raf)
  window.removeEventListener('resize', resize)
  renderer?.dispose()
})

watch(() => props.size, resize)
</script>

<style scoped>
:host, .relative { display: block }
</style>