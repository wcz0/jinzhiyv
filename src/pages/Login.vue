<template>
  请登录 phone:<input type="text" v-model="phone" /> password:<input
    type="password"
    v-model="password"
  />
  <button @click="login">登录</button>
</template>

<script>
export default {
  props: ["phone", "password"],
  methods: {
    login() {
      this.$emit("login", {
        phone: this.phone,
        password: this.password
      });
    }
  }
};
</script>

<style>
</style>