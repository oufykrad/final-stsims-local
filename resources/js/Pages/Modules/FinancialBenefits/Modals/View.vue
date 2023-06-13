<template>
    <b-modal v-model="showModal" title="View Financial Benefit" header-class="p-3 bg-light" size="xl" class="v-modal-custom" modal-class="zoomIn" centered hide-footer>    
        <b-form class="customform mb-2">
           
            <div class="row">
                <div class="col-md-12">
                    <hr class="text-muted"/>
                    <div class="table-responsive">
                        <table class="table table-bordered table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr class="fs-11">
                                    <th>Benefit</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="list in selected.benefits" v-bind:key="list.id">
                                    <td>
                                        <h5 class="fs-13 mb-0 text-dark" v-if="list.benefit">
                                            {{list.benefit.name}} 
                                            <span v-if="list.benefit.name == 'Stipend'" class="text-muted fs-11">({{list.month}})</span>
                                        </h5>
                                    </td>
                                    <td class="text-center" v-if="list.benefit">₱{{formatMoney(list.amount,list.benefit.name)}}</td>
                                    <td class="text-center">
                                        <span v-if="!list.release_type" class="badge bg-info">Partial</span>
                                        <span v-else class="badge bg-success">Full</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </b-form>
    </b-modal>
</template>
<script>
export default {
    data(){
        return {
            showModal: false,
            selected: ''
        }
    },
    methods : {
        set(data){
            console.log(data);
            this.selected = data;
            this.showModal = true;
        },
        formatMoney(value) {
            let val = (value/1).toFixed(2).replace(',', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },
    }
}
</script>