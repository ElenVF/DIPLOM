<template>
  <div class="books">
    <div class="row">
      <template v-for="item in items">
        <div class="col-6 col-md-4 col-lg-6 col-xxl-3">
          <div :class="['mb-3', 'w-100', 'book-item', {'book-item--for-approve': item.status === 0 }]">
            <div class="mb-1 w-100 thin-border">
              <a :href="'/book/update?id=' + item.id">
                <div :style="{ backgroundImage: 'url(\'/' + item.preview + '\')' }" class="book-item-preview"></div>
                <div v-if="item.status === 0" class="book-item-approve-hint">требует проверки</div>
                <div class="book-item-data">
                  <div class="book-item-data-name">{{ item.name }}</div>
                  <div class="book-item-data-author">{{ item.author }}</div>
                  <div class="book-item-data-year">{{ item.year }}</div>
                </div>
              </a>
            </div>
            <div class="d-flex justify-content-between w-100">
              <a href="#" @click.prevent="showModalDelete(item)">Удалить</a>
              <a :href="'/book/update?id=' + item.id">Изменить</a>
            </div>
          </div>
        </div>
      </template>
    </div>
    <button class="btn mt-3 w-100" v-if="items.length < countAll" @click="fetchBooks(true)">Показать еще</button>

    <modal v-model:show="isShowDelete" class="modal-delete" :title="'Удалить книгу:<br>' + book.name + ' ' + book.author + '?'">
      <div class="d-flex justify-content-center">
        <button type="button" class="btn btn-primary" @click="deleteItem">Да</button>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <button type="button" class="btn" @click="closeModalDelete">Нет</button>
      </div>
      <div class="alert alert-danger" v-show="errorMessage" v-html="errorMessage"></div>
    </modal>
    <loader v-show="isLoading"></loader>
  </div>
</template>

<script>
    import helper from '@/helper'
    import modal from '@c/modal.vue'
    import loader from '@c/loader.vue'

    export default {
        props: {
          all: {
            type: Number,
            default: 0
          }
        },

        data() {
            return {
              errorMessage: '',
              items: [],
              isShowDelete: false,
              isShowBook: false,
              book: {},
              isLoading: false,
              page: 0,
              countAll: 0,
            }
        },
        watch: {
          '$route'(to, from) {
            this.fetchBooks()
          }
        },
        components: {modal, loader},
        methods: {
          fetchBooks(withAppend = false) {
            console.log(this.all)
            this.isLoading = true
            if (withAppend) {
              this.page ++
            } else {
              this.page = 0
            }
            helper.requestPost({url: '/account/fetch-books',
              data: 'page=' + this.page + '&all=' + this.all})
                .then(response => {
                  this.isLoading = false
                  if (!withAppend) {
                    this.items = response.data.items
                  } else {
                    this.items = this.items.concat(response.data.items)
                  }
                  this.countAll = response.data.countAll
                }).catch(e => console.log(e))
          },

          showModalDelete(book) {
            this.isShowDelete = true
            this.book = book
          },
          closeModalDelete() {
            this.isShowDelete = false
            this.book = {}
          },
          deleteItem() {
            this.isLoading = true
            let params = '&id=' + this.book.id
            helper.requestPost({url: '/account/delete-book', data: params})
            .then(response => {
              this.isLoading = false
              if (response.data.ok) {
                this.fetchBooks()
                this.closeModalDelete()
              }
            })
          },
        },
      mounted() {
        this.fetchBooks()
      }
    }
</script>