<template>
    <transition name="fade-fast">
        <div class="modal" v-if="show_" @click.self="close">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="title" class="modal-title" v-html="title"></h5>
                        <button type="button" class="btn close" @click="close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        props: {
            show: {
                type: Boolean,
                default: false
            },
            title: {
                type: String,
                default: ''
            }
        },

        data() {
            return {
                show_: this.show
            }
        },

        watch: {
            show( val ) {
                this.show_ = val;
            },

            show_( val ) {
                this.$emit('update:show', val);
            }
        },

        methods: {
            close() {
                this.show_ = false;
                this.$emit('closemodal');
            }
        },

        mounted() {
            // закрытие по нажатию esc
            document.addEventListener('keyup', e => {
               if( e.keyCode !== 27 || !this.show_ ) return;

               this.close();
            });
        }
    }
</script>
