<template>
  <h1>jinzhiyv抢购系统</h1>
  <button>一键购买</button>
  <button @click="checkGoods('am')">查看上午的商品</button>
  <button @click="checkGoods('pm')">查看下午的商品</button>
  <button @click="showSystem">显示系统信息</button>
  <button @click="signOut">退出系统</button>
  <div>
    <h2>Info</h2>
    <p>{{ siv }}</p>
    <p>{{ stoken }}</p>
  </div>
  <div>
    <h2>Show</h2>
    <ul v-if="goods">
      <li v-for="item in goods" :key="item.id">
        <img :src="item.cover_img" alt />
        <p>id: {{ item.id }}</p>
        <p>price: {{ item.price }}</p>
        <p>name: {{ item.name }}</p>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      siv: this.$store.state.siv,
      stoken: this.$store.state.stoken,
      goods: [],
      sys: null,
    }
  },
  methods: {
    checkGoods(time) {
      if (time == 'am') {
        this.axios.get("/api/api/am").then(res => {
          if (res.data.code == 1) {
            this.goods = res.data.data
          } else alert(res.data.msg)
        })
      } else {
        this.axios.get("/api/api/pm").then(res => {
          if (res.data.code == 1) {
            this.goods = res.data.data
          } else alert(res.data.msg)
        })
      }
    },
    signOut() {
      this.$store.commit('setSiv', null)
      this.$store.commit('setStoken', null)
      localStorage.removeItem('siv')
      localStorage.removeItem('stoken')
      this.$router.push("/login");
      this.axios.get("/api/singout").then(res => {
        console.log(res)
        if (res.data.code == 1) {
          this.$store.commit('setSiv', null)
          this.$store.commit('setStoken', null)
          this.$router.push("/login");
        }
      })
    },
    showSystem(){
      this.axios.get("/api/api/system").then(res => {
        console.log(res)
        if (res.data.code == 1) {
          this.goods = res.data.data
        } else alert(res.data.msg)
      })
    }
  }
}


</script>

<style>
</style>


