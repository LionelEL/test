<template>
    <div class="">
        <div class="row">
            <div class="form-group col-sm-8">
                <label for="">Наименование</label>
                <el-input v-model="license.name" placeholder="Укажите наименование"></el-input>
                <div class="has-error" v-if="errors">
                    <span>{{ errors.name ? errors.name[0] : '' }}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group  col-sm-8">
                <label for="">Дата подписания</label>
                <el-date-picker v-model="license.date_start" type="date" format="dd.MM.yyyy" placeholder="Укажите дату">
                </el-date-picker>
                <div class="has-error" v-if="errors">
                    <span>{{ errors.date_start ? errors.date_start[0] : '' }}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-8">
                <load-file-component
                     :model="license"
                     :fileList="license.license_list"
                     :action="actions.loadFile"
                     :data="{ folder: folder }"
                     label="Загрузить доп. соглашение"
                     property="license"
                 >
                 </load-file-component>
            </div>
        </div>
    </div>
</template>

<script>
import LoadFileComponent from '../../../../common-components/LoadFileComponent.vue';

export default {
    data() {
        return {
            actions:{
                loadFile: '/api/v1/legal/licenses/load-license-file',
            },
        }
    },
    components: {
        LoadFileComponent
    },
    props: [
        'license',
        'folder',
        'errors'
    ],
    created() {
    }
}
</script>

<style lang="css">

</style>
