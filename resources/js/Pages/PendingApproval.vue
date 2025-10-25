<template>
  <div class="flex justify-center items-center min-h-screen bg-gray-50">
    <div
      class="max-w-md w-full bg-white text-center rounded-2xl shadow-lg p-8 animate-fade-in"
    >
      <h1 class="text-2xl font-semibold text-[#ff9800] mb-4">
        Votre compte est en attente d’approbation
      </h1>

      <p class="text-gray-600 mb-2">
        Veuillez patienter jusqu’à ce qu’un administrateur approuve votre compte.
      </p>

      <p class="text-gray-600 mb-4">Merci de votre patience.</p>

      <p class="text-gray-500">
        Besoin d’aide ? Appelez-nous :
        <a
          href="tel:3201"
          class="text-[#ff9800] font-semibold hover:underline transition-colors"
        >
          3201
        </a>
        /
        <a
          href="tel:3360"
          class="text-[#ff9800] font-semibold hover:underline transition-colors"
        >
          3360
        </a>
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: "PendingApproval",
};
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.8s ease-in-out;
}
</style>
