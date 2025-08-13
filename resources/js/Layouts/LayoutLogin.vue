
<script>
import HelloWorld from '@/Pages/HelloWorldLogin.vue'; 
import { route } from '../../../vendor/tightenco/ziggy/src/js';

export default {
  name: 'Layout',
  components: {
    HelloWorld
  }
};
</script>

<template>
  
  <div>
    <header class="bg-gray-800 text-white p-4 text-center relative">
    
          
        <HelloWorld msg="DTN 32" />
        
    </header>

    <main class="p-4">
      <slot/>
    </main>

    <footer class="bg-gray-800 text-white p-4 text-center">
      <p>&copy; 2018 DTN</p>
    </footer>
  </div>
</template>
