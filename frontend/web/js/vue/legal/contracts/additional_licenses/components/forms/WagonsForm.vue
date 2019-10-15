<template lang="html">
    <div class="">
        <label for="">Прикрепить вагон</label>
        <div class="form-group">
            <div class="block">
                <el-select
                    v-model="wagon"
                    filterable
                    remote
                    v-on:change="addWagon"
                    placeholder="Укажите номер вагона"
                    :remote-method="getWagons"
                    :loading="loading">
                    <el-option
                        v-for="wagon in wagons"
                        :key="wagon.id"
                        :label="wagon.number"
                        :value="wagon.id">
                    </el-option>
                </el-select>
            </div>
        </div>
        <label for="">Прикрепленные вагоны</label>
        <table class="table text-center add-wagons-table">
            <thead>
                <tr>
                    <th>Номер вагона</th>
                    <th>Дата начала</th>
                    <th>Дата окончания</th>
                    <th>Стоимость, руб</th>
                    <th class="actions">Удалить</th>
                </tr>
            </thead>
            <tbody v-if="license.wagons.length" class="table text-center">
                <tr v-for="wagon in license.wagons">
                    <td class="wagon-number">{{ wagon.number }}</td>
                    <td>
                        <el-date-picker v-model="wagon.date_start" type="date" format="dd.MM.yyyy" placeholder="Укажите дату">
                        </el-date-picker>
                    </td>
                    <td>
                        <el-date-picker v-model="wagon.date_finish" type="date" format="dd.MM.yyyy" placeholder="Укажите дату">
                        </el-date-picker>
                    </td>
                    <td>
                        <el-input v-model="wagon.price" placeholder="Укажите стоимость">
                        </el-input>
                    </td>
                    <td>
                        <a href="#" class="fa fa-times text-danger" title="Удалить" @click="dropWagon(wagon.id)"></a>
                    </td>
                </tr>
            </tbody>
            <tbody v-if="!license.wagons.length">
            <tr>
                <td colspan="5"><em class="text-muted">Нет вагонов</em></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            actions: {
                getWagons: '/api/v1/static/data/search-wagon',
                restWagons: '/api/v1/wagons/default'
            },
            wagons: [],
            wagon: '',
            loading: false,
        }
    },
    props: ['license'],
    created() {
        this.getWagons();
    },
    methods: {
        getWagons(query = '') {
            let params = {
                query: query
            }

            this.$http.get(this.actions.getWagons, {params: params})
                .then((response) => {
                    this.wagons = response.data;
                });
        },

        addWagon() {
            if (!this.checkInArray(this.wagon)) {
                this.$notify({
                    title: 'Ошибка',
                    message: 'Вагон уже прикреплен к этому соглашению',
                    type: 'error'
                });

                this.wagon = '';
                return false;
            }

            if (this.wagon != '') {
                this.$http.get(this.actions.restWagons + '/' + this.wagon)
                    .then((response) => {
                        this.license.wagons.push({
                            id: response.data.id,
                            number: response.data.number,
                            date_start: this.license.date_start,
                            date_finish: this.license.date_finish,
                            price: '0.00',
                        });
                        this.wagon = '';
                        this.$notify({
                            title: 'Успешно!',
                            message: 'Вагон успешно прикреплен к соглашению',
                            type: 'success'
                        })
                    });
                    return true;
            }
        },

        checkInArray(id) {
            return this.license.wagons.every((el, index, arr) => {
                if (el.id == id) {
                    return false;
                }
                return true;
            });
        },

        dropWagon(id) {
            var self = this;
            this.license.wagons.every((el, index, arr) => {
                if (el.id == id) {
                    self.license.wagons.splice(index, 1);
                }
                return true;
            });
        }
    }
}
</script>

<style lang="css">
.wagon-number {
    font-size: 14px;
    vertical-align: middle !important;
}
.add-wagons-table .el-input__inner {
    border:none;
}
.add-wagons-table tbody tr td {
    padding: 0px;
    vertical-align: middle;
}
</style>
