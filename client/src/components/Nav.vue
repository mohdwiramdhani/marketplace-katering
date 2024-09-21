<template>
  <nav class="navbar navbar-expand-md navbar-dark" style="background-color: #66330A; margin-bottom: 1rem;">
    <div class="container-fluid">
      <router-link to="/" class="navbar-brand" href="#">Katering</router-link>

      <div>
        <ul class="navbar-nav me-auto mb-2 mb-md-0">
          <template v-if="!isAuthenticated">
            <li class="nav-item">
              <router-link to="/register-merchant" class="nav-link" href="#">Merchant</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/register-customer" class="nav-link" href="#">Customer</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/login" class="nav-link" href="#">Login</router-link>
            </li>
          </template>
          <template v-else>
            <li class="nav-item">
              <router-link to="/profile" class="nav-link" href="#">Merchant</router-link>
            </li>
            <li class="nav-item">
              <a @click="logout" class="nav-link" href="#">Logout</a>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import Cookies from 'js-cookie';

export default {
  name: "Nav",
  data() {
    return {
      isAuthenticated: false,
    };
  },
  mounted() {
    this.checkAuth();
  },
  watch: {
    '$route'() {
      this.checkAuth(); // Cek status autentikasi setiap kali rute berubah
    }
  },
  methods: {
    checkAuth() {
      const token = Cookies.get('token');
      this.isAuthenticated = !!token; // Update status autentikasi
    },
    logout() {
      Cookies.remove('token');
      this.isAuthenticated = false;
      this.$router.push('/login');
    }
  }
};
</script>

<style>
.nav-link {
  color: #ffffff; /* Ubah warna teks link */
}

.nav-link:hover {
  color: #ffd700; /* Ubah warna teks link saat hover */
}
</style>
