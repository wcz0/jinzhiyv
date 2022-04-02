<template>
  <a-row type="flex">
    <a-col :flex="1">
      <p>请登录 phone:</p>
      <input type="text" v-model="phone" />
      <p>password:</p>
      <input type="password" v-model="password" />
      <br />
    </a-col>
  </a-row>
  <a-row>
    <a-col :span="1">
      <a-button type="primary" @click="login">登录</a-button>
    </a-col>
  </a-row>
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
          this.$store.commit('setSiv', res.data.data.siv)
          this.$store.commit('setStoken', res.data.data.stoken)
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