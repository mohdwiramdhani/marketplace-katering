<template>
  <div class="container mt-5 mb-5">
    <h4 class="text-center mb-4">Daftar Customer</h4>
    <div class="card mx-auto shadow-sm" style="max-width: 400px;">
      <div class="card-body">
        <div class="d-flex justify-content-center mb-4">
          <img class="logo img-fluid w-50" src="@/assets/logo.png" alt="Logo" />
        </div>
        <form @submit.prevent="submit">
          <div class="mb-3">
            <input v-model="data.name" type="text" class="form-control" placeholder="Nama" required />
          </div>
          <div class="mb-3">
            <input v-model="data.email" type="email" class="form-control" placeholder="Email" required />
          </div>
          <div class="mb-3">
            <input v-model="data.password" type="password" class="form-control" placeholder="Password" required />
          </div>
          <div class="mb-3">
            <input v-model="data.password_confirmation" type="password" class="form-control" placeholder="Konfirmasi Password" required />
          </div>
          <div class="mb-3">
            <input v-model="data.company_name" type="text" class="form-control" placeholder="Nama Kantor" required />
          </div>
          <div class="mb-3">
            <input v-model="data.contact" type="text" class="form-control" placeholder="Kontak" required />
          </div>
          <div class="mb-3">
            <input v-model="data.address" type="text" class="form-control" placeholder="Alamat" required />
          </div>

          <div v-if="errorMessage" class="alert alert-danger text-center mb-3">{{ errorMessage }}</div>
          <div v-if="successMessage" class="alert alert-success text-center mb-3">{{ successMessage }}</div>

          <button class="btn btn-primary w-100 py-2 mt-2 mb-2" type="submit">Daftar</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
  name: "RegisterCustomer",
  setup() {
    const data = ref({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      role: 'customer',
      company_name: '',
      contact: '',
      address: ''
    });

    const errorMessage = ref('');
    const successMessage = ref('');
    const router = useRouter();

    const submit = async () => {
      if (data.value.password !== data.value.password_confirmation) {
        errorMessage.value = "Password tidak cocok!";
        return;
      }

      try {
        await axios.post("http://localhost:8000/api/auth/register", data.value);
        successMessage.value = "Pendaftaran berhasil! Silakan masuk.";
        setTimeout(() => {
          router.push('/login');
        }, 2000);
      } catch (error) {
        if (error.response && error.response.data.message) {
          errorMessage.value = error.response.data.message;
        } else {
          errorMessage.value = 'Terjadi kesalahan. Silakan coba lagi.';
        }
      }
    };

    return {
      data,
      submit,
      errorMessage,
      successMessage
    };
  }
}
</script>

<style scoped>
.card {
  border-radius: 8px;
}

.card-body button {
  background-color: #66330A;
  color: white;
  border: none;
}

.card-body button:hover {
  background-color: #4b2403;
  color: white;
}

.form-control {
  height: 45px;
}
</style>
