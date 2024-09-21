<template>
    <div class="not-found">
      <h1 class="text-center">404</h1>
      <h2 class="text-center">Halaman Tidak Ditemukan</h2>
      <p class="text-center">Maaf, halaman yang Anda cari tidak tersedia.</p>
      <div class="text-center">
        <router-link to="/" class="btn btn-primary">Kembali ke Beranda</router-link>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'NotFound'
  }
  </script>
  
  <style scoped>
  .not-found {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    text-align: center;
  }
  </style>
  