<template lang="html">
    <div>
        <left-menu></left-menu>
        <div class="">
            <el-card class="box-card col-sm-12">
                <div slot="header" class="clearfix">
                        <span class="h4">Доп. соглашение к договору {{this.$route.query.contractName}}</span>
                    <div style="float: right;">
                        <button type="button" class="btn btn-flat btn-sm btn-danger" @click="back">
                            Назад
                        </button>
                        <button type="button" class="btn btn-primary btn-sm btn-flat" @click="saveModel()">
                            <i class="fa fa-save"></i> {{ license.id ? 'Обновить' : 'Сохранить' }}
                        </button>
                    </div>
                </div>
                <div class="text item">
                    <div class="row">
                        <div class="col-sm-6">
                            <h4>Основная информация</h4>
                            <main-form
                                :license="license"
                                :folder="this.$route.query.folder"
                                :errors="errors"
                            >
                            </main-form>
                        </div>
                        <div class="col-sm-6">
                            <h4>Прикрепленные вагоны</h4>
                            <wagons-form :license="license"></wagons-form>
                        </div>
                    </div>
                </div>
            </el-card>
        </div>
    </div>
</template>

<script>
import LeftMenu from '../../../common-components/LeftMenu.vue';
import Model from './components/model.js';
import MainForm from './components/forms/MainForm.vue';
import WagonsForm from './components/forms/WagonsForm.vue';
export default {
    data() {
        return {
            actions: {
                restApi: "/api/v1/legal/licenses",
            },
            license: $.extend(true, {}, Model),
            errors: [],
            activeName: 'main'
        }
    },
    components: {
        LeftMenu,
        MainForm,
        WagonsForm
    },
    created() {
        if (this.$route.params.hasOwnProperty('id')) {
            this.getModel();
        }
    },
    methods: {
        getModel() {
            this.$http.get(this.actions.restApi + '/' + this.$route.params.id, {})
                .then((response) => {
                    this.license = $.extend(true, {}, Model, response.data);
                    if (response.data.hasOwnProperty('wagons')) {
                        this.license.wagons = [];
                        var self = this;
                        response.data.wagons.every(function(el) {
                            self.license.wagons.push({
                                id: el.id,
                                number: el.number,
                                date_start: el.date_start,
                                date_finish: el.date_finish,
                                price: el.price,
                            });
                            return true;
                        });
                    }
                    if (this.license.hasOwnProperty('license')) {
                        var self = this;
                        let files = this.license.license;
                        this.license.license = [];
                        if (files !== null) {
                            files.every((el, index, arr) => {
                                self.license.license_list.push({
                                    name: el.name,
                                    url: el.url,
                                    path: el.path,
                                    uid: el.uid
                                });
                                self.license.license.push(el);
                                return true;
                            });
                        }
                    }
                });
        },
        saveModel() {
            this.license.contract_id = this.$route.query.contractId;
            if (this.license.id) {
                this.$http.put(this.actions.restApi + "/" + this.license.id, {license: this.license})
                    .then((response) => {
                        this.$notify({
                            type: 'success',
                            title: 'Успешно!',
                            message: 'Запись обновлена'
                        });
                        this.$router.go(-1);
                    })
                    .catch((response) => {
                        this.$notify({
                            type: 'error',
                            title: 'Ошибка!',
                            message: response.data.message
                        });
                        this.errors = response.data.errors;
                    });
            } else {
                this.$http.post(this.actions.restApi, {license: this.license})
                    .then((response) => {
                        this.$notify({
                            type: 'success',
                            title: 'Успешно!',
                            message: 'Запись сохранена'
                        });
                        this.$router.go(-1);
                    })
                    .catch((response) => {
                        this.$notify({
                            type: 'error',
                            title: 'Ошибка!',
                            message: response.data.message
                        });
                        this.errors = response.data.errors;
                    });
            }
        },
        back() {
            this.$router.go(-1);
        }
    }
}
</script>

<style lang="css">
.el-date-editor {
    width: 100% !important;
}
</style>
