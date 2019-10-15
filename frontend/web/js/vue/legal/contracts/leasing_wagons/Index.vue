<template lang="html">
    <div>
        <left-menu></left-menu>
        <div class="col-sm-12">
            <!-- <permission-visible-denied v-if="!this.$router.user.rules.legal.commercial.visible"></permission-visible-denied >
            <permission-editable-denied v-if="!this.$router.user.rules.legal.commercial.editable"></permission-editable-denied> -->
        </div>
        <!-- <div class="" v-if="this.$router.user.rules.legal.commercial.visible"> -->
        <div class="">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <div class="row">
                        <div class="col-sm-8 form-inline">
                            <div class="form-group">
                                <router-link to="/legal/leasing-wagons/create" class="btn btn-primary btn-flat btn-sm"><i class="fa fa-plus"></i> Создать</router-link>
                            </div>

                            <div class="form-group">
                                <el-badge v-if="expiredAmount" :value="expiredAmount" class="button-badge">
                                    <el-checkbox-button v-model="filters.expired" @change="getRecords" class="archive-button">
                                        <span
                                            v-if="filters.expired"
                                            title="Показать только просроченные договоры"
                                            >Все договоры</span>
                                        <span v-if="!filters.expired">Просроченные</span>
                                    </el-checkbox-button>
                                </el-badge>
                                <el-checkbox-button v-else v-model="filters.expired" @change="getRecords" class="archive-button">
                                    <span
                                        v-if="filters.expired"
                                        title="Показать только просроченные договоры"
                                        >Все договоры</span>
                                    <span v-if="!filters.expired">Просроченные</span>
                                </el-checkbox-button>
                            </div>

                            <div class="form-group">
                                <el-checkbox-button v-model="filters.archive" @change="getRecords" class="archive-button">
                                    <span
                                        v-if="filters.archive"
                                        title="Показать только архивные договоры"
                                        >Все договоры</span>
                                    <span v-if="!filters.archive">Архивные</span>
                                </el-checkbox-button>
                            </div>


                            <div class="form-group">
                                <el-button size="small" class="reset-button" v-on:click="resetFilters">Сбросить фильтры</el-button>
                            </div>

                        </div>
                        <div class="col-sm-4">
                            <el-pagination
                                :pager-count="5"
                                background
                                @current-change="changePage"
                                @size-change="handleCurrentChange"
                                :page-sizes="[20, 50, 100, 200]"
                                :page-size="filters.limit"
                                layout="total, sizes, prev, pager, next"
                                :total="totalRecords"
                                :current-page="filters.page"
                                class="pull-right">
                            </el-pagination>
                        </div>
                    </div>
                    <div class="row">

                    </div>
                </div>
                <div class="text item">
                    <contracts-table
                        :contracts="contracts"
                        :filters="filters"
                        :selectedRow="selectedRow"
                        v-on:get-contracts="getRecords"
                        v-on:drop-contract="drop"
                        v-on:archive-contract="archive"
                        v-on:responsible-change-contract="changeResponsible"
                        v-on:annul-contract="annul"
                        v-on:prolongate-contract="prolongate"
                        v-on:selectRow="selectRowId"
                        v-on:sort="sorting"
                    ></contracts-table>
                </div>
            </el-card>
        </div>
    </div>
</template>

<script>
import LeftMenu from "../../../common-components/LeftMenu.vue";
import Filters from './components/filters.js';
import ContractsTable from "./components/Table.vue";
import { Loading } from 'element-ui';

