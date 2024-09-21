<template>
    <div class="container mt-5">
      <h1 class="text-center mb-4">Profil Merchant</h1>
      <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
          <div
            v-if="alertMessage"
            :class="`alert alert-${alertType} alert-dismissible fade show`"
            role="alert"
          >
            {{ alertMessage }}
          </div>
          <form @submit.prevent="updateProfile">
            <div class="mb-3">
              <label for="company_name" class="form-label">Nama Perusahaan</label>
              <input
                type="text"
                class="form-control"
                id="company_name"
                v-model="merchant.company_name"
                required
              />
            </div>
            <div class="mb-3">
              <label for="contact" class="form-label">Kontak</label>
              <input
                type="text"
                class="form-control"
                id="contact"
                v-model="merchant.contact"
                required
              />
            </div>
            <div class="mb-3">
              <label for="address" class="form-label">Alamat</label>
              <input
                type="text"
                class="form-control"
                id="address"
                v-model="merchant.address"
                required
              />
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Deskripsi</label>
              <textarea
                class="form-control"
                id="description"
                v-model="merchant.description"
                rows="3"
              ></textarea>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary w-100">
                Perbarui
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import Cookies from 'js-cookie';
  
  export default {
    name: "ProfileMerchant",
    data() {
      return {
        merchant: {
          company_name: '',
          contact: '',
          address: '',
          description: null,
        },
        alertMessage: '', 
        alertType: ''
      };
    },
    mounted() {
      this.fetchMerchantProfile();
    },
    methods: {
      async fetchMerchantProfile() {
        const token = Cookies.get('token');
        try {
          const response = await axios.get('http://localhost:8000/api/merchant', {
            headers: {
              Authorization: `Bearer ${token}`
            }
          });
          this.merchant = response.data.data;
        } catch (error) {
          console.error("Error fetching merchant profile:", error);
        }
      },
      async updateProfile() {
        const token = Cookies.get('token');
        const updateData = {
          company_name: this.merchant.company_name,
          contact: this.merchant.contact,
          address: this.merchant.address,
        };
        if (this.merchant.description) {
          updateData.description = this.merchant.description;
        }
        try {
          await axios.put('http://localhost:8000/api/merchant', updateData, {
            headers: {
              Authorization: `Bearer ${token}`
            }
          });
          this.alertMessage = "Profil merchant berhasil diperbarui!";
          this.alertType = "success";
        } catch (error) {
          console.error("Error updating merchant profile:", error);
          this.alertMessage = "Gagal memperbarui profil merchant.";
          this.alertType = "danger";
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .card {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
  }
  </style>
  