<template>
    <div class="container mt-5">
      <h1 class="text-center mb-4">Pesan Menu: {{ menu.name }}</h1>
      <form @submit.prevent="submitOrder">
        <div class="form-group">
          <label for="quantity">Jumlah</label>
          <input type="number" id="quantity" v-model="order.quantity" class="form-control" required min="1" />
        </div>
        <div class="form-group">
          <label for="notes">Catatan (Opsional)</label>
          <textarea id="notes" v-model="order.notes" class="form-control"></textarea>
        </div>
        <div class="form-group">
          <label for="delivery_date">Tanggal Pengantaran</label>
          <input type="date" id="delivery_date" v-model="order.delivery_date" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-success">Kirim Order</button>
      </form>
  
      <div v-if="notification" class="alert alert-success mt-3">
        {{ notification }}
      </div>
      <div v-if="error" class="alert alert-danger mt-3">
        {{ error }}
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import Cookies from 'js-cookie';
  
  export default {
    name: 'OrderMenu',
    data() {
      return {
        order: {
          quantity: 1,
          notes: '',
          delivery_date: '',
        },
        notification: '',
        error: '',
        menu: this.$route.params.menu,
      };
    },
    methods: {
      async submitOrder() {
        const token = Cookies.get('token');
        const orderData = {
          menu_id: this.menu.id,
          quantity: this.order.quantity,
          notes: this.order.notes,
          delivery_date: this.order.delivery_date,
        };
  
        try {
          this.notification = 'Order berhasil dikirim!';
          this.error = '';
          setTimeout(() => {
            this.$router.push({ name: 'MenuList' });
          }, 2000);
        } catch (error) {
          console.error('Error submitting order:', error);
          this.error = 'Terjadi kesalahan saat mengirim order.';
          this.notification = '';
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .btn-success {
    background-color: #28a745;
    color: white;
  }
  </style>
  