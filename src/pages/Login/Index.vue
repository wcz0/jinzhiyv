<template>
  <p>请登录 phone:</p>
  <input type="text" v-model="phone" />
  <p>password:</p>
  <input type="password" v-model="password" />
  <br />
  <button @click="login">登录</button>
  <div v-show="isSuccess">
    <h3>返回首页中...</h3>
    <router-link to="/">立刻返回</router-link>
  </div>
</template>

<script>
export default {
  data() {
    return {
      phone: '',
      password: '',
      isSuccess: false
    }
  },
  methods: {
    login() {
      this.axios.post("/api/login", {
        phone: this.phone,
        password: this.password
      }).then(res => {
        console.log(res)
        if (res.data.code == 1) {
          this.isSuccess = true
          localStorage.setItem('siv', res.data.data.siv)
          localStorage.setItem('stoken', res.data.data.stoken)
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