<template lang="html">
    <div>
        <table class="table text-center contracts-table" @click="hideContext()">
            <thead>
                <tr class="input-row">
                    <td class="checked-amount text-muted">{{checkedAmount}}</td>
                    <td><el-input placeholder="номер" v-model="filters.number" @change="changeFilter"></el-input></td>
                    <td><el-input placeholder="контрагент" v-model="filters.contractor" @change="changeFilter"></el-input></td>
                    <td><el-input placeholder="предмет" v-model="filters.subject" @change="changeFilter"></el-input></td>
                    <td>
                        <el-date-picker
                              v-model="filters.date_start_range"
                              type="daterange"
                              placeholder="Дата начала"
                              v-on:change="changeDateStart"
                              format="dd.MM.yyyy">
                        </el-date-picker>
                    </td>
                    <td>
                        <el-date-picker
                              v-model="filters.date_finish_range"
                              type="daterange"
                              placeholder="Дата окончания"
                              v-on:change="changeDateFinish"
                              format="dd.MM.yyyy">
                        </el-date-picker>
                    </td>
                    <td><el-input placeholder="пролонгация" v-model="filters.prolongation" @change="changeFilter"></el-input></td>
                    <td><el-input placeholder="ответственный" v-model="filters.responsible" @change="changeFilter"></el-input></td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" @click="checkAll" v-model="allChecked">
                    </td>
                    <th class="sortingField" @click="sorting('number')">
                        Номер
                        <i
                            v-if="checkSortField('number').field"
                            class="pull-right fa"
                            v-bind:class="{
                                'fa-sort-amount-desc': checkSortField('number').type,
                                'fa-sort-amount-asc' : !checkSortField('number').type
                            }">
                        </i>
                    </th>
                    <th class="sortingField" @click="sorting('contractor')">
                        Контрагент
                        <i
                            v-if="checkSortField('contractor').field"
                            class="pull-right fa"
                            v-bind:class="{
                                'fa-sort-amount-desc': checkSortField('contractor').type,
                                'fa-sort-amount-asc' : !checkSortField('contractor').type
                            }">
                        </i>
                    </th>
                    <th class="sortingField" @click="sorting('subject')">
                        Предмет
                        <i
                            v-if="checkSortField('subject').field"
                            class="pull-right fa"
                            v-bind:class="{
                                'fa-sort-amount-desc': checkSortField('subject').type,
                                'fa-sort-amount-asc' : !checkSortField('subject').type
                            }">
                        </i>
                    </th>
                    <th class="sortingField" @click="sorting('date_start')">
                        Дата подписания
                        <i
                            v-if="checkSortField('date_start').field"
                            class="pull-right fa"
                            v-bind:class="{
                                'fa-sort-amount-desc': checkSortField('date_start').type,
                                'fa-sort-amount-asc' : !checkSortField('date_start').type
                            }">
                        </i>
                    </th>
                    <th class="sortingField" @click="sorting('date_finish')">
                        Дата окончания
                        <i
                            v-if="checkSortField('date_finish').field"
                            class="pull-right fa"
                            v-bind:class="{
                                'fa-sort-amount-desc': checkSortField('date_finish').type,
                                'fa-sort-amount-asc' : !checkSortField('date_finish').type
                            }">
                        </i>
                    </th>
                    <th class="sortingField fixed-width-column" @click="sorting('prolongation')">
                        Пролонгация
                        <i
                            v-if="checkSortField('prolongation').field"
                            class="pull-right fa"
                            v-bind:class="{
                                'fa-sort-amount-desc': checkSortField('prolongation').type,
                                'fa-sort-amount-asc' : !checkSortField('prolongation').type
                            }">
                        </i>
                    </th>
                    <th class="sortingField fixed-width-column" @click="sorting('responsible')">
                        Ответственный
                        <i
                            v-if="checkSortField('responsible').field"
                            class="pull-right fa"
                            v-bind:class="{
                                'fa-sort-amount-desc': checkSortField('responsible').type,
                                'fa-sort-amount-asc' : !checkSortField('responsible').type
                            }">
                        </i>
                    </th>
                </tr>
            </thead>
            <tbody v-if="contracts.length">
                <tr
                        v-for="contract, index in contracts"
                        @contextmenu="rightClickRow(contract, index)"
                        v-bind:class="{
                            'text-muted': contract.archive,
                            'text-red': contract.isExpired && !contract.archive,
                            'current-row': index == selectedRow}"
                >
                    <td>
                        <input type="checkbox" v-model="checkedContractIds" @click="check(contract)" :value="contract.id">
                    </td>
                    <td>{{ contract.number }}</td>
                    <td>{{ contract.contractor_name }}</td>
                    <td>{{ contract.subjects_labels }}</td>
                    <td>{{ formatDate(contract.date_start) }}</td>
                    <td>{{ formatDate(contract.date_finish) }}</td>
                    <td>{{ contract.prolongation_name }}</td>
                    <td>{{ contract.responsible_name }}</td>
                </tr>
            </tbody>
            <tbody v-if="!contracts.length">
            <tr>
                <td colspan="8"><em class="text-muted">Нет договоров</em></td>
            </tr>
            </tbody>
        </table>

        <div class="back-foul" @click="hideContext()" v-if="context.show"></div>
        <transition name="fade">
            <ul
                v-if="context.show && !checkedContractIds.length"
                v-bind:style="{ top: context.params.top + 'px', left: context.params.left + 'px', position: context.params.position, zIndex: context.params.zIndex }"
                class="el-dropdown-menu"
                x-placement="bottom-end">
                <li class="el-dropdown-menu__item" @click="view(currentRow.id)">
                    <i class="el-icon-search"></i> Просмотр
                </li>
                <li class="el-dropdown-menu__item el-dropdown-menu__item--divided" @click="edit(currentRow.id)" v-bind:class="{'is-disabled': currentRow.archive}">
                    <i class="el-icon-edit"></i> Редактировать
                </li>
                <li class="el-dropdown-menu__item" @click="confirmProlongate(currentRow)" v-bind:class="{'is-disabled': currentRow.archive || currentRow.prolongation_id == 1 || currentRow.prolongation_id == 3}">
                    <i class="fa fa-clock-o"></i> Пролонгировать
                </li>
                <li class="el-dropdown-menu__item" @click="confirmAnnul(currentRow)" v-bind:class="{'is-disabled': currentRow.archive}">
                    <i class="fa fa-window-close-o"></i> Расторгнуть
                </li>
                <li class="el-dropdown-menu__item" @click="confirmArchive(currentRow)" v-if="!currentRow.archive">
                    <i class="fa fa-archive"></i> В архив
                </li>
                <li class="el-dropdown-menu__item" @click="confirmReturnArchive(currentRow)" v-else v-bind:class="{'is-disabled': currentRow.annul_doc.length}">
                    <i class="fa fa-archive"></i> Из архива
                </li>
                <li class="el-dropdown-menu__item" @click="confirmDrop(currentRow)">
                    <i class="el-icon-delete"></i> Удалить
                </li>
            </ul>
            <ul
                v-if="context.show && checkedContractIds.length"
                v-bind:style="{ top: context.params.top + 'px', left: context.params.left + 'px', position: context.params.position, zIndex: context.params.zIndex }"
                class="el-dropdown-menu"
                x-placement="bottom-end">
                <li class="el-dropdown-menu__item" @click="confirmResponsibleChange()" v-bind:class="{'is-disabled': currentRow.archive}">
                    <i class="fa fa-user-o"></i> Изменить ответственного
                </li>
            </ul>
        </transition>
        <el-dialog
            title="Удаление договора"
            :visible.sync="confirmDropFlag"
            width="30%"
            >
            <div class="text-center">
                <h4>
                    Вы действительно хотите удалить договор
                    <em><b>№ {{ modelToBeDeleted.number }}</b></em>
                    от
                    <em> <b>{{ formatDate(modelToBeDeleted.date_start) }}</b>? </em>
                </h4>

                <span class="not-set">
                    Это действие нельзя будет отменить.
                </span>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="drop()">Удалить</el-button>
                <el-button type="default" @click="confirmDropFlag = false">Отмена</el-button>
            </span>
        </el-dialog>
        <el-dialog
            title="Архивация договора"
            :visible.sync="confirmArchiveFlag"
            width="30%"
            >
            <div class="text-center">
                <h4>
                    Вы действительно хотите отправить договор
                    <em><b>№ {{ modelToBeDeleted.number }}</b></em>
                    от
                    <em> <b>{{ formatDate(modelToBeDeleted.date_start) }}</b> </em>
                    в архив?
                </h4>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="archive()">Архивировать</el-button>
                <el-button type="default" @click="confirmArchiveFlag = false">Отмена</el-button>
            </span>
        </el-dialog>
        <el-dialog
            title="Изменение ответственного"
            :visible.sync="confirmResponsibleChangeFlag"
            width="30%"
            >
            <div class="">
                <label for="">Указать ответственным</label>
                <el-select
                        v-model="newResponsible"
                        filterable remote placeholder="Укажите сотрудника"
                        :remote-method="getEmployees"
                    >
                    <el-option v-for="item in employees" :key="item.id" :label="item.fio" :value="item.id">
                    </el-option>
                </el-select>
            </div>
            <div style="margin: 15px 0">
                <label for="">Для договоров</label>
                <table class="table text-center">
                    <thead>
                        <tr>
                            <td>Номер</td>
                            <td>Дата подписания</td>
                            <td>Предыдущий ответственный</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="contract in contractsToResponsibleChange">
                            <td>{{contract.number}}</td>
                            <td>{{formatDate(contract.date_finish)}}</td>
                            <td>{{contract.responsible_name}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="responsibleChange()" :disabled="!newResponsible">Применить</el-button>
                <el-button type="default" @click="confirmResponsibleChangeFlag = false">Отмена</el-button>
            </span>
        </el-dialog>
        <el-dialog
            title="Расторжение договора"
            :visible.sync="confirmAnnulFlag"
            width="30%"
            >
            <div class="text-center">
                <h4>
                    Вы действительно хотите расторгнуть договор
                    <em><b>№ {{ modelToBeDeleted.number }}</b></em>
                    от
                    <em> <b>{{ formatDate(modelToBeDeleted.date_start) }}</b> </em>
                    ?
                </h4>
                <load-file-component
                     :model="modelToBeDeleted"
                     :fileList="modelToBeDeleted.annul_doc_list"
                     :action="actions.loadFile"
                     :data="{ folder: 'commercial' }"
                     label="Загрузить документ"
                     property="annul_doc"
                 >
                 </load-file-component>
                <span class="not-set">
                    Для расторжения договора необходимо загрузить документ. Договор будет отправлен в архив. Это действие нельзя будет отменить.
                </span>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="annul()">Расторгнуть</el-button>
                <el-button type="default" @click="confirmAnnulFlag = false">Отмена</el-button>
            </span>
        </el-dialog>
        <el-dialog
            title="Восстановить договор из архива"
            :visible.sync="confirmReturnArchiveFlag"
            width="30%"
            >
            <div class="text-center">
                <h4>
                    Вы действительно хотите восстановить договор
                    <em><b>№ {{ modelToBeDeleted.number }}</b></em>
                    от
                    <em> <b>{{ formatDate(modelToBeDeleted.date_start) }}</b> </em>
                    из архива?
                </h4>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="returnArchive()">Вернуть</el-button>
                <el-button type="default" @click="confirmReturnArchiveFlag = false">Отмена</el-button>
            </span>
        </el-dialog>
        <el-dialog
            title="Пролонгация договора"
            :visible.sync="confirmProlongateFlag"
            width="30%"
            >
            <div class="text-center">
                <h4>
                    Вы действительно хотите продлить срок действия договора
                    <em><b>№ {{ modelToBeDeleted.number }}</b></em>
                    от
                    <em> <b>{{ formatDate(modelToBeDeleted.date_start) }}</b> </em>
                    на год?
                </h4>

                <span class="not-set">
                    Это действие нельзя будет отменить.
                </span>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="prolongate()">Продлить</el-button>
                <el-button type="default" @click="confirmProlongateFlag = false">Отмена</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
import StaticMixin from '../../../../mixins/StaticMixin.js';
import LoadFileComponent from '../../../common-components/LoadFileComponent.vue';
export default {
    data() {
        return {
            actions:{
                loadFile: '/api/v1/legal/contracts/load-contract-file',
            },
            context: {
                params: {
                    left: '0',
                    top: '0',
                    position: 'fixed',
                    zIndex: 9999
                },
                show: false
            },
            currentRow: '',
            modelToBeConducted: {},
            modelToBeDeleted: {},
            newResponsible: '',
            allChecked: false,
            contractsToResponsibleChange: [],
            checkedContractIds: [],
            confirmDropFlag: false,
            confirmArchiveFlag: false,
            confirmAnnulFlag: false,
            confirmReturnArchiveFlag: false,
            confirmResponsibleChangeFlag: false,
            confirmProlongateFlag: false,
            redirectUrl: '/legal/organization/'
        }
    },
    created() {
        this.getEmployees();
    },
    components: {
        LoadFileComponent
    },
    props: ["contracts", "filters", "selectedRow"],
    mixins: [StaticMixin],
    computed: {
        checkedAmount: function () {
            var amount = this.contractsToResponsibleChange.length;
            return amount ? amount : '' ;
        }
    },
    methods: {
        hideContext() {
            this.context.show = false;
            this.$emit('selectRow', '-1');
        },
        confirmDrop(modelToBeDeleted) {
            this.modelToBeDeleted = modelToBeDeleted;
            this.hideContext();
            this.confirmDropFlag = true;
        },
        confirmArchive(modelToBeDeleted) {
            this.modelToBeDeleted = modelToBeDeleted;
            this.hideContext();
            this.confirmArchiveFlag = true;
        },
        confirmAnnul(modelToBeDeleted) {
            this.modelToBeDeleted = modelToBeDeleted;
            this.hideContext();
            this.confirmAnnulFlag = true;
        },
        confirmReturnArchive(modelToBeDeleted) {
            this.modelToBeDeleted = modelToBeDeleted;
            this.hideContext();
            this.confirmReturnArchiveFlag = true;
        },
        confirmProlongate(modelToBeDeleted) {
            this.modelToBeDeleted = modelToBeDeleted;
            this.hideContext();
            this.confirmProlongateFlag = true;
        },
        confirmResponsibleChange() {
            this.hideContext();
            this.confirmResponsibleChangeFlag = true;
        },
        drop() {
            this.$emit('drop-contract', this.modelToBeDeleted.id);
            this.confirmDropFlag = false;
        },
        archive() {
            this.$emit('archive-contract', this.modelToBeDeleted.id);
            this.confirmArchiveFlag = false;
        },
        annul() {
            this.$emit('annul-contract', this.modelToBeDeleted);
            this.confirmAnnulFlag = false;
        },
        responsibleChange() {
            var params = {
                checkedContractIds: this.checkedContractIds,
                responsibleId: this.newResponsible
            };
            this.$emit('responsible-change-contract', params);
            this.confirmResponsibleChangeFlag = false;
            this.resetCheck();
        },
        returnArchive() {
            this.$emit('archive-contract', this.modelToBeDeleted.id);
            this.confirmReturnArchiveFlag = false;
        },
        prolongate() {
            this.$emit('prolongate-contract', this.modelToBeDeleted.id);
            this.confirmProlongateFlag = false;
        },
        sorting(field) {
            this.$emit('sort', field)
        },
        changeFilter() {
            this.$emit('get-contracts');
        },
        edit(id) {
            this.$router.push(this.redirectUrl + 'update/' + id);
        },
        addLicense(row) {
            this.$router.push({path: this.redirectUrl + 'add-license', query: {contractId: row.id, contractName: row.number, folder: 'organization/licenses'}});
        },
        view(id) {
            this.$router.push(this.redirectUrl + 'view/' + id);
        },
        rightClickRow(row, index) {
            event.preventDefault();
            this.currentRow = row;
            this.context.params.left = event.clientX;
            this.context.params.top = event.clientY;
            this.context.show = true;
            this.$emit('selectRow', index);
        },
        changeDateStart(range) {
            if (range) {
                this.filters.date_start = [moment(range[0]).unix(), moment(range[1]).unix()];
            } else {
                this.filters.date_start = '';
            }
            this.changeFilter();
        },
        changeDateFinish(range) {
            if (range) {
                this.filters.date_finish = [moment(range[0]).unix(), moment(range[1]).unix()];
            } else {
                this.filters.date_finish = '';
            }
            this.changeFilter();
        },
        checkSortField(findField) {
            for (var field in this.filters.sortFields) {
                if (this.filters.sortFields.hasOwnProperty(findField)) {
                    return {
                        field: true,
                        type: this.filters.sortFields[findField]
                    };
                }
            }

            return {
                field: false,
                type: false
            };
        },
        formatDate(date) {
            if (date == '-' || date == null) {
                return '-';
            }
            return moment(date).format("DD.MM.YYYY");
        },
        checkAll() {
            this.checkedContractIds = [];
            this.contractsToResponsibleChange = [];
            var self = this;

            if (!this.allChecked) {
                this.contracts.forEach(function(contract) {
                    self.checkedContractIds.push(contract.id);
                    self.contractsToResponsibleChange.push(contract);
                });
            }
        },
        check(contract) {
            this.allChecked = false;
            var index = this.checkedContractIds.indexOf(contract.id);
            if (index > -1) {
                this.checkedContractIds.splice(index, 1);
                this.contractsToResponsibleChange.splice(index, 1);
            } else {
                this.checkedContractIds.push(contract.id);
                this.contractsToResponsibleChange.push(contract);
            }
        },
        resetCheck() {
            this.allChecked = false;
            this.checkedContractIds = [];
            this.contractsToResponsibleChange = [];
        }
    }
}
</script>

<style lang="css">
    tbody tr {
        cursor: pointer;
    }

    .el-dropdown-menu__item i {
        margin-right: 5px;
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active до версии 2.1.8 */ {
        opacity: 0;
    }

    .back-foul {
        width: 1920px;
        height: 1080px;
        opacity: 0;
        position: fixed;
        top: 0px;
        left: 0px;
        z-index: 550;
    }
    .contracts-table .input-row td{
        padding: 0 10px!important;
    }
    .contracts-table tr th{
        padding: 8px 2px!important;
    }
    .contracts-table .input-row td .el-input input{
        padding: 0!important;
    }
    .contracts-table .el-input__inner {
        height: auto;
        text-align: center;
        border: transparent;
        font-size: 12px;
    }
    .input-row .el-range-input{
        font-size: 12px;
    }
    .sortingField {
        cursor: pointer;
    }

</style>
