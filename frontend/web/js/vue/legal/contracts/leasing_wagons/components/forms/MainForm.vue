<template>
    <div class="">
        <div class="row">
            <div class="col-sm-4 form-group">
                <label for="">Номер договора</label>
                <el-input v-model="contract.number" placeholder="Укажите номер"></el-input>
                <div class="has-error" v-if="errors">
                    <span>{{ errors.number ? errors.number[0] : '' }}</span>
                </div>
            </div>
            <div class="col-sm-4 form-group">
                <label for="">Контрагент</label>
                <el-select
                    v-model="contract.contractor_id"
                    filterable remote placeholder="Укажите контрагента"
                    :remote-method="getCompanies"
                >
                    <el-option v-for="item in companies" :key="item.id" :label="item.name" :value="item.id">
                    </el-option>
                </el-select>
                <div class="has-error" v-if="errors">
                    <span>{{ errors.contractor_id ? errors.contractor_id[0] : '' }}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 form-group">
                <label for="">Бессрочный договор</label>
                <div class="">
                    <el-checkbox v-model="contract.permanent" @change="resetColumns">Дата окончания действия договора в документе не указана</el-checkbox>
                </div>
            </div>
            <div class="col-sm-2 form-group">
                <label for="">Дата подписания</label>
                <el-date-picker v-model="contract.date_start" type="date" format="dd.MM.yyyy" placeholder="Укажите дату">
                </el-date-picker>
                <div class="has-error" v-if="errors">
                    <span>{{ errors.date_start ? errors.date_start[0] : '' }}</span>
                </div>
            </div>
            <div class="col-sm-2 form-group">
                <label for="">Дата окончания</label>
                <el-date-picker v-model="contract.date_finish" type="date" format="dd.MM.yyyy" placeholder="Укажите дату" :disabled="contract.permanent">
                </el-date-picker>
                <div class="has-error" v-if="errors">
                    <span>{{ errors.date_finish ? errors.date_finish[0] : '' }}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 form-group">
                <label for="">Тип пролонгации</label>
                <el-select
                    v-model="contract.prolongation_id"
                    filterable remote placeholder="Укажите тип"
                    :remote-method="getContractProlongations"
                     :disabled="contract.permanent"
                >
                    <el-option v-for="item in contract_prolongations" :key="item.id" :label="item.name" :value="item.id">
                    </el-option>
                </el-select>
                <div class="has-error" v-if="errors">
                    <span>{{ errors.prolongation_id ? errors.prolongation_id[0] : '' }}</span>
                </div>
            </div>
            <div class="col-sm-2 form-group">
                <label for="">Заявление о пролонгации</label>
                <el-input v-model="contract.statement_term" placeholder="Укажите кол-во дней" :disabled="contract.permanent || contract.prolongation_id == 1"></el-input>
                <div class="has-error" v-if="errors">
                    <span>{{ errors.statement_term ? errors.statement_term[0] : '' }}</span>
                </div>
            </div>
            <div class="col-sm-2 form-group">
                <label for="">Оповещение об окончании</label>
                <el-input v-model="contract.prolongation_term" placeholder="Укажите кол-во дней" :disabled="contract.permanent"></el-input>
                <div class="has-error" v-if="errors">
                    <span>{{ errors.prolongation_term ? errors.prolongation_term[0] : '' }}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 form-group">
                <label for="">Предмет</label>
                <el-select
                    multiple
                    v-model="contract.subjects"
                    filterable remote placeholder="Укажите предмет"
                    :remote-method="getContractSubjects"
                >
                    <el-option v-for="item in contract_subjects" :key="item.id" :label="item.name" :value="item.id">
                    </el-option>
                </el-select>
            </div>
            <div class="col-sm-4 form-group">
                <label for="">Ответственный</label>
                <el-select
                    v-model="contract.responsible_id"
                    filterable remote placeholder="Укажите сотрудника"
                    :remote-method="getEmployees"
                >
                    <el-option v-for="item in employees" :key="item.id" :label="item.fio" :value="item.id">
                    </el-option>
                </el-select>
                <div class="has-error" v-if="errors">
                    <span>{{ errors.responsible_id ? errors.responsible_id[0] : '' }}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 form-group">
                <load-file-component
                     :model="contract"
                     :fileList="contract.contracts_list"
                     :action="actions.loadFile"
                     :data="{ folder: 'leasing_wagons' }"
                     label="Загрузить договор"
                     property="contracts"
                 >
                 </load-file-component>
            </div>
        </div>
    </div>
</template>

<script>
import StaticMixin from '../../../../../mixins/StaticMixin.js';
import LoadFileComponent from '../../../../common-components/LoadFileComponent.vue';

export default {
    data() {
        return {
            actions:{
                loadFile: '/api/v1/legal/contracts/load-contract-file',
            },
        }
    },
    components: {
        LoadFileComponent
    },
    props: [
        'contract',
        'errors'
    ],
    mixins: [StaticMixin],
    created() {
        var self = this;
        this.$root.$on('modelLoaded', function (params) {
            if (params.contract) {
                if (params.contract.prolongation) {
                    self.contract_prolongations.push(params.contract.prolongation);
                } else {
                    self.getContractProlongations();
                }

                if (params.contract.responsible) {
                    self.employees.push(params.contract.responsible);
                } else {
                    self.getEmployees();
                }

                if (params.contract.contractor) {
                    self.companies.push(params.contract.contractor);
                } else {
                    self.getCompanies();
                }

                self.getContractSubjects(self.contract.type_id);
            } else {
                self.getCompanies();
                self.getContractProlongations();
                self.getEmployees();
                self.getContractSubjects(self.contract.type_id);
            }
        });
    },
    methods: {
        resetColumns() {
            this.contract.prolongation_id = null;
            this.contract.statement_term = null;
            this.contract.prolongation_term = null;
            this.contract.date_finish = null;
        }
    }
}
</script>

<style lang="css">

</style>
