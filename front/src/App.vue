<template>
    <div id="loading_placeholder" class="d-flex align-items-center justify-content-center vh-100" v-if="isLoading"><loader-circle></loader-circle></div>
    <router-view v-else></router-view>
</template>

<script>
import axios from "axios"
import LoaderCircle from "./components/anims/LoaderCircle.vue"

export default {
    name: 'App',
    data() {
        return {
            isLoading: false
        }
    },
    components: {
        LoaderCircle
    },
    methods: {
        async getCSRF() {
            await axios.get(`${process.env.VUE_APP_BACKEND_URL}/sanctum/csrf-cookie`, {
                withCredentials: true
            })
        },
        async googleAuth() {
            const response = await axios.get(`${process.env.VUE_APP_BACKEND_URL}/google/oauth`, {}, {
                headers: {

                },
            })
            window.location.replace(response.data)
        },
        checkUrl() {
            const url = new URL(window.location.href)
            const params = new URLSearchParams(url.hash.substring(1))
            const token = params.get('access_token')

            if (token) {
                document.cookie = `access_token=${token}; path=/; max-age=3600; Secure; SameSite=lax`
            }
        },


    },
    async mounted() {
        this.isLoading = true
        await this.getCSRF()
        await this.checkUrl()
        // await new Promise(resolve => setTimeout(resolve, 5000));
        this.isLoading = false
    }
}
</script>

<style>
.table {
    margin-bottom: 0 !important;
}

/* @import '~bootstrap/dist/css/bootstrap.css' */
</style>
