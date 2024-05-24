<template>
    <div class="container">
        <section class="home">
            <Button>
                <router-link to="/apply/about" class="home-link">
                    Перейти до форми
                </router-link>
            </Button>
        </section>
    </div>
</template>
<script>
import Button from 'primevue/button';
export default {
    components: {Button},
}
</script>

<style scoped>
    .home{
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .home-link{
        font-size: 20px;
        color: #fff;
        padding: 20px 0;
    }
</style>