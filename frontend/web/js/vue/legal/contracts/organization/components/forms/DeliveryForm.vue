<template>
    <div class="row">
        <div class="col-sm-4">
            <load-file-component
                 :model="contract"
                 :fileList="contract.delivery_acts_list"
                 :action="actions.loadFile"
                 :data="{ folder: 'organization' }"
                 label="Загрузить акты приема-передачи"
                 property="delivery_acts"
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
