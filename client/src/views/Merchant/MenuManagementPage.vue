<template>
    <div class="container mt-5">
      <h1 class="text-center mb-4">Manajemen Menu</h1>
      <div class="text-end mb-3">
        <router-link to="/add-menu" class="btn btn-brown">Tambah Menu</router-link>
      </div>
      <div class="row">
        <div v-if="menus.length === 0" class="alert alert-warning" role="alert">
          Belum ada menu yang ditambahkan.
        </div>
        <div v-for="menu in menus" :key="menu.id" class="col-md-4 mb-3">
          <div class="card">
            <img
              :src="menu.photo ? getImageUrl(menu.photo) : require('@/assets/default.png')"
              class="card-img-top"
              alt="Menu Photo"
              style="height: 200px; object-fit: cover;"
            />
            <div class="card-body">
              <h5 class="card-title">{{ menu.name }}</h5>
              <p class="card-text">{{ menu.description }}</p>
              <p class="card-text"><strong>Harga: </strong> Rp {{ formatPrice(menu.price) }}</p>
              <router-link :to="`/menu/${menu.id}`" class="btn btn-brown">Detail</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import Cookies from 'js-cookie';
  
  export default {
    name: 'MenuManagementPage',
    data() {
      return {
        menus: [],
      };
    },
    mounted() {
      this.fetchMenus();
    },
    methods: {
      async fetchMenus() {
        const token = Cookies.get('token');
        try {
          const response = await axios.get('http://localhost:8000/api/menus', {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });
          this.menus = response.data.data.menus;
        } catch (error) {
          console.error('Error fetching menus:', error);
        }
      },
      getImageUrl(photo) {
        return `http://localhost:8000/storage/${photo}`;
      },
      formatPrice(price) {
        return price.toString().split('.')[0];
      },
    },
  };
  </script>
  
  <style scoped>
  .card {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
  }
  
  .card-img-top {
    height: 200px;
    object-fit: cover;
  }
  
  .btn-brown {
    background-color: #8B4513;
    color: white;
  }
  
  .btn-brown:hover {
    background-color: #6F4C3E;
  }
  </style>
  