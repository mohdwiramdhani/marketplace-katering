<template>
    <div class="container mt-5">
      <h1 class="text-center mb-4">Detail Pesanan</h1>
  
      <div class="row">
        <div class="col-md-6">
          <div class="card mb-4">
            <div class="card-body" v-if="order">
              <div class="text-center mt-3">
                <strong>Struk Pembayaran:</strong>
                <div v-if="order.payment_receipt">
                  <button @click="showPaymentReceipt" class="btn">Cek Struk Pembayaran</button>
                </div>
                <div v-else>
                  <p>Struk belum ada.</p>
                </div>
              </div>
  
              <form @submit.prevent="uploadPaymentReceipt" enctype="multipart/form-data">
                <div class="form-group mb-3">
                  <label for="menu">Menu</label>
                  <input type="text" class="form-control" v-model="order.menu_name" id="menu" disabled />
                </div>
  
                <div class="form-group mb-3">
                  <label for="quantity">Jumlah</label>
                  <input type="number" class="form-control" v-model="order.quantity" id="quantity" disabled />
                </div>
  
                <div class="form-group mb-3">
                  <label for="total_price">Total Harga</label>
                  <input type="text" class="form-control" v-model="formattedTotalPrice" id="total_price" disabled />
                </div>
  
                <div class="form-group mb-3">
                  <label for="address">Alamat</label>
                  <input type="text" class="form-control" v-model="order.address" id="address" disabled />
                </div>
  
                <div class="form-group mb-3">
                  <label for="contact">Kontak</label>
                  <input type="text" class="form-control" v-model="order.contact" id="contact" disabled />
                </div>
  
                <div class="form-group mb-3">
                  <label for="notes">Catatan</label>
                  <input type="text" class="form-control" v-model="order.notes" id="notes" disabled />
                </div>
  
                <div class="form-group mb-3">
                  <label for="delivery_date">Tanggal Pengiriman</label>
                  <input type="text" class="form-control" v-model="order.delivery_date" id="delivery_date" disabled />
                </div>
  
                <div class="form-group mb-3">
                  <label for="status">Status</label>
                  <input type="text" class="form-control" v-model="translatedOrderStatus" id="status" disabled />
                </div>
  
                <div class="form-group mb-3">
                  <label for="payment_receipt">Unggah Bukti Pembayaran</label>
                  <input type="file" class="form-control" @change="handleFileUpload" id="payment_receipt" required />
                </div>
  
                <div class="d-flex justify-content-between mt-4">
                  <button type="submit" class="btn btn-primary">Unggah Bukti Pembayaran</button>
                  <router-link to="/order-list" class="btn btn-secondary">Kembali</router-link>
                </div>
              </form>
            </div>
            <div v-else class="alert alert-danger" role="alert">
              Gagal memuat detail pesanan. Silakan coba lagi nanti.
            </div>
          </div>
        </div>
  
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="mt-4 mb-4">Detail Invoice</h5>
              <div v-if="order && order.invoice">
                <div class="form-group mb-3">
                  <label for="invoice_total_price">Total Harga</label>
                  <input type="text" class="form-control" v-model="formattedInvoiceTotalPrice" id="invoice_total_price" disabled />
                </div>
                <div class="form-group mb-3">
                  <label for="invoice_date">Tanggal</label>
                  <input type="text" class="form-control" v-model="order.invoice.invoice_date" id="invoice_date" disabled />
                </div>
                <div class="form-group mb-3">
                  <label for="invoice_status">Status</label>
                  <input type="text" class="form-control" v-model="translatedInvoiceStatus" id="invoice_status" disabled />
                </div>
              </div>
              <div v-else>
                <p>Invoice belum tersedia untuk pesanan ini.</p>
              </div>
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
    name: 'OrderListDetail',
    data() {
      return {
        order: null,
        paymentReceiptFile: null,
      };
    },
    computed: {
      formattedTotalPrice() {
        return this.order ? Math.floor(this.order.total_price).toString() : '';
      },
      formattedInvoiceTotalPrice() {
        return this.order && this.order.invoice ? Math.floor(this.order.invoice.total_price).toString() : '';
      },
      translatedInvoiceStatus() {
        if (this.order && this.order.invoice) {
          return this.order.invoice.status === 'unpaid' ? 'Belum Dibayar' : 'Sudah Dibayar';
        }
        return '';
      },
      translatedOrderStatus() {
        if (this.order) {
          switch (this.order.status) {
            case 'pending':
              return 'Menunggu';
            case 'in_process':
              return 'Dalam Proses';
            case 'completed':
              return 'Selesai';
            case 'canceled':
              return 'Dibatalkan';
            default:
              return this.order.status;
          }
        }
        return '';
      },
    },
    mounted() {
      this.fetchOrderDetail();
    },
    methods: {
      async fetchOrderDetail() {
        const token = Cookies.get('token');
        const orderId = this.$route.params.id;
        try {
          const response = await axios.get(`http://localhost:8000/api/orders/${orderId}`, {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });
          this.order = response.data.data;
        } catch (error) {
          console.error('Error fetching order detail:', error);
          this.order = null;
        }
      },
      showPaymentReceipt() {
        const url = this.getPaymentReceiptUrl(this.order.payment_receipt);
        window.open(url, '_blank');
      },
      getPaymentReceiptUrl(receipt) {
        return `http://localhost:8000/storage/${receipt}`;
      },
      handleFileUpload(event) {
        this.paymentReceiptFile = event.target.files[0];
      },
      async uploadPaymentReceipt() {
        const token = Cookies.get('token');
        const orderId = this.order.id;
  
        const formData = new FormData();
        formData.append('payment_receipt', this.paymentReceiptFile);
  
        try {
          await axios.post(`http://localhost:8000/api/orders/${orderId}/upload-receipt`, formData, {
            headers: {
              Authorization: `Bearer ${token}`,
              'Content-Type': 'multipart/form-data',
            },
          });
          alert('Bukti pembayaran berhasil diunggah!');
          this.fetchOrderDetail();
        } catch (error) {
          console.error('Error uploading payment receipt:', error);
          alert('Terjadi kesalahan saat mengunggah bukti pembayaran. Silakan coba lagi.');
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
  </style>
  