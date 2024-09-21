<template>
    <div class="container mt-5">
      <h1 class="text-center mb-4">Tambah Menu</h1>
      <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
          <form @submit.prevent="addMenu">
            <div class="form-group mb-3">
              <label for="name">Nama Menu</label>
              <input type="text" class="form-control" v-model="newMenu.name" id="name" required />
            </div>
            <div class="form-group mb-3">
              <label for="description">Deskripsi</label>
              <textarea class="form-control" v-model="newMenu.description" id="description" required></textarea>
            </div>
            <div class="form-group mb-3">
              <label for="quantity">Jumlah</label>
              <input type="number" class="form-control" v-model="newMenu.quantity" id="quantity" required />
            </div>
            <div class="form-group mb-3">
              <label for="price">Harga</label>
              <input type="number" class="form-control" v-model="newMenu.price" id="price" required />
            </div>
            <div class="form-group mb-3">
              <label for="type">Tipe</label>
              <select class="form-control" v-model="newMenu.type" id="type" required>
                <option value="food">Makanan</option>
                <option value="drink">Minuman</option>
              </select>
            </div>
            <div class="form-group mb-3">
              <label for="photo">Upload Foto</label>
              <input type="file" class="form-control" @change="onFileChange" id="photo" />
            </div>
            <div class="d-flex justify-content-between mt-4">
              <button type="submit" class="btn btn-success">Tambah Menu</button>
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
    name: 'AddMenuPage',
    data() {
      return {
        newMenu: {
          name: '',
          description: '',
          quantity: 0,
          price: 0,
          type: 'food',
        },
        photoFile: null,
        notification: null,
      };
    },
    methods: {
      onFileChange(event) {
        this.photoFile = event.target.files[0];
      },
      async addMenu() {
        const token = Cookies.get('token');
        const formData = new FormData();
        formData.append('name', this.newMenu.name);
        formData.append('description', this.newMenu.description);
        formData.append('quantity', this.newMenu.quantity);
        formData.append('price', this.newMenu.price);
        formData.append('type', this.newMenu.type);
        if (this.photoFile) {
          formData.append('photo', this.photoFile);
        }
        try {
          await axios.post('http://localhost:8000/api/menus', formData, {
            headers: {
              Authorization: `Bearer ${token}`,
              'Content-Type': 'multipart/form-data',
            },
          });
          this.notification = {
            type: 'alert alert-success',
            message: 'Menu berhasil ditambahkan!',
          };
          this.resetForm();
  
          setTimeout(() => {
            this.$router.push('/menu-management');
          }, 2000);
        } catch (error) {
          console.error('Error adding menu:', error);
          this.notification = {
            type: 'alert alert-danger',
            message: 'Terjadi kesalahan saat menambahkan menu. Silakan coba lagi.',
          };
        }
      },
      resetForm() {
        this.newMenu.name = '';
        this.newMenu.description = '';
        this.newMenu.quantity = 0;
        this.newMenu.price = 0;
        this.newMenu.type = 'food';
        this.photoFile = null;
      },
    },
  };
  </script>
  
  <style scoped>
  .card {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
  }
  </style>
  