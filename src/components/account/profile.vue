<template>
  <div class="items">
    <div v-show="message.length" class="alert-success alert alert-dismissible">
      {{ message }}<button type="button" class="btn-close" aria-label="Закрыть" @click="message = ''"></button></div>
    <form @submit.prevent="submitForm">
      <template v-for="(data, field) in fields">
        <div :class="['mb-3', 'field-user-' + field, 'required']">
            <MaskInput v-if="field === 'phone'" :value.sync="fields.phone.value" mask="+7(###)###-##-##"
                       :class="['form-control', {'is-invalid': data.errors && data.errors.length > 0}]"
                       :name="'User[' + field + ']'" :placeholder="data.label"/>
            <input v-else :type="data.type ? data.type : 'text'" :id="'user-' + field"
                   :class="['form-control', {'is-invalid': data.errors && data.errors.length > 0}]"
                   :name="'User[' + field + ']'" :placeholder="data.label" aria-required="true" v-model="data.value">
            <div class="invalid-feedback">{{ data.errors ? data.errors.join() : '' }}</div>
<!--          <div v-else class="form-check">
            <input type="hidden" :name="'User[' + field + ']'" value="0">
            <input type="checkbox" :id="'user-' + field"
                   :class="['form-check-input', {'is-invalid': data.errors && data.errors.length > 0}]"
                   :name="'User[' + field + ']'" value="1" placeholder aria-required="true">
            <label class="form-check-label" :for="'user-' + field">{{ data.label }}</label>
            <div class="invalid-feedback">{{ data.errors ? data.errors.join() : '' }}</div>
          </div>-->
        </div>
      </template>
      <div class="form-group right">
        <div><button type="submit" class="btn btn-primary" name="login-button">Сохранить</button></div>
      </div>

    </form>
    <loader v-show="isLoading"></loader>
  </div>
</template>

<script>
    import helper from '@/helper'
    import modal from '@c/modal.vue'
    import loader from '@c/loader.vue'
    import { MaskInput } from 'vue-3-mask';

    export default {
        props: {
        },

        data() {
            return {
              fields: {
                login: {label: 'Логин', value: '', errors: [], type: 'text'},
                name: {label: 'Имя', value: '', errors: [], type: 'text'},
                email: {label: 'Email', value: '', errors: [], type: 'text'},
                phone: {label: 'Телефон', value: '', errors: [], type: 'text'},
                address: {label: 'Адрес', value: '', errors: [], type: 'text'},
                password: {label: 'Сменить пароль', value: '', errors: [], type: 'password'},
                password_repeat: {label: 'Повтор пароля', value: '', errors: [], type: 'password'},
                // terms: {label: 'Согласие на обработку персональных данных', value: '', errors: [], type: 'checkbox'},
              },
              isLoading: false,
              message: '',
            }
        },
        watch: {

        },
        components: {modal, loader, MaskInput},
        methods: {
          submitForm(e) {
            this.isLoading = true
            this.message = ''
            const formData = new FormData(e.srcElement)
            let params = ''
            for (let p of formData.entries()) {
              params += '&' + encodeURIComponent(p[0]) + '=' + encodeURIComponent(p[1])
            }
            helper.requestPost({url: '/account/profile-save', data: params})
            .then(response => {
              this.isLoading = false
              if (response.data.ok && parseInt(response.data.ok) === 1) {
                this.message = 'Данные сохранены'
                if (this.fields.password.value.length > 0) location.href = '/site/login'
                Object.entries(this.fields).forEach(value => {
                  this.fields[value[0]].errors = []
                })
              } else {
                Object.entries(this.fields).forEach(value => {
                  this.fields[value[0]].errors = []
                  if (typeof response.data.errors['user-' + value[0]] !== "undefined") {
                    this.fields[value[0]].errors = response.data.errors['user-' + value[0]]
                  }
                })
              }
            }).catch(e => console.log(e))
          },
        },
      mounted() {
        helper.requestPost({url: '/account/profile-load', data: ''})
            .then(response => {
              this.isLoading = false
              if (response.data.login && response.data.name) {
                this.fields.login.value = response.data.login
                this.fields.name.value = response.data.name
                this.fields.email.value = response.data.email
                this.fields.phone.value = response.data.phone
                this.fields.address.value = response.data.address
              }
            }).catch(e => console.log(e))
      }
    }
</script>