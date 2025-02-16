<template>
  <div class="events">
    <template v-if="!isShowItem">
      <div class="mb-3"><button type="button" class="btn btn-primary" @click="addItem">Создать</button></div>
      <table>
        <tr>
          <th>Дата начала</th>
          <th>Дата конца</th>
          <th>Заголовок</th>
          <th></th>
        </tr>
        <template v-for="(item, i) in items">
          <tr @click="openItem(item)">
            <td>{{ item.dateStartFormat }}</td>
            <td>{{ item.dateEndFormat }}</td>
            <td>{{ item.name }}</td>
            <td>
              <button type="button" class="btn btn-warning btn-delete" @click.stop="showModalDelete(i)" title="Удалить">
                <span>&times;</span>
              </button>
            </td>
          </tr>
        </template>
      </table>
      <button class="btn mt-3 w-100" v-if="items.length < countAll" @click="fetchItems(true)">Показать еще</button>
    </template>
    <template v-else>
      <ul class="events-item-data">
        <li>
          <input type="text" class="form-control" name="name" placeholder="Заголовок" aria-required="true" v-model="item.name">
          <div class="invalid-feedback" v-show="errors['event-name']">{{ errors["event-name"] ? errors["event-name"].join('') : '' }}</div>
        </li>
        <li>
          <VueDatePicker v-model="item.date_start" placeholder="Дата начала"
                         text-input format="dd.MM.yyyy HH:mm" month-name-format="long" locale="ru"></VueDatePicker>
<!--          <input type="text" :class="['form-control', {'is-invalid': !item.date_start}]"
                 name="date_start" placeholder="Дата начала" aria-required="true" v-model="item.date_start">-->
          <div class="invalid-feedback" v-show="errors['event-date_start']">{{ errors["event-date_start"] ? errors["event-date_start"].join('') : '' }}</div>
        </li>
        <li>
          <VueDatePicker v-model="item.date_end" placeholder="Дата конца" text-input format="dd.MM.yyyy HH:mm"
                         month-name-format="long" locale="ru"></VueDatePicker>
<!--          <input type="text" :class="['form-control', {'is-invalid': !item.date_end}]"
                 name="date_end" placeholder="Дата конца" aria-required="true" v-model="item.date_end">-->
          <div class="invalid-feedback" v-show="errors['event-date_end']">{{ errors["event-date_end"] ? errors["event-date_end"].join('') : '' }}</div>
        </li>
        <li>
          <textarea class="form-control" name="description" placeholder="Описание" v-model="item.description"></textarea>
          <div class="invalid-feedback">{{ errors["event-description"] ? errors["event-description"].join('') : '' }}</div>
        </li>
        <li>
          <input type="text" class="form-control" name="address" placeholder="Адрес" v-model="item.address">
          <div class="invalid-feedback">{{ errors["event-address"] ? errors["event-address"].join('') : '' }}</div>
        </li>
      </ul>
      <div class="d-flex form-group justify-content-between mt-5 mb-5">
        <div><button type="button" class="btn btn-warning" @click="fetchItems(false)">Назад</button></div>
        <div><button type="button" class="btn btn-primary" @click="saveItem">Сохранить</button></div>
      </div>
    </template>

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
    // import Datepicker from 'vue3-datepicker'
    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css'

    export default {
        props: {
        },

        data() {
            return {
              items: [],
              isLoading: false,
              page: 0,
              countAll: 0,
              item: {},
              isShowItem: false,
              isShowDelete: false,
              itemId: 0,
              message: '',
              errors: {
                'event-name' : [],
                'event-date_start' : [],
                'event-date_end' : [],
                'event-description' : [],
                'event-address' : [],
              }
            }
        },
        watch: {
          '$route'(to, from) {
            this.fetchItems()
          }
        },
        components: {modal, loader, VueDatePicker},
        methods: {
          fetchItems(withAppend = false) {
            this.isLoading = true
            this.errors = {}
            this.isShowItem = false
            if (withAppend) {
              this.page ++
            } else {
              this.page = 0
            }
            helper.requestPost({
              url: '/account/fetch-events',
              data: 'page=' + this.page
            }).then(response => {
                  this.isLoading = false
                  if (!withAppend) {
                    this.items = response.data.items
                  } else {
                    this.items = this.items.concat(response.data.items)
                  }
                  this.countAll = response.data.countAll
                })
          },
          saveItem() {
            this.message = ''
            this.isLoading = true
            let data = 'id=' + encodeURIComponent(this.item.id)
            data += '&name=' + encodeURIComponent(this.item.name)
            data += '&date_start=' + helper.dateFormatMySQL(this.item.date_start)
            data += '&date_end=' + helper.dateFormatMySQL(this.item.date_end)
            data += '&description=' + encodeURIComponent(this.item.description)
            data += '&address=' + encodeURIComponent(this.item.address)

            helper.requestPost({
              url: '/account/save-event',
              data: data
            }).then(response => {
              this.isLoading = false
              if (response.data.ok === 1) {
                this.message = response.data.message
                this.fetchItems()
              } else {
                this.errors = response.data.errors
                // console.log(response.data)
              }
            })
          },
          showModalDelete(index) {
            this.isShowDelete = true
            this.itemId = this.items[index].id
          },
          closeModalDelete() {
            this.isShowDelete = false
            this.itemId = 0
          },
          deleteItem() {
            this.message = ''
            this.isLoading = true
            helper.requestPost({url: '/account/delete-event', data: 'id=' + this.itemId})
                .then(response => {
                  this.isLoading = false
                  if (response.data.ok) {
                    this.fetchItems()
                    this.closeModalDelete()
                  }
                })
          },
          openItem(item) {
            this.isShowItem = true
            this.item = item
          },
          addItem() {
            this.item = {
              id: 0,
              name: '',
              date_start: '',
              date_end: '',
              description: '',
              address: '',
            }
            this.isShowItem = true
          },
        },
      mounted() {
        this.fetchItems()
      }
    }
</script>