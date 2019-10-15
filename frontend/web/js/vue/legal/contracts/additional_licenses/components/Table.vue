<template>
    <div class="">
        <div class="form-group">
            <label for="">Наименование</label>
            <el-input v-model="license.name" placeholder="Укажите наименование"></el-input>
        </div>
        <div class="form-group">
            <label for="">Дата подписания</label>
            <el-date-picker v-model="license.date_start" type="date" format="dd.MM.yyyy" placeholder="Укажите дату">
            </el-date-picker>
        </div>
        <div class="form-group">
            <load-file-component
                 :model="license"
                 :fileList="license.license_list"
                 :action="actions.loadFile"
                 :data="{ folder: 'commercial' }"
                 label="Загрузить доп. соглашение"
                 property="contracts"
             >
             </load-file-component>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm btn-flat" @click="addLicense()">
                <i class="fa fa-save"></i> Сохранить
            </button>
        </div>
    </div>
</template>

<script>
import LoadFileComponent from '../../common-components/LoadFileComponent.vue';

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
        'license'
    ],
    created() {

    },
    methods: {
        addLicense() {

        },
    }
}
</script>

<style lang="css">

</style>
