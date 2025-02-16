import { createApp } from 'vue'
import { createRouter, createWebHashHistory } from 'vue-router'

import bookForm from "./components/books/bookForm.vue"
import booksItem from "./components/books/booksItem.vue"
import booksList from "./components/books/booksList.vue"

const router = createRouter({
    history: createWebHashHistory('/books/'),
    routes: [
        { path: '/', name: 'booksList', component: booksList },
        { path: '/:id', name: 'booksItem', component: booksItem },
        // { path: '/order', name: 'bookForm', component: bookForm }
    ]
});

const app = createApp({
    // el: '#app',
    data() {
        return {
            items: [],
            isLoading: false,
            page: 0,
            showBlock: 0,
        }
    },
    watch: {},
    components: {bookForm},
    methods: {

    },
    mounted() {

    }
})

app.use(router)
app.mount('#main');