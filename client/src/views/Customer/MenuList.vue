<template>
    <div class="container mt-5">
      <h1 class="text-center mb-4">Daftar Menu</h1>
      <div class="row mb-4">
        <div class="col-md-4">
          <select v-model="searchType" id="menuType" class="form-control" @change="searchMenus">
            <option value="">Pilih Tipe (Semua)</option>
            <option value="food">Makanan</option>
            <option value="drink">Minuman</option>
          </select>
        </div>
      </div>
  
      <div class="row">
        <div v-if="menus.length === 0" class="alert alert-warning" role="alert">
          Belum ada menu yang tersedia.
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
              <p class="card-text"><strong>Stok: </strong> {{ menu.quantity }}</p>
              <p class="card-text"><strong>Harga: </strong> Rp {{ formatPrice(menu.price) }}</p>
              <button @click="openOrderModal(menu)" class="btn btn-success">Pesan</button>
            </div>
          </div>
        </div>
      </div>
  
      <div class="modal" tabindex="-1" role="dialog" v-if="showModal">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Pesan Menu: {{ selectedMenu.name }}</h5>
            </div>
            <div class="modal-body">
              <form @submit.prevent="submitOrder">
                <div class="form-group">
                  <label for="quantity">Jumlah</label>
                  <input type="number" id="quantity" v-model="order.quantity" class="form-control" required min="1" />
                </div>
                <div class="form-group">
                  <label for="notes">Catatan (Opsional)</label>
                  <textarea id="notes" v-model="order.notes" class="form-control"></textarea>
                </div>
                <div class="form-group mb-4">
                  <label for="delivery_date">Tanggal Pengantaran</label>
                  <input type="date" id="delivery_date" v-model="order.delivery_date" class="form-control" required />
                </div>
                <div class="d-flex justify-content-between">
                  <button type="submit" class="btn btn-success">Order</button>
                  <button type="button" class="btn btn-secondary" @click="closeOrderModal">Tutup</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
  
      <div class="modal" tabindex="-1" role="dialog" v-if="showNotification">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Notifikasi</h5>
            </div>
            <div class="modal-body">
              <p>{{ notification }}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" @click="handleNotificationOk">OK</button>
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
    name: 'MenuList',
    data() {
      return {
        menus: [],
        searchType: '',
        showModal: false,
        selectedMenu: {},
        order: {
          quantity: 1,
          notes: '',
          delivery_date: '',
        },
        notification: '',
        showNotification: false,
      };
    },
    mounted() {
      this.fetchMenus();
    },
    methods: {
      async fetchMenus() {
        const token = Cookies.get('token');
        try {
          const response = await axios.get('http://localhost:8000/api/all-menus', {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });
          this.menus = response.data.data.menus;
        } catch (error) {
          console.error('Error fetching menus:', error);
        }
      },
      async searchMenus() {
        const token = Cookies.get('token');
        try {
          const response = await axios.get('http://localhost:8000/api/search-menus', {
            headers: {
              Authorization: `Bearer ${token}`,
            },
            params: {
              type: this.searchType,
            },
          });
          this.menus = response.data.data.menus;
        } catch (error) {
          console.error('Error searching menus:', error);
        }
      },
      getImageUrl(photo) {
        return `http://localhost:8000/storage/${photo}`;
      },
      formatPrice(price) {
        return parseInt(price).toLocaleString();
      },
      openOrderModal(menu) {
        this.selectedMenu = menu;
        this.showModal = true;
        this.order.quantity = 1;
        this.order.notes = '';
        this.order.delivery_date = '';
      },
      closeOrderModal() {
        this.showModal = false;
      },
      async submitOrder() {
        if (this.order.quantity > this.selectedMenu.quantity) {
          this.notification = 'Jumlah yang diminta melebihi stok yang tersedia.';
          this.showNotification = true;
          return;
        }
  
        const token = Cookies.get('token');
        const orderData = {
          menu_id: this.selectedMenu.id,
          quantity: this.order.quantity,
          notes: this.order.notes,
          delivery_date: this.order.delivery_date,
        };
  
        try {
          const response = await axios.post('http://localhost:8000/api/orders', orderData, {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });
          this.notification = 'Order berhasil dikirim!';
          this.showNotification = true;
          this.closeOrderModal();
        } catch (error) {
          console.error('Error submitting order:', error);
          this.notification = 'Terjadi kesalahan saat mengirim order.';
          this.showNotification = true;
        }
      },
      closeNotification() {
        this.showNotification = false;
      },
      handleNotificationOk() {
        this.closeNotification();
        this.fetchMenus();
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
  
  .btn-success {
    background-color: #28a745;
    color: white;
  }
  
  .btn-success:hover {
    background-color: #218838;
  }
  
  .btn-secondary {
    background-color: #6c757d;
    color: white;
  }
  
  .btn-secondary:hover {
    background-color: #5a6268;
  }
  
  .modal {
    display: block;
  }
  </style>
  