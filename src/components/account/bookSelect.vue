<template>
  <modal v-model:show="show_" class="modal-book-form" title="Выбрать книгу для обмена">
    <div class="container">
      <div class="books mb-3">
        <div class="row">
          <template v-for="item in items">
            <div class="col-6 col-md-3 col-xxl-2">
              <div :class="['mb-3', 'w-100', 'book-item', {'book-item--selected': fromBookId === item.id}]" @click.prevent="selectBook(item)">
                <div class="mb-1 w-100 thin-border">
                  <a href="#">
                    <div :style="{ backgroundImage: 'url(\'/' + item.preview + '\')' }" class="book-item-preview"></div>
                  </a>
                </div>
                <div class="d-flex justify-content-center w-100">
                  <a href="#">Выбрать</a>
                </div>
              </div>
            </div>
          </template>
        </div>
        <button class="btn mt-3 w-100" v-if="items.length < countAll" @click="fetchBooks(true)">Показать еще</button>
      </div>
      <div class="d-flex justify-content-end" v-if="fromBookId">
        <button type="button" class="btn btn-primary" @click="saveFromBook">Подтвердить</button>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <button type="button" class="btn btn-warning" @click="closeModal">Отмена</button>
      </div>
    </div>
    <div class="book-form-bg"></div>
  </modal>
  <loader v-show="isLoading"></loader>
</template>

<script>
    import helper from '@/helper'
    import modal from '@c/modal.vue'
    import loader from '@c/loader.vue'

    export default {
      props: {
        show: false,
        bidId: 0,
        fromUserId: 0,
      },
      emits: ['saveFromBook'],

      data() {
          return {
            show_: this.show,
            fromBookId: 0,
            fromBookName: '',
            items: [],
            isLoading: false,
            message: '',
            page: 0,
            countAll: 0,
          }
      },
      watch: {
        show( val ) {
          this.show_ = val
          if (val) this.fetchBooks()
          else this.closeModal()
        },

        show_( val ) {
          this.$emit('update:show', val)
        }
      },
      components: {modal, loader},
      methods: {
        fetchBooks(withAppend = false) {
          this.isLoading = true
          if (withAppend) this.page ++
          else this.page = 0
          helper.requestPost({
            url: '/site/fetch-books',
            data: '&user_id=' + this.fromUserId + ' + &page=' + this.page
          }).then(response => {
                this.isLoading = false
                if (!withAppend) {
                  this.items = response.data.items
                } else {
                  this.items = this.items.concat(response.data.items)
                }
                this.countAll = response.data.countAll
              }).catch(e => console.log(e))
        },
        selectBook(item) {
          this.fromBookId = item.id
          this.fromBookName = item.name + ' ' + item.author
        },
        saveFromBook() {
          this.isLoading = true
          helper.requestPost({
            url: '/account/save-from-book',
            data: '&bid_id=' + this.bidId
                + '&from_book_id=' + this.fromBookId
          }).then(response => {
                this.isLoading = false
                if (response.data.ok && parseInt(response.data.ok) === 1) {
                  this.$emit('saveFromBook', this.fromBookId, this.fromBookName)
                  this.closeModal()
                } else {
                  console.log(response.data)
                }
              }).catch(e => console.log(e))
        },
        closeModal() {
          this.show_ = false
          this.fromBookId = 0
        },
      },
      mounted() {

      }
    }
</script>