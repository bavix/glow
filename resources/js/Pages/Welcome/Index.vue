<template>
    <layout title="Welcome">
        <h1>Welcome, {{ user.name }}</h1>
        <p>Glow CDN</p>
    </layout>
</template>

<script>
import Layout from '../../Layouts/DefaultLayout'

export default {
    components: {
        Layout,
    },
    props: {
        user: Object,
    },
}
</script>
