<template>
    <load-file-component
         :model="contract"
         :fileList="contract.licenses_list"
         :action="actions.loadFile"
         :data="{ folder: 'expediter' }"
         label="Загрузить доп. соглашения"
         property="licenses"
     >
     </load-file-component>
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
        LoadFileComponent,
    },
    props: [
        'contract'
    ],
    created() {

    }
}
</script>

<style lang="css">

</style>
