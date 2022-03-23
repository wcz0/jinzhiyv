<template>
  <p>请登录 phone:</p>
  <input type="text" v-model="phone" /> password:
  <input type="password" v-model="password" />
  <button @click="login">登录</button>
</template>

<script>
export default {
  props: ["phone", "password"],
  methods: {

  }
};
</script>

<style>
</style>