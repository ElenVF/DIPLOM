<template>
  <div class="container">
    <h1>ДОСТУПНЫЕ КНИГИ</h1>
    <div class="items mb-5">
      <div class="row">
        <div class="col-lg-2">
          <div class="books-params">
            <div class="books-params-head">Сортировать по</div>
            <ul class="books-params-data">
              <li><a href="#" @click.prevent="setOrderBy('author')" :class="{active: orderBy === 'author'}">- Автору</a></li>
              <li><a href="#" @click.prevent="setOrderBy('category.name')" :class="{active: orderBy === 'category.name'}">- Жанру</a></li>
              <li><a href="#" @click.prevent="setOrderBy('year')" :class="{active: orderBy === 'year'}">- Году издания</a></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-10">
          <div class="items">
            <div class="row">
              <template v-for="item in items">
                <div class="col-6 col-md-3 col-xxl-2">
                  <div class="mb-5 w-100 book-item">
                    <div class="w-100 thin-border">
                      <a href="#" @click.prevent="openItem(item.id)">
                        <div :style="{ backgroundImage: 'url(\'/' + item.preview + '\')' }" class="book-item-preview"></div>
                        <div class="book-item-data">
                          <div class="book-item-data-name">{{ item.name }}</div>
                          <div class="book-item-data-author">{{ item.author }}</div>
                          <div class="book-item-data-year">{{ item.year }}</div>
                        </div>
                      </a>
                    </div>
                  </div>
                </div>
              </template>
            </div>
            <button class="btn mt-3 w-100" v-if="items.length < countAll" @click="fetchBooks(true)">Показать еще</button>
          </div>
        </div>
      </div>
    </div>
    <loader v-show="isLoading"></loader>
  </div>
</template>

<script>
    import helper from '@/helper'
    import loader from '@c/loader.vue'

    export default {
        props: {
        },

        data() {
            return {
              errorMessage: '',
              items: [],
              isLoading: false,
              page: 0,
              orderBy: 'author',
              countAll: 0,
            }
        },
        watch: {

        },
        components: {loader},
        methods: {
          fetchBooks(withAppend = false) {
            this.isLoading = true
            if (withAppend) {
              this.page ++
            } else {
              this.page = 0
            }

            helper.requestPost({url: '/site/fetch-books',
              data: 'page=' + this.page + '&orderBy=' + encodeURIComponent(this.orderBy)})
                .then(response => {
                  this.isLoading = false
                  if (!withAppend) {
                    this.items = response.data.items
                  } else {
                    this.items = this.items.concat(response.data.items)
                  }
                  this.countAll = response.data.countAll
                });
          },
          setOrderBy(orderBy) {
            this.orderBy = orderBy
            this.page = 0
            this.fetchBooks()
          },
          openItem(id) {
            this.$router.push({ name: 'booksItem', params: { id: id } })
          }
        },
      mounted() {
        this.fetchBooks()
      }
    }
</script>