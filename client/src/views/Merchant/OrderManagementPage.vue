<template>
  <div class="container mt-5">
    <h1 class="text-center mb-4">Manajemen Pesanan</h1>
    <div class="row">
      <div v-if="orders.length === 0" class="alert alert-warning" role="alert">
        Belum ada pesanan yang diterima.
      </div>
      <div class="col-md-12">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Menu</th>
              <th>Pelanggan</th>
              <th>Jumlah</th>
              <th>Total Harga</th>
              <th>Status</th>
              <th>Bukti Pembayaran</th>
              <th>Detail</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id">
              <td>{{ order.id }}</td>
              <td>{{ order.menu.name }}</td>
              <td>{{ order.company_name }}</td>
              <td>{{ order.quantity }}</td>
              <td>Rp {{ formatPrice(order.total_price) }}</td>
              <td>{{ translateStatus(order.status) }}</td>
              <td>{{ order.payment_receipt ? 'Sudah diunggah' : 'Belum diunggah' }}</td>
              <td>
                <router-link :to="`/order/${order.id}`" class="btn btn-brown">Detail</router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Cookies from 'js-cookie';

export default {
  name: 'OrderManagementPage',
  data() {
    return {
      orders: [],
    };
  },
  mounted() {
    this.fetchOrders();
  },
  methods: {
    async fetchOrders() {
      const token = Cookies.get('token');
      try {
        const response = await axios.get('http://localhost:8000/api/orders', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        this.orders = response.data.data;
      } catch (error) {
        console.error('Error fetching orders:', error);
      }
    },
    formatPrice(price) {
      return price.toString().split('.')[0];
    },
    translateStatus(status) {
      const statusMap = {
        pending: 'Menunggu',
        in_process: 'Dalam Proses',
        completed: 'Selesai',
        canceled: 'Dibatalkan',
      };
      return statusMap[status] || status;
    },
  },
};
</script>

<style scoped>
.table {
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
}
.table th,
.table td {
  vertical-align: middle;
}
.btn-brown {
  background-color: #8B4513;
  color: white;
}
.btn-brown:hover {
  background-color: #7B3F00;
}
</style>
