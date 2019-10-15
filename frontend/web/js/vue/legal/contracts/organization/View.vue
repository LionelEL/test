<template lang="html">
    <div>
        <left-menu></left-menu>
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span class="h4">Организационный договор № {{model.number}} от {{formatDate(model.date_start)}}</span>
                <div style="float: right;">
                    <router-link :to="{ path: this.redirectUrl + '/update/' + model.id}" class="btn btn-success btn-flat btn-sm"><i class="fa fa-edit"></i> Редактировать</router-link>
                    <button @click="goBack" class="btn btn-flat btn-sm btn-primary">
                        <i class=""></i> Назад
                    </button>
                </div>
            </div>
            <div class="text item repair-list main-info">
                <div class="row">
                    <div class="col-sm-6">
                        <div>
                            <h4>Основная информация</h4>
                            <dl class="dl-horizontal">
                                <dt>Контрагент</dt><dd><router-link :to="{ path: '/legal/companies/view/' + model.contractor_id}" title="Просмотреть">{{model.contractor_name}}</router-link></dd>
                                <dt>Дата подписания</dt><dd>{{formatDate(model.date_start)}}</dd>
                                <dt>Дата окончания</dt><dd v-if="!model.permanent">{{formatDate(model.date_finish)}} (осталось {{model.date_finish_left}})</dd><dd v-else>Бессрочный</dd>
                                <dt>Тип пролонгации</dt><dd v-if="!model.permanent">{{model.prolongation_name}}</dd><dd v-else>-</dd>
                                <dt>Заявление о пролонгации</dt><dd v-if="!model.permanent">{{model.statement_term_text}}</dd><dd v-else>-</dd>
                                <dt>Оповещение об окончании</dt><dd v-if="!model.permanent">{{formatDate(model.date_notify)}} (за {{model.prolongation_term_text}})</dd><dd v-else>-</dd>
                                <dt>Предмет договора</dt><dd>{{model.subjects_labels}}</dd>
                                <dt>Ответственный</dt><dd >{{model.responsible_name}}</dd>
                                <dt>Внес информацию</dt><dd >{{model.creator_name}}</dd>
                                <dt>Статус</dt><dd >{{!model.archive ? 'Активный' : 'В архиве'}}</dd>
                            </dl>
                        </div>
                        <div>
                            <h4>Состояние документа</h4>
                            <table class="table" v-if="model.statusHistory != null && model.statusHistory.length > 0">
                                <thead>
                                    <tr>
                                        <th>Статус</th>
                                        <th>Дата</th>
                                        <th>Ответственный</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in model.statusHistory">
                                        <td>
                                            <el-tag v-if="item.status.name == 'Не проведен'"
                                                close-transition
                                                style='width:100%'
                                                :type="item.status.tag_type">
                                                Создан
                                            </el-tag>
                                            <el-tag v-else
                                                close-transition
                                                style='width:100%'
                                                :type="item.status.tag_type">
                                                {{ item.status.name }}
                                            </el-tag>
                                        </td>
                                        <td>{{ formatDate(item.date) }}</td>
                                        <td>{{ item.employee.fio }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-else><span class="text-muted"><em>нет информации</em></span></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div>
                            <h4>Документы</h4>
                            <div class="documents-section">
                                <ul v-if="model.contracts && model.contracts.length" class="list-unstyled">
                                    <li v-for="doc in model.contracts"><a :href="doc.url" target="_blank" title="Просмотреть"><i class="fa fa-download"></i> {{doc.name}}</a></li>
                                </ul>
                                <div v-else class="text-muted"><em>нет</em></div>
                            </div>
                        </div>
                        <div>
                            <h4>Доп. соглашения</h4>
                            <div class="documents-section">
                                <ul v-if="model.licenses && model.licenses.length" class="list-unstyled">
                                    <li v-for="doc in model.licenses"><a :href="doc.url" target="_blank" title="Просмотреть"><i class="fa fa-download"></i> {{doc.name}}</a></li>
                                </ul>
                                <div v-else class="text-muted"><em>нет</em></div>
                            </div>
                        </div>
                        <div>
                            <h4>Приложения</h4>
                            <div class="documents-section">
                                <ul v-if="model.applications && model.applications.length" class="list-unstyled">
                                    <li v-for="doc in model.applications"><a :href="doc.url" target="_blank" title="Просмотреть"><i class="fa fa-download"></i> {{doc.name}}</a></li>
                                </ul>
                                <div v-else class="text-muted"><em>нет</em></div>
                            </div>
                        </div>
                        <div>
                            <h4>Спецификации</h4>
                            <div class="documents-section">
                                <ul v-if="model.specifications && model.specifications.length" class="list-unstyled">
                                    <li v-for="doc in model.specifications"><a :href="doc.url" target="_blank" title="Просмотреть"><i class="fa fa-download"></i> {{doc.name}}</a></li>
                                </ul>
                                <div v-else class="text-muted"><em>нет</em></div>
                            </div>
                        </div>
                        <div>
                            <h4>Акты приема-передачи</h4>
                            <div class="documents-section">
                                <ul v-if="model.delivery_acts && model.delivery_acts.length" class="list-unstyled">
                                    <li v-for="doc in model.delivery_acts"><a :href="doc.url" target="_blank" title="Просмотреть"><i class="fa fa-download"></i> {{doc.name}}</a></li>
                                </ul>
                                <div v-else class="text-muted"><em>нет</em></div>
                            </div>
                        </div>
                        <div>
                            <h4>Прочие документы</h4>
                            <div class="documents-section">
                                <ul v-if="model.others && model.others.length" class="list-unstyled">
                                    <li v-for="doc in model.others"><a :href="doc.url" target="_blank" title="Просмотреть"><i class="fa fa-download"></i> {{doc.name}}</a></li>
                                </ul>
                                <div v-else class="text-muted"><em>нет</em></div>
                            </div>
                        </div>
                        <div>
                            <h4>Соглашение о расторжении</h4>
                            <div class="documents-section">
                                <ul v-if="model.annul_doc && model.annul_doc.length" class="list-unstyled">
                                    <li v-for="doc in model.annul_doc"><a :href="doc.url" target="_blank" title="Просмотреть"><i class="fa fa-download"></i> {{doc.name}}</a></li>
                                </ul>
                                <div v-else class="text-muted"><em>нет</em></div>
                            </div>
                        </div>
                        <div>
                            <h4>История ответственных</h4>
                            <el-timeline v-if="model.responsibleHistory != null && model.responsibleHistory.length > 0">
                                <el-timeline-item
                                    v-for="(activity, index) in model.responsibleHistory"
                                    :key="index"
                                    :timestamp="formatDate(activity.date)">
                                    {{activity.employee.fio}}
                                </el-timeline-item>
                            </el-timeline>
                        </div>
                    </div>
                </div>
            </div>
        </el-card>
        <el-dialog :title="'Доп. соглашение ' + currentLicense.name" :visible.sync="viewLicenseDialog" width="50%">
            <license-view-dialog :license="currentLicense"></license-view-dialog>
        </el-dialog>
    </div>
</template>

<script>
import LeftMenu from '../../../common-components/LeftMenu.vue';
import LicenseViewDialog from '../additional_licenses/components/LicenseViewDialog.vue';
import {
    Loading
} from 'element-ui';
export default {
    data() {
        return {
            actions: {
                getModel: "/api/v1/legal/contracts",
            },
            redirectUrl: '/legal/organization',
            model: {},
            viewLicenseDialog: false,
            currentLicense: {}
        }
    },
    components: {
        'left-menu': LeftMenu,
        'license-view-dialog': LicenseViewDialog,
    },
    created() {
        this.getModel();
    },
    methods: {
        getModel() {
            this.loadingInstance = Loading.service({
                'text': "Формируем данные о договоре..."
            });
            this.$http.get(this.actions.getModel + '/' + this.$route.params.id)
                .then((response) => {
                    this.model = response.data;

                    this.loadingInstance.close();
                });
        },
        downloadFile(href) {
            window.open(href, '_blank');
        },
        editLicense(id) {
            this.$router.push(this.redirectUrl + '/update-license/' + id);
        },
        viewLicense(license) {
            this.viewLicenseDialog = true;
            this.currentLicense = license;
        },
        view(id) {
            this.$router.push(this.redirectUrl + 'view/' + id);
        },
        formatDate(date) {
            if (date != undefined && !isNaN(date)) {
                return moment.unix(date).format("DD.MM.YYYY HH:mm");
            }
            if (date == undefined || date == '-') {
                return '-';
            }
            return moment(date).format("DD.MM.YYYY");
        },
        goBack() {
            this.$router.go(-1);
        }
    }
}
</script>

<style>
.repair-list .dl-horizontal dt {
    text-align: left;
    font-weight: normal;
}

.main-info dt {
    width: 40%;
}

.main-info dd {
    margin-left: 40%;
}

.action-th {
    width: 50px;
}
</style>
