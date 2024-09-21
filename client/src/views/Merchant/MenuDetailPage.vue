<template>
    <div class="container mt-5">
      <h1 class="text-center mb-4">Detail Menu</h1>
      <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
          <form @submit.prevent="updateMenu">
            <div class="text-center mb-4">
              <img
                :src="menu.photo ? getImageUrl(menu.photo) : require('@/assets/default.png')"
                class="img-fluid mb-2"
                alt="Menu Photo"
                style="height: 200px; object-fit: cover;"
              />
            </div>
            <div class="form-group mb-3">
              <label for="name">Nama Menu</label>
              <input type="text" class="form-control" v-model="menu.name" id="name" required />
            </div>
            <div class="form-group mb-3">
              <label for="description">Deskripsi</label>
              <textarea class="form-control" v-model="menu.description" id="description" required></textarea>
            </div>
            <div class="form-group mb-3">
              <label for="quantity">Jumlah</label>
              <input type="number" class="form-control" v-model="menu.quantity" id="quantity" required />
            </div>
            <div class="form-group mb-3">
              <label for="price">Harga</label>
              <input type="text" class="form-control" v-model="menu.price" id="price" required />
            </div>
            <div class="form-group mb-3">
              <label for="type">Tipe</label>
              <select class="form-control" v-model="menu.type" id="type" required>
                <option value="food">Makanan</option>
                <option value="drink">Minuman</option>
              </select>
            </div>
            <div class="form-group mb-3">
              <label for="photo">Upload Foto</label>
              <input type="file" class="form-control" @change="onFileChange" id="photo" />
            </div>
            <input type="hidden" name="_method" value="PUT" />
            <div class="d-flex justify-content-between mt-4">
              <button type="submit" class="btn btn-success">Perbarui</button>
              <router-link to="/menu-management" class="btn btn-secondary">Kembali</router-link>
            </div>
          </form>
          <div v-if="notification" class="alert" :class="notification.type" role="alert" style="margin-top: 20px;">
            {{ notification.message }}
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import Cookies from 'js-cookie';
  
  export default {
    name: 'MenuDetailPage',
    data() {
      return {
        menu: {},
        photoFile: null,
        notification: null,
      };
    },
    mounted() {
      this.fetchMenuDetail();
    },
    methods: {
      async fetchMenuDetail() {
        const token = Cookies.get('token');
        const menuId = this.$route.params.id;
        try {
          const response = await axios.get(`http://localhost:8000/api/menus/${menuId}`, {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });
          this.menu = response.data.data.menu;
          this.menu.price = Math.floor(this.menu.price);
        } catch (error) {
          console.error('Error fetching menu detail:', error);
        }
      },
      getImageUrl(photo) {
        return `http://localhost:8000/storage/${photo}`;
      },
      onFileChange(event) {
        this.photoFile = event.target.files[0];
      },
      async updateMenu() {
        const token = Cookies.get('token');
        const menuId = this.menu.id;
        const formData = new FormData();
        formData.append('name', this.menu.name);
        formData.append('description', this.menu.description);
        formData.append('quantity', this.menu.quantity);
        formData.append('price', this.menu.price);
        formData.append('type', this.menu.type);
        if (this.photoFile) {
          formData.append('photo', this.photoFile);
        }
        try {
          await axios.post(`http://localhost:8000/api/menus/${menuId}`, formData, {
            headers: {
              Authorization: `Bearer ${token}`,
              'X-HTTP-Method-Override': 'PUT',
              'Content-Type': 'multipart/form-data',
            },
          });
          this.notification = {
            type: 'alert alert-success',
            message: 'Menu berhasil diperbarui!',
          };
          setTimeout(() => {
            this.$router.push({ name: 'menuManagement' });
          }, 2000);
        } catch (error) {
          console.error('Error updating menu:', error);
          this.notification = {
            type: 'alert alert-danger',
            message: 'Terjadi kesalahan saat memperbarui menu. Silakan coba lagi.',
          };
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .card {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
  }
  .img-fluid {
    border-radius: 8px;
  }
  </style>
  