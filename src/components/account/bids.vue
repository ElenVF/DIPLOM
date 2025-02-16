<template>
  <div class="bids">
    <div class="d-flex justify-content-between mb-4">
      <button :class="['bids-filter-tab', {'bids-filter-tab--active': from === 0}]" @click="fetchItemsFrom(0)">Входящие</button>
      <button :class="['bids-filter-tab', {'bids-filter-tab--active': from === 1}]" @click="fetchItemsFrom(1)">Исходящие</button>
    </div>
    <template v-if="!isShowItem">
      <div class="bids-wrap">
        <table>
          <tr>
            <th>Дата</th>
            <th>Книга</th>
            <th class="only-desktop-td">Книга для обмена</th>
            <th>Отправитель заявки</th>
            <th class="only-desktop-td">Получатель заявки</th>
            <th>Статус</th>
          </tr>
          <template v-for="item in items">
            <tr @click="openItem(item)">
              <td>{{ item.createdFormat }}</td>
              <td>{{ item.toBookName }}</td>
              <td class="only-desktop-td">{{ item.fromBookName }}</td>
              <td>{{ item.fromUserName }}</td>
              <td class="only-desktop-td">{{ item.toUserName }}</td>
              <td :class="{'td-red': item.status_id === 3, 'td-yellow': item.status_id === 1}">{{ item.statusName }}</td>
            </tr>
          </template>
        </table>
      </div>
      <button class="btn mt-3 w-100" v-if="items.length < countAll" @click="fetchItems(true)">Показать еще</button>
    </template>
    <template v-else>
      <ul class="bids-item-data">
        <li>Заявка от {{ bid.createdFormat }}</li>
        <li>Книга: {{ bid.toBookName }}</li>
        <li>
          Книга для обмена: {{ bid.fromBookName }}<br>
          <button v-if="from === 0 && bid.status_id === 1 && !bid.from_book_id" class="btn mt-3"
                  @click="isShowBookSelect = true">Выбрать книгу</button>
          <book-select v-if="from === 0 && bid.status_id === 1" v-model:show="isShowBookSelect" :bid-id="bid.id"
                       :from-user-id="bid.from_user_id" @save-from-book="setFromBook"></book-select>
        </li>
        <li>От кого: {{ bid.fromUserName }}</li>
        <li>Контакты: {{ bid.fromUserData }}</li>
        <li>Способ доставки: {{ bid.deliveryName }}</li>
        <li>Статус заявки: {{ bid.statusName }}</li>
      </ul>
      <div v-if="from === 0 && bid.status_id === 1" class="mt-5 mb-5">
        <button type="button" class="btn btn-primary" @click="completeBid">Принять заявку</button>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <button type="button" class="btn btn-warning" @click="rejectBid">Отклонить заявку</button>
      </div>
    </template>

    <modal v-model:show="isShowChange" class="modal-change" :title="(changeStatus === 'complete' ? 'Подтвердить' : 'Отклонить') + ' заявку'">
      <div class="d-flex justify-content-center">
        <button type="button" class="btn btn-primary" @click="changeBidStatus">Да</button>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <button type="button" class="btn" @click="closeModalChange">Нет</button>
      </div>
      <div class="alert alert-danger mt-4" v-show="message" v-html="message"></div>
    </modal>
    <loader v-show="isLoading"></loader>
  </div>
</template>

<script>
    import helper from '@/helper'
    import modal from '@c/modal.vue'
    import bookSelect from "./bookSelect.vue";
    import loader from '@c/loader.vue'

    export default {
        props: {
        },

        data() {
            return {
              from: 0,
              items: [],
              isLoading: false,
              page: 0,
              countAll: 0,
              bid: {},
              isShowItem: false,
              isShowBookSelect: false,
              isShowChange: false,
              changeStatus: '',
              message: '',
            }
        },
        emits: ['getCounters'],
        watch: {
          '$route'(to, from) {
            this.fetchItemsFrom(0)
          }
        },
        components: {modal, loader, bookSelect},
        methods: {
          fetchItemsFrom(from) {
            this.from = from
            this.isShowItem = false
            this.fetchItems()
          },
          fetchItems(withAppend = false) {
            this.isLoading = true
            if (withAppend) {
              this.page ++
            } else {
              this.page = 0
            }

            helper.requestPost({
              url: '/account/fetch-bids',
              data: 'page=' + this.page + '&from=' + this.from
            }).then(response => {
                  this.isLoading = false
                  if (!withAppend) {
                    this.items = response.data.items
                  } else {
                    this.items = this.items.concat(response.data.items)
                  }
                  this.countAll = response.data.countAll
                });
          },

          completeBid() {
            this.showModalChange('complete')
          },
          rejectBid() {
            this.showModalChange('reject')
          },
          showModalChange(status) {
            this.isShowChange = true
            this.changeStatus = status
            this.message = ''
          },
          closeModalChange() {
            this.isShowChange = false
            this.changeStatus = ''
          },
          changeBidStatus() {
            this.isLoading = true
            helper.requestPost({url: '/account/change-bid-status',
              data: '&status=' + encodeURIComponent(this.changeStatus) + '&bid_id=' + this.bid.id})
            .then(response => {
              this.isLoading = false
              if (response.data.ok) {
                this.fetchItemsFrom(0)
                this.closeModalChange()
                this.$emit('getCounters')
              } else {
                this.message = response.data.message
              }
            })
          },
          openItem(bid) {
            this.isShowItem = true
            this.bid = bid
            // this.$router.push({ name: 'bidsItem', params: { id: id } })
          },
          setFromBook(fromBookId, fromBookName) {
            this.bid.from_book_id = fromBookId
            this.bid.fromBookName = fromBookName
          }
        },
      mounted() {
        this.fetchItemsFrom(0)
      }
    }
</script>