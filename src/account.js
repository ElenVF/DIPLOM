import { createApp } from 'vue'
import { createRouter, createWebHashHistory } from 'vue-router'

import books from "./components/account/books.vue"
import bids from "./components/account/bids.vue"
import events from "./components/account/events.vue"
import profile from "./components/account/profile.vue"
import crud from "./components/account/crud.vue"
import helper from "./helper";
// import siteBooks from "./components/books/booksList.vue"

const router = createRouter({
    history: createWebHashHistory('/account'),
    routes: [
        { path: '/', name: 'books', component: books },
        { path: '/books-all', name: 'books-all', component: books, props: { all: 1}},
        { path: '/profile', name: 'profile', component: profile },
        { path: '/bids', name: 'bids', component: bids },
        { path: '/crud/:model', name: 'crud', component: crud },
        { path: '/events', name: 'events', component: events },
    ]
})

const app = createApp({
    // el: '#app',
    data() {
        return {
            items: [],
            isLoading: false,
            bookCounter: 0,
            bidCounter: 0,
        }
    },
    watch: {},
    components: {},
    methods: {
        getCounters() {
            this.isLoading = true
            helper.requestPost({url: '/account/get-counters'})
                .then(response => {
                    this.isLoading = false
                    if (response.data.bookCounter) {
                        this.bookCounter = response.data.bookCounter
                    }
                    if (response.data.bidCounter) {
                        this.bidCounter = response.data.bidCounter
                    }
                })
        },
    },
    mounted() {
        this.getCounters()
    }
})

app.use(router)
app.mount('#main')