<template>
  <h1>jinzhiyv抢购系统</h1>
  <button>一键购买</button>
  <button>查看上午的商品</button>
  <button>查看下午的商品</button>
  <button>显示系统信息</button>
  <div>
    <h2>Show</h2>
      <ul v-if="data">
        <li v-for="item in data">
          <img src="{{item.url}}" alt="">
          price: {{item.price}}
          name: {{item.name}}
        </li>
      </ul>
  </div>
</template>

<script setup>



</script>

<style>
</style>


