<template>
  <div class="crud">
    <div v-show="message.length" class="alert-success alert alert-dismissible">
      {{ message }}<button type="button" class="btn-close" aria-label="Закрыть" @click="message = ''"></button></div>

    <ul class="crud-list">
      <li class="d-flex">
        <div class="crud-list-num">#</div>
        <div class="crud-list-name">Наименование</div>
      </li>
      <template v-for="(item, i) in items">
        <li class="d-flex">
          <div class="crud-list-num">{{ i + 1 }}</div>
          <div class="crud-list-name">
            <input type="text" :class="['form-control', {'is-invalid': !item.name}]"
                   name="names[]" placeholder="Имя" aria-required="true" v-model="item.name">
          </div>
          <div class="crud-list-delete">
            <button type="button" class="btn btn-warning" @click="showModalDelete(i)" title="Удалить">
              <span>&times;</span>
            </button>
          </div>
        </li>
      </template>
    </ul>
    <div class="d-flex form-group justify-content-between">
      <div><button type="button" class="btn" @click="addItem">Добавить</button></div>
      <div><button type="button" class="btn btn-primary" @click="saveItems">Сохранить</button></div>
    </div>

    <modal v-model:show="isShowDelete" class="modal-delete" title="Удалить?">
      <div class="d-flex justify-content-center">
        <button type="button" class="btn btn-primary" @click="deleteItem">Да</button>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <button type="button" class="btn" @click="closeModalDelete">Нет</button>
      </div>
      <div class="alert alert-danger" v-show="message" v-html="message"></div>
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
        },
        data() {
            return {
              items: [],
              isLoading: false,
              isShowDelete: false,
              itemId: 0,
              message: '',
              model: ''
            }
        },
        watch: {
          '$route'(to, from) {
            this.message = ''
            this.fetchItems()
          }
        },
        components: {modal, loader},
        methods: {
          fetchItems() {
            this.model = this.$route.params.model
            this.isLoading = true
            helper.requestPost({
              url: '/account/fetch-crud-items',
              data: 'model=' + encodeURIComponent(this.model)
            }).then(response => {
                this.isLoading = false
                this.items = response.data.items
            })
          },
          saveItems() {
            this.message = ''
            this.isLoading = true
            let data = 'model=' + encodeURIComponent(this.model)
            this.items.forEach((item, i) => {
              data += '&ids[]=' + encodeURIComponent(item.id)
              data += '&names[]=' + encodeURIComponent(item.name)
            })

            helper.requestPost({
              url: '/account/save-crud-items',
              data: data
            }).then(response => {
              this.message = response.data.message
              this.isLoading = false
              this.fetchItems()
            })
          },
          addItem() {
            this.items.push({id: 0, name: ''})
          },

          showModalDelete(index) {
            let itemId = this.items[index].id
            console.log(itemId)
            if (itemId) {
              this.isShowDelete = true
              this.itemId = itemId
            } else {
              this.items.splice(index, 1)
            }
          },
          closeModalDelete() {
            this.isShowDelete = false
            this.itemId = 0
          },
          deleteItem() {
            this.message = ''
            this.isLoading = true
            let params = 'model=' + encodeURIComponent(this.model) + '&id=' + this.itemId
            helper.requestPost({url: '/account/delete-crud-item', data: params})
            .then(response => {
              this.isLoading = false
              if (response.data.ok) {
                this.fetchItems()
                this.closeModalDelete()
              }
            })
          },
        },
      mounted() {
        this.fetchItems()
      }
    }
</script>