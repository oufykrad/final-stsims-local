<template>
    <b-row>
        <b-col lg="6">
            <b-card>
                <b-card-body style="height: calc(100vh - 355px)">

                </b-card-body>
            </b-card>
        </b-col>
        <b-col lg="6">
            <div class="card" id="contactList">
                
                <div class="card-body" style="height: calc(100vh - 392px)">
                    <div>
                        <table class="table table-borderless align-middle mt-n2">
                            <thead class="text-center">
                                <tr>
                                    <th>Grade</th>
                                    <th>Upper Limit</th>
                                    <th>Lower Limit</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(course, index) in lists" v-bind:key="'a-'+index" class="mb-n4">
                                    <td width="33%">
                                        <input type="text" class="form-control mb-n4" v-model="course.grade" placeholder="Grade" style="text-transform: capitalize;" required>
                                    </td>
                                    <td width="33%">
                                        <input type="text" class="form-control mb-n4" v-model="course.upper" placeholder="Upper Limit" style="text-transform: capitalize;" required>
                                    </td>
                                    <td width="33%">
                                        <input type="text" class="form-control mb-n4" v-model="course.lower" placeholder="Lower Limit" style="text-transform: capitalize;" required>
                                    </td>
                                    <td class="text-end" width="2%">
                                        <b-button @click="rmv(index)" variant="soft-danger" v-b-tooltip.hover title="Remove" class="edit-list mb-n4"><i class="ri-delete-bin-5-line align-bottom"></i> </b-button>
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td colspan="6">
                                            <div class="d-grid gap-2">
                                        <button  type="button" class="btn btn-light btn-sm mt-2 btn-block">
                                            <i class="text-muted bx bx-plus-circle"></i>
                                        </button>
                                            </div>
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
                 <div class="card-footer">
                    <div class="row g-3">
                        <div class="col-md-auto ms-auto">
                            <div class="hstack float-end gap-2 mt-4 mt-sm-0">
                                <button @click="add()" v-b-tooltip.hover title="Add" class="btn btn-primary btn-md float-end me-0" type="button">
                                    <div class="btn-content"><i class="bx bxs-plus-circle"></i></div>
                                </button>
                                <button @click="save()" class="btn btn-success btn-md btn-label" type="button">
                                    <div class="btn-content"><i class="ri-check-double-line label-icon align-middle fs-16 me-2"></i> Save </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-col>
    </b-row>
</template>
<script>
import Pagination from "@/Shared/Components/Pagination.vue";
export default {
    props: ['id'],
    components: { Pagination },
    data(){
        return{
            currentUrl: window.location.origin,
            lists: [],
            form: this.$inertia.form({
                id: '',
                grading: '',
                type: 'grading'
            }),
        }
    },
    computed: {
        updated: function () {
            return this.lists;
        },
    },
    methods: {
        save(){
            this.form.id = this.id;
            this.form.grading = this.updated;

            this.form.put('/schools/update',{
                preserveScroll: true,
                onSuccess: (response) => {}
            });
        },
        add(){
            this.lists.push({grade: '',upper: '', lower: ''});
        },
        rmv(index){
            this.lists.splice(index,1);
        },
    }
}
</script>
