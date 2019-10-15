<template>
    <div class="row">
        <div class="col-sm-4">
            <load-file-component
                 :model="contract"
                 :fileList="contract.applications_list"
                 :action="actions.loadFile"
                 :data="{ folder: 'repair_ps' }"
                 label="Загрузить приложения"
                 property="applications"
             >
             </load-file-component>
        </div>
    </div>
</template>

<script>
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
        'contract'
    ]
}
</script>

<style lang="css">

</style>