export default {
    data() {
        return {
            actions: {
                rest: '/api/v1/legal/contracts',
                toggleArchive: '/api/v1/legal/contracts/toggle-archive',
                annul: '/api/v1/legal/contracts/annul',
                prolongate: '/api/v1/legal/contracts/prolongate',
                changeResponsible: '/api/v1/legal/contracts/change-responsible',
                userSettings: '/api/v1/users/user-settings',
            },
            contracts: [],
            totalRecords: 0,
            expiredAmount: '',
            selectedRow: -1,
            filters: $.extend(true, {}, Filters),
            redirectUrl: '/legal/leasing-wagons',
            userSettingsKey: 'legal-leasing-wagons-user-settings'
        }
    },
    created() {
        this.getUserSettings();
    },
    components: {
        LeftMenu,
        ContractsTable
    },
    methods: {
        getRecords() {
            this.setUserSettings();
            let params = {
                filters: this.filters
            };
            this.loadingInstance = Loading.service({'text': "Формируем таблицу договоров..."});
            this.$http.get(this.actions.rest, {params: params})
                .then((response) => {
                    this.contracts = response.data.records;
                    this.totalRecords = response.data.totalRecords;
                    this.expiredAmount = response.data.expiredAmount;
                    this.loadingInstance.close();
                }, (response) => {
                    console.log("Error - ", response.data);
                })
        },

        handleCurrentChange: function(target) {
            this.filters.limit = target;
            this.getRecords();
        },
        drop(id) {
            this.$http.delete(this.actions.rest + "/" + id)
                .then((response) => {
                    this.getRecords();
                    this.$notify({
                        type: 'success',
                        title: 'Успешно!',
                        message: response.data.message
                    });
                });
        },
        archive(id) {
            this.$http.get(this.actions.toggleArchive, {params: {id: id}})
                .then((response) => {
                    this.getRecords();
                    this.$notify({
                        type: 'success',
                        title: 'Успешно!',
                        message: response.data.message
                    });
                });
        },
        annul(contract) {
            if (!contract.annul_doc) {
                this.$notify({
                    type: 'error',
                    title: 'Ошибка!',
                    message: 'Необходимо прикрепить соглашение'
                });
                return false;
            }
            this.$http.put(this.actions.annul, {contract: contract})
                .then((response) => {
                    this.getRecords();
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
                });
        },
        changeResponsible(params) {
            this.$http.put(this.actions.changeResponsible, {params})
                .then((response) => {
                    this.getRecords();
                    this.$notify({
                        type: 'success',
                        title: 'Успешно!',
                        message: 'Ответственный изменен успешно'
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
                });
        },
        prolongate(id) {
            this.$http.get(this.actions.prolongate, {params: {id: id}})
                .then((response) => {
                    this.getRecords();
                    this.$notify({
                        type: 'success',
                        title: 'Успешно!',
                        message: response.data.message
                    });
                });
        },
        changePage(page) {
            this.filters.page = page;
            this.getRecords();
        },
        sorting(field) {
            this.filters.sortFields = Object.assign({}, this.filters.sortFields);

            if (this.filters.sortFields.hasOwnProperty(field)) {
                this.filters.sortFields[field] = !this.filters.sortFields[field];
            } else {
                this.filters.sortFields[field] = true;
            }
            this.getRecords();
            return;
        },
        getUserSettings() {
            var params = {
                key: this.userSettingsKey
            }
            this.$http.get(this.actions.userSettings, {params:params})
                .then((response) => {
                    if (response.data != "") {
                        var data = JSON.parse(response.data);
                        this.filters = $.extend(true, this.filters, data.filters);
                        this.selectedRow = data.selectedRow;
                    }
                    this.getRecords();
                })
        },
        selectRowId(id) {
            this.selectedRow = id;
            this.setUserSettings();
        },
        resetFilters() {
            this.filters = JSON.parse(JSON.stringify(Filters));
            this.getRecords();
        },
        setUserSettings() {
            var params = {
                key: this.userSettingsKey,
                value: JSON.stringify({filters: this.filters, selectedRow: this.selectedRow})
            }
            this.$http.post(this.actions.userSettings, {params})
                .then((response) => {});
        },
    }
}
</script>
<style lang="css">
.pps-contracts .el-dialog {
    width: 30%;
}

.pps-contracts .top-buttons {
    display: inline-block;
}

.pps-contracts .el-checkbox-button__inner {
    /*width: 100%;*/
    font-size: 10px;
    border-left: 1px solid #bfcbd9;
}

.pps-contracts .el-checkbox-button {
    /*width: 100%;*/
    margin-left: 5px;
}

.pps-contracts .el-checkbox-button:last-child .el-checkbox-button__inner {
    border-radius: 0px;
}

.search-company {
    font-size: 12px;
    max-height: 32px;
}

.pps-contracts a.btn-flat {
    font-size: 12px;
}

.pps-contracts .el-select-dropdown {
    z-index: 2030 !important;
}

.pps-contracts .files {
    cursor: pointer;
}

.pps-contracts .files:hover {
    color: #3c8dbc;
}
.reset-button {
    padding: 8px 20px!important;
}
.archive-button {
    margin: 0!important;
}
.archive-button .el-checkbox-button__inner {
    padding: 8px 20px!important;
    font-size: 12px!important;

}
.button-badge sup{
    z-index: 100;
    color: white;
    border:none;
    font-size: 10px;
}
</style>
