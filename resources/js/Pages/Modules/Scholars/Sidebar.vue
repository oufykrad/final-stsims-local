<template>
    <div class="p-4 d-flex flex-column h-100">
        <div class="table-responsive">
            <table class="table table-borderless table-sm table-centered align-middle table-nowrap">
                <tbody class="border-0">
                    <tr v-for="(count,index) in total" v-bind:key="index">
                        <td>
                            <h4 class="text-truncate fs-14 fs-medium mb-0"><i class="ri-stop-fill align-middle fs-18 me-2" :class="colors[index]"></i>{{options[index]}}</h4>
                        </td>
                        <td class="text-end">
                            <p class="fw-bold mb-0" :class="colors[index]">{{count}}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <hr class="text-muted mt-n1 mb-4"/>
        <ul class="list-group mb-4" style="cursor: pointer;">
            <li class="list-group-item" @click="print('graduated')"><i class="bx bxs-printer float-end fs-20 text-light align-middle me-2"></i><i class="bx fs-18 bxs-award text-warning align-middle me-2"></i>Graduates <span class="text-muted fs-11">(w/ Honor)</span> </li>
            <li class="list-group-item" @click="print('graduates')"><i class="bx bxs-printer float-end fs-20 text-light align-middle me-2"></i><i class="bx fs-18 bxs-graduation text-warning align-middle me-2"></i>List of Graduates</li>
            <li class="list-group-item" @click="print('scholars')"><i class="bx bxs-printer float-end fs-20 text-light align-middle me-2"></i><i class="ri-team-fill fs-18 text-warning align-middle me-2"></i>List of Scholars</li>
        </ul>
        <div class="mt-auto">
            <b-row class="g-1">
                    <b-col lg="4">
                    <button @click="create()" class="btn btn-soft-primary btn-sm w-100" type="button">
                        <div class="btn-content"> Create </div>
                    </button>
                </b-col>
                <b-col lg="4">
                    <button @click="sync()" class="btn btn-soft-primary btn-sm w-100" type="button">
                        <div class="btn-content"> Sync </div>
                    </button>
                </b-col>
                <b-col lg="4">
                    <button @click="excel()" class="btn btn-primary btn-sm w-100" type="button">
                        <div class="btn-content"> Import </div>
                    </button>
                </b-col>
            </b-row>
        </div>
    </div>
    <Print :dropdowns="dropdowns" :programs="programs" ref="print"/>
    <Import ref="import" />
    <Sync ref="sync" />
</template>
<script>
import Print from './Modals/Print.vue';
import Import from './Modals/Import.vue';
import Sync from './Modals/Sync.vue';
export default {
    props: ['statuses', 'regions', 'programs', 'dropdowns'],
    components : { Print, Import, Sync },
    data(){
        return {
            currentUrl: window.location.origin,
            total: [],
            options: ['Ongoing Scholars','Graduated Scholars','Total Scholars'],
            colors: ['text-primary','text-info','text-success']
        }
    },
    created(){
        this.fetch();
    },
    methods : {
        fetch(){
            axios.get(this.currentUrl + '/scholars', {
                params: { type: 'counts'}
            })
            .then(response => {
                this.total = response.data;
            })
            .catch(err => console.log(err));
        },
        print(type){
            this.$refs.print.set(type);
        },
        excel() {
            this.$refs.import.show();
        },
        sync() {
            this.$refs.sync.show();
        },
        create(){
            this.$refs.create.show();
        }
    }
}
</script>