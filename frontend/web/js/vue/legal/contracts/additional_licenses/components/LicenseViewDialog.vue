<template lang="html">
    <div class="">
        <dl class="dl-horizontal">
            <dt>Дата начала</dt><dd>{{formatDate(license.date_start)}}</dd>
            <dt>Внес информацию</dt><dd >{{license.creator}}</dd>
            <dt>Документ</dt><dd >
                <div class="documents-section">
                    <ul v-if="license.license && license.license.length" class="list-unstyled">
                        <li v-for="doc in license.license"><a :href="doc.url" target="_blank" title="Просмотреть"><i class="fa fa-download"></i> {{doc.name}}</a></li>
                    </ul>
                    <div v-else class="text-muted"><em>нет</em></div>
                </div>
            </dd>
        </dl>
        <table v-if="license.wagons && license.wagons.length" class="table">
            <thead>
                <tr>
                    <th>Номер вагона</th>
                    <th>Дата начала</th>
                    <th>Дата окончания</th>
                    <th>Стоимость, руб</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="wagon in license.wagons">
                    <td>{{ wagon.wagon.number }}</td>
                    <td>{{formatDate(wagon.date_start)}}</td>
                    <td>{{formatDate(wagon.date_finish)}}</td>
                    <td>{{ wagon.price }}</td>
                </tr>
            </tbody>
        </table>
        <div v-else>
            <em class="text-muted">нет вагонов</em>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {

        }
    },
    props: [
        'license',
    ],
    methods: {
        formatDate(date) {
            if (date != undefined && !isNaN(date)) {
                return moment.unix(date).format("DD.MM.YYYY");
            }
            if (date == undefined || date == '-') {
                return '-';
            }
            return moment(date).format("DD.MM.YYYY");
        },
    }
}
</script>

<style lang="css" scoped>

</style>
