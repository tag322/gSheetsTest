<template>
    <div id="content" class="d-flex align-items-center justify-content-center vh-100">
        <div class="p-3 border rounded">
            <div class="actions_block mb-1">
                <a @click.prevent="googleAuth" class="btn btn-dark me-1">Google oAuth</a>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios"

export default {
    methods: {
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
                window.location.replace(process.env.VUE_APP_FRONTEND_URL + '/sheets')
            }
        },
    },
    mounted() {
        this.checkUrl()
    }
}
</script>

<style></style>