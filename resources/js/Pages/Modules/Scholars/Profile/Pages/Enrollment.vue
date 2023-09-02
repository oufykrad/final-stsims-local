<template>
    <b-row>
        <b-col xxl="12">
            <b-card no-body>
                <b-card-body style="height: calc(100vh - 380px); overflow: auto;">
                    <table class="table table-bordered table-nowrap align-middle mb-0">
                        <thead class="table-dark">
                            <tr class="fs-11">
                                <th class="text-center" width="20%">Academic Year</th>
                                <th class="text-center" width="20%">Semester</th>
                                <th class="text-center" width="20%">Level</th>
                                <th class="text-center" width="20%">No. of Fails</th>
                                <th class="text-center" width="20%">Status</th>
                            </tr>
                        </thead> 
                        <tbody>
                            <tr class="font-size-11" v-for="(list,index) in enrollments" v-bind:key="index">
                                <td width="20%" class="text-center fs-12">{{list.semester.academic_year}}</td>
                                <td width="20%" class="text-center fs-12">{{list.semester.semester.name}}</td>
                                <td width="20%" class="text-center fs-12">{{list.level.name}}</td>
                                <td width="20%" class="text-center fs-12">{{check(list.lists)}}</td>
                                <td class="text-center">
                                    <span v-if="!list.is_clear" class="badge bg-danger">Incomplete</span>
                                    <span v-else class="badge bg-success">Cleared</span>
                                </td>
                            </tr>
                        </tbody>
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
            enrollments: [],
            checks: []
        }
    },
    created(){
        this.enrollments = this.financials.data.enrollments;
    },
    methods: {
        check(lists){
            this.checks = [];
            if (lists.some(item => item.is_failed == 1)) {
                this.checks.push(true);
            }
            return this.checks.length;
        }
    }
}
</script>