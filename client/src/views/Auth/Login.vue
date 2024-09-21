<template>
  <div class="container mt-5">
    <h4 class="text-center mb-4">Login</h4>
    <div class="card mx-auto" style="max-width: 400px;">
      <div class="card-body">
        <div class="d-flex justify-content-center mb-4">
          <img class="logo img-fluid w-50" src="@/assets/logo.png" alt="Logo" />
        </div>
        <form @submit.prevent="submit">
          <div class="mb-3">
            <input v-model="data.email" type="email" class="form-control" placeholder="Email" required />
          </div>
          <div class="mb-3">
            <input v-model="data.password" type="password" class="form-control" placeholder="Password" required />
          </div>
          <button class="btn w-100 py-2 mt-2 mb-2" type="submit">Login</button>
        </form>
        <div v-if="errorMessage" class="alert alert-danger mt-3">{{ errorMessage }}</div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import cookies from 'vue-cookies';

export default {
  name: "Login",
  setup() {
    const data = ref({
      email: '',
      password: ''
    });

    const errorMessage = ref('');
    const router = useRouter();

    const submit = async () => {
  errorMessage.value = '';
  try {
    const response = await axios.post("http://localhost:8000/api/auth/login", data.value);

    const userRole = response.data.role;
    const userToken = response.data.token;

    cookies.set('token', userToken, '7d');

    if (userRole === 'merchant') {
      await router.push('/dashboard-merchant');
    } else if (userRole === 'customer') {
      await router.push('/dashboard-customer');
    }

  } catch (error) {
    if (error.response) {
      if (error.response.status === 401) {
        errorMessage.value = 'Email atau password salah.';
      } else {
        errorMessage.value = error.response.data.message || 'Login gagal. Silakan coba lagi.';
      }
    } else {
      errorMessage.value = 'Login gagal. Silakan periksa koneksi Anda.';
    }
  }
};


    return {
      data,
      submit,
      errorMessage
    };
  }
}
</script>

<style>
.card {
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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