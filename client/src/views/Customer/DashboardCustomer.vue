<template>
    <div class="container mt-5">
      <h1 class="text-center mb-4">Dashboard Customer</h1>
      <div class="row justify-content-center">
        <div class="col-md-5 mb-4">
          <div class="card" style="background-color: #FACA5F;">
            <div class="card-body text-center">
              <h5 class="card-title" style="color: #66330A;">Menu</h5>
              <p class="card-text">Cari dan lihat menu katering.</p>
              <router-link to="/menu-list" class="btn" style="background-color: #66330A; color: white;">Cek Menu</router-link>
            </div>
          </div>
        </div>
  
        <div class="col-md-5 mb-4">
          <div class="card" style="background-color: #FACA5F;">
            <div class="card-body text-center">
              <h5 class="card-title" style="color: #66330A;">Order</h5>
              <p class="card-text">Lihat status pesanan.</p>
              <router-link to="/order-list" class="btn" style="background-color: #66330A; color: white;">Cek Order</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "DashboardCustomer"
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
  
  .container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }
  
  .row {
    width: 100%;
    max-width: 900px;
  }
  </style>
  