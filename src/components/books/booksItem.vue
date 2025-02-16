<template>
  <div class="container">
    <h1>Карточка книги</h1>

    <div class="row">
      <div class="col-lg-7">
        <div class="books-params mb-5">
          <div>Название: <span class="books-params-value">{{ book.name }}</span></div>
          <div>Автор: <span class="books-params-value">{{ book.author }}</span></div>
          <div>Год издания: <span class="books-params-value">{{ book.year }}</span></div>
          <div>Жанр: <span class="books-params-value">{{ book.categoryName }}</span></div>
          <div>Никнейм пользователя: <span class="books-params-value">{{ book.userName }}</span></div>
          <div>Краткое описание:</div>
          <div>{{ book.description }}</div>

          <button v-if="user_id && user_id !== book.user_id" class="btn mt-3 mb-3" @click="openForm">Предложить обмен</button>

        </div>
      </div>
      <div class="col-lg-4">
        <div class="book-image mb-5">
          <div class="bold-border">
            <img :src="'/' + book.preview" class="img-fluid">
          </div>
        </div>
      </div>
    </div>

    <book-form v-model:show="isShowForm" :to-book-id="book.id" :to-book-name="book.name + ' ' + book.author"
               :to-user-id="book.user_id" :from-user-id="user_id"></book-form>
    <loader v-show="isLoading"></loader>
  </div>
</template>

<script>
    import helper from '@/helper'
    import BookForm from "./bookForm"
    import loader from '@c/loader.vue'

    export default {
        props: {
        },

        data() {
            return {
              book: {
                id: 0,
                name: '',
                author: '',
                year: '',
                categoryName: '',
                userName: '',
                description: '',
                preview: '',
                user_id: 0, //юзер владелец книги
              },
              user_id: 0,   //текущий юзер (отправитель заявки)
              isLoading: false,
              message: '',
              isShowForm: false,
            }
        },
        watch: {
          '$route'(to, from) {
            this.fetchBook()
          }
        },
        components: {BookForm, loader},
        methods: {
          fetchBook() {
            this.isLoading = true
            helper.requestPost({url: '/site/fetch-book', data: '&id=' + this.$route.params.id})
                .then(response => {
                  this.isLoading = false
                  if (response.data.book) {
                    this.book = response.data.book
                  }
                  this.user_id = response.data.user_id
                }).catch(e => console.log(e))
          },
          openForm() {
            // this.$router.push({ name: 'bookForm', params: { book_id: this.book.id } })
            this.isShowForm = true
          },
        },
      mounted() {
        this.fetchBook()
      }
    }
</script>