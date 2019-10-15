<template lang="html">
    <div>
        <left-menu></left-menu>
        <div class="">
            <el-card class="box-card col-sm-12">
                <div slot="header" class="clearfix">
                    <span class="h4">Коммерческий договор</span>
                    <div style="float: right;">
                        <router-link :to="redirectUrl" class="btn btn-flat btn-sm btn-danger" exact>
                            <i class=""></i> Назад
                        </router-link>
                        <button type="button" class="btn btn-primary btn-sm btn-flat" @click="saveModel()">
                            <i class="fa fa-save"></i> {{ contract.id ? 'Обновить' : 'Сохранить' }}
                        </button>
                    </div>
                </div>
                <div class="text item">
                    <el-tabs v-model="activeName">
                        <el-tab-pane label="Основная информация" name="main">
                            <main-form
                                :contract="contract"
                                :errors="errors"
                            >
                            </main-form>
                        </el-tab-pane>
                        <el-tab-pane label="Доп. соглашения" name="licenses">
                            <licenses-form :contract="contract"></licenses-form>
                        </el-tab-pane>
                        <el-tab-pane label="Приложения" name="applications">
                            <applications-form :contract="contract"></applications-form>
                        </el-tab-pane>
                        <el-tab-pane label="Прочие документы" name="others">
                            <others-form :contract="contract"></others-form>
                        </el-tab-pane>
                    </el-tabs>
                </div>
            </el-card>
        </div>
    </div>
</template>

<script>
import StaticMixin from '../../../mixins/StaticMixin.js';
import Model from './components/model.js';
import LeftMenu from '../../../common-components/LeftMenu.vue';
import MainForm from './components/forms/MainForm.vue';
import LicensesForm from './components/forms/LicensesForm.vue';
import ApplicationsForm from './components/forms/ApplicationsForm.vue';
import OthersForm from './components/forms/OthersForm.vue';

export default {
    data() {
        return {
            actions: {
                restApi: "/api/v1/legal/contracts",
            },
            contract: $.extend(true, {}, Model),
            activeName: 'main',
            errors: [],
            fullscreenLoading: false,
            redirectUrl: '/legal/commercial/',
            context: {
                params: {
                    left: '0',
                    top: '0',
                    position: 'fixed',
                    zIndex: 9999
                },
                show: false
            },
        }
    },
    mixins: [StaticMixin],
    components: {
        LeftMenu,
        MainForm,
        LicensesForm,
        ApplicationsForm,
        OthersForm
    },
    created() {
        if (this.$route.params.hasOwnProperty('id')) {
            this.getModel();
        }
    },
    mounted() {
        if (!this.$route.params.hasOwnProperty('id')) {
            this.$root.$emit('modelLoaded', {contract: false});
        }
    },
    methods: {
        getModel() {
            this.$http.get(this.actions.restApi + '/' + this.$route.params.id, {})
                .then((response) => {
                    this.contract = $.extend(true, {}, Model, response.data);

                    if (this.contract.hasOwnProperty('contracts')) {
                        var self = this;
                        let files = this.contract.contracts;
                        this.contract.contracts = [];
                        if (files !== null) {
                            files.every((el, index, arr) => {
                                self.contract.contracts_list.push({
                                    name: el.name,
                                    url: el.url,
                                    path: el.path,
                                    uid: el.uid
                                });
                                self.contract.contracts.push(el);
                                return true;
                            });
                        }
                    }
                    if (this.contract.hasOwnProperty('licenses')) {
                        var self = this;
                        let files = this.contract.licenses;
                        this.contract.licenses = [];
                        if (files !== null) {
                            files.every((el, index, arr) => {
                                self.contract.licenses_list.push({
                                    name: el.name,
                                    url: el.url,
                                    path: el.path,
                                    uid: el.uid
                                });
                                self.contract.licenses.push(el);
                                return true;
                            });
                        }
                    }
                    if (this.contract.hasOwnProperty('applications')) {
                        var self = this;
                        let files = this.contract.applications;
                        this.contract.applications = [];
                        if (files !== null) {
                            files.every((el, index, arr) => {
                                self.contract.applications_list.push({
                                    name: el.name,
                                    url: el.url,
                                    path: el.path,
                                    uid: el.uid
                                });
                                self.contract.applications.push(el);
                                return true;
                            });
                        }
                    }

                    if (this.contract.hasOwnProperty('others')) {
                        var self = this;
                        let files = this.contract.others;
                        this.contract.others = [];
                        if (files !== null) {
                            files.every((el, index, arr) => {
                                self.contract.others_list.push({
                                    name: el.name,
                                    url: el.url,
                                    path: el.path,
                                    uid: el.uid
                                });
                                self.contract.others.push(el);
                                return true;
                            });
                        }
                    }
                    this.$root.$emit('modelLoaded', {contract: this.contract});
                });
        },
        saveModel() {
            if (this.contract.id) {
                this.$http.put(this.actions.restApi + "/" + this.contract.id, {contract: this.contract})
                    .then((response) => {
                        this.$notify({
                            type: 'success',
                            title: 'Успешно!',
                            message: 'Запись обновлена'
                        });
                        this.$router.push(this.redirectUrl);
                    })
                    .catch((response) => {
                        this.$notify({
                            type: 'error',
                            title: 'Ошибка!',
                            message: response.data.message
                        });
                        this.errors = response.data.errors;
                        this.activeName = 'main';
                    });
            } else {
                this.$http.post(this.actions.restApi, {contract: this.contract})
                    .then((response) => {
                        this.$notify({
                            type: 'success',
                            title: 'Успешно!',
                            message: 'Запись сохранена'
                        });
                        this.$router.push(this.redirectUrl);
                    })
                    .catch((response) => {
                        this.$notify({
                            type: 'error',
                            title: 'Ошибка!',
                            message: response.data.message
                        });
                        this.errors = response.data.errors;
                        this.activeName = 'main';
                    });
            }
        },

        hideContext() {
            this.context.show = false;
        },


    }
}
</script>

<style lang="css">
textarea {
    width: 100%;
}

.box-title {
    padding-left: 2%;
    padding-top: 1%;
    padding-bottom: 1%;
}

.el-date-editor {
    width: 100% !important;
}

.btn-danger {
    background-color: #d32f2f !important;
}

.btn-danger:hover {
    background-color: #c62828 !important;
}

</style>
