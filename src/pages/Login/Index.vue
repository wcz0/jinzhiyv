<template>
  <p>请登录 phone:</p>
  <input type="text" v-model="phone" />
  <p>password:</p>
  <input type="password" v-model="password" />
  <br />
  <button @click="login">登录</button>
  <button @click="test">1</button>
  <p v-show="isSuccess">
    <router-link to="/">返回首页</router-link>..
  </p>
</template>

<script>
export default {
  props: [
    'phone',
    'password',
    'isSuccess',
  ],
  methods: {
    login() {
      this.axios.post("/api/login", {
        phone: this.phone,
        password: this.password
      }).then(res => {
        console.log(res)
        if (res.code === 1) {
          this.isSuccess = true
          setTimeout(() => {
            this.$router.push("/");
          }, 3000);
        }
      })
    },
  }
};
</script>

<style>
</style>