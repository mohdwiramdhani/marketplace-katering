<template>
<Nav></Nav>
<main class="form-signin w-100 m-auto">
<router-view></router-view>
</main>
</template>

<style>

</style>

<script>
import Nav from './components/Nav.vue';

export default {
  components: {Nav}
}
</script>