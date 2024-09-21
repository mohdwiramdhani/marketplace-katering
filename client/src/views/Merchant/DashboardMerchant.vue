<template>
    <div class="container mt-5">
      <h1 class="text-center mb-4">Dashboard Merchant</h1>
      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="card" style="background-color: #FACA5F;">
            <div class="card-body text-center">
              <h5 class="card-title" style="color: #66330A;">Merchant</h5>
              <p class="card-text">Kelola profil merchant.</p>
              <router-link to="/merchant-profile" class="btn" style="background-color: #66330A; color: white;">Lihat Profil</router-link>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card" style="background-color: #FACA5F;">
            <div class="card-body text-center">
              <h5 class="card-title" style="color: #66330A;">Menu</h5>
              <p class="card-text">Kelola menu katering.</p>
              <router-link to="/menu-management" class="btn" style="background-color: #66330A; color: white;">Kelola Menu</router-link>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card" style="background-color: #FACA5F;">
            <div class="card-body text-center">
              <h5 class="card-title" style="color: #66330A;">Order</h5>
              <p class="card-text">Lihat dan kelola pesanan.</p>
              <router-link to="/order-management" class="btn" style="background-color: #66330A; color: white;">Lihat Pesanan</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "DashboardMerchant"
  };
  </script>
  
  <style scoped>
  .card {
    transition: transform 0.2s;
  }
  
  .card:hover {
    transform: scale(1.05);
  }
  
  .card-title {
    font-size: 1.5rem;
    font-weight: bold;
  }
  
  .card-text {
    margin-bottom: 1.5rem;
  }
  </style>
  