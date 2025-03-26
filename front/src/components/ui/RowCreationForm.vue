<template>
    <div class="modal fade" id="modalWindow" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Добавление новой строки</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="input-block" v-for="(field, fieldIndex) in fields" :key="field">
                        <label for="basic-url" class="form-label">Введите {{ field }}</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" @input="inputUpdate($event.target.value, fieldIndex)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" @click="test">++</button>

                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Отменить</button>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal" @click.prevent="submit">Добавить</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        fields: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            newRow: []
        }
    },
    methods: {
        test() {
            if(typeof this.newRow[0] != 'number') {
                this.newRow[0] = 0
            } else {
                this.newRow[0] ++
            }
        },
        inputUpdate(newValue, fieldIndex) {
            this.newRow[fieldIndex] = newValue
        },
        submit() {
            for(let i = 0; i < this.fields.length; i++) {
                if(this.newRow[i] === undefined) {
                    this.newRow[i] = ''
                }
            }
            
            this.$emit('createNewRow', this.newRow)
        }
    }
}
</script>

<style></style>