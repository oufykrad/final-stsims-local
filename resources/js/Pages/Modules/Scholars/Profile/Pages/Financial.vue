<template>
    <b-row>
        <b-col xxl="12">
            <b-card no-body>
                <b-card-body style="height: calc(100vh - 380px); overflow: auto;">
                    <table class="table table-bordered table-nowrap align-middle mb-0">
                        <thead class="table-dark">
                            <tr class="fs-11">
                                <th class="text-center" width="20%">Academic Year | Semester</th>
                                <th v-for="(l,index) in headers" v-bind:key="index" :width="70/headers.length+'%'" class="text-center">{{l}}</th>
                                <th class="text-center" width="10%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(s,index) in semesters" v-bind:key="index">
                                <tr class="fs-11" v-if="(counts[index] != 0) ? true : false">
                                    <td class="text-center">{{semesters[index].academic_year}} - {{semesters[index].semester.name}}</td>
                                    <td v-for="(l,index) in headers" v-bind:key="index" class="text-center">
                                        ₱ {{calculate(l,s.id)}}
                                    </td>
                                    <td class="text-center"> 
                                        ₱ {{calculate('Total',s.id)}}
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <thead>
                            <tr class="table-light fs-11 fw-semibold">
                                <td class="text-center">Total</td>
                                <td v-for="(l,index) in headers" v-bind:key="index" class="text-center">
                                    ₱ {{formatMoney(check(l))}}
                                </td>
                                <td class="text-center">₱ {{formatMoney(monitoring.total)}}</td>
                            </tr>
                        </thead>
                    </table>
                </b-card-body>
            </b-card>
        </b-col>
    </b-row>
</template>
<script>
export default {
    props: ['financials'],
    data(){
        return {
            monitoring: '',
            headers: [],
            semesters: [],
            counts: [],
            totals: []
        }
    },
    created(){
        this.show();
    },
    methods: {
        show() {
            this.monitoring = this.financials.data;

            this.monitoring.benefits.map((list) => {
                if (!this.headers.includes(list.benefit.short)) {
                    this.headers.push(list.benefit.short);
                }
            });

            this.monitoring.enrollments.map((list) => {
                if (!this.semesters.some(item => item.id === list.semester.id)) {
                    this.semesters.push(list.semester);
                }
                var benefitsLength = this.monitoring.benefits.filter((item) => item.school_semester_id == list.semester.id);
                this.counts.push(benefitsLength.length);
            });

            this.showModal = true;
        },
        calculate(data,semester){
            if(data != 'Total'){
                var total = 0;
            
                this.monitoring.benefits.map((list) => {
                    if(list.benefit.short == data) {
                        if(list.school_semester_id == semester){
                            total = parseInt(total) + parseInt(list.amount);
                        }
                    }
                });
                this.totals.push(total);
                return this.formatMoney(total);
            }else{
                let t = this.formatMoney(this.totals.reduce((a, b) => a + b, 0));
                this.totals = [];
                return t;
            };
            return 0;
        },
        check(l){
            var total = 0;
            this.monitoring.benefits.map((list) => {
                if(list.benefit.short == l) {
                    total = parseInt(total) + parseInt(list.amount);
                }
            });
            return total;
        },
        formatMoney(value) {
            let val = (value/1).toFixed(2).replace(',', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },
    }
}
</script>