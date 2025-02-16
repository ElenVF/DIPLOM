<template>
  <modal v-model:show="show_" class="modal-book-form" title="ОФОРМЛЕНИЕ ОБМЕНА">
    <div class="container">
      <div v-if="!isCompleted">
        <div class="d-lg-flex mb-3">
          <div class="col-12 col-lg-6">
            <div class="delivery-values">
              <div>Выбор доставки</div>
              <template v-for="deliveryItem in deliveries">
                <div :class="['delivery-value', {'delivery-value--active': delivery === deliveryItem.id}]" @click="delivery = deliveryItem.id">
                  <span></span> {{ deliveryItem.name }}
                </div>
              </template>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <div class="delivery-values delivery-values--right">
              <div class="mb-3">{{ toBookName }}</div>
              <div class="delivery-values-rules">
                После выбора книги, пожалуйста, укажите предпочтительный способ доставки. Затем ожидайте ответа пользователя в течение нескольких дней. Пользователь выберет одну из ваших книг для обмена и свяжется со вами по электронной почте или по телефону для уточнения деталей .
                Счастливого обмена!)
              </div>
            </div>
          </div>
        </div>

        <div class="alert alert-danger mt-4" v-show="message" v-html="message"></div>
        <div class="d-flex justify-content-between justify-content-lg-end" v-if="delivery">
          <button type="button" class="btn btn-primary" @click="submitForm">Оформить заявку</button>
          &nbsp;&nbsp;
          <button type="button" class="btn" @click="closeModal">Отмена</button>
        </div>
      </div>
      <div v-else class="text-center">
        <div v-html="message" class="mb-3"></div>
        <button type="button" class="btn mb-3" @click="closeModal">OK</button>
      </div>
    </div>
    <div class="book-form-bg"></div>
    <loader v-show="isLoading"></loader>
  </modal>
</template>

<script>
    import helper from '@/helper'
    import modal from '@c/modal.vue'
    import loader from '@c/loader.vue'

    export default {
      props: {
        show: false,
        toBookId: 0,
        toBookName: '',
        toUserId: 0,   //юзер владелец книги
      },

      data() {
          return {
            show_: this.show,
            isLoading: false,
            message: '',
            page: 0,
            countAll: 0,
            delivery: 0,
            isCompleted: false,
            deliveries: [],
          }
      },
      watch: {
        show( val ) {
          this.show_ = val
          if (!val) this.closeModal()
          else this.fetchItems()
        },

        show_( val ) {
          this.$emit('update:show', val);
        }
      },
      components: {modal, loader},
      methods: {
        submitForm() {
          this.isLoading = true
          this.isCompleted = false
          this.message = ''
          let params = '&to_book_id=' + this.toBookId
              + '&to_user_id=' + this.toUserId
              + '&delivery=' + this.delivery

          helper.requestPost({url: '/site/bid-submit', data: params})
              .then(response => {
                this.isLoading = false
                if (response.data.ok && parseInt(response.data.ok) === 1) {
                  this.message = 'ЗАЯВКА УСПЕШНО ОФОРМЛЕНА.<br>ОЖИДАЙТЕ ПОДТВЕРЖДЕНИЕ В ТЕЧЕНИЕ 24 ЧАСОВ'
                  this.isCompleted = true
                } else {
                  this.message = response.data.errors.join('<br>')
                }
              }).catch(e => console.log(e))
        },
        fetchItems() {
          this.isLoading = true
          helper.requestPost({
            url: '/site/fetch-deliveries',
            data: 'model=delivery&to_user_id=' + this.toUserId
          }).then(response => {
            this.isLoading = false
            this.deliveries = response.data.items
          })
        },
        closeModal() {
          this.show_ = false
          this.delivery = 0
          this.message = ''
        },
      },
      mounted() {
        // this.fetchItems()
      }
    }
</script>