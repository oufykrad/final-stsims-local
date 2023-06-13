<template>
    <b-row class="mb-3 mt-n2">
        <div class="col-md-6">
            <div class="page-title-left mt-2">
                <ol class="breadcrumb m-0 fs-15">
                    <li class="breadcrumb-item fw-bold">{{ selected.academic_year }}</li>
                    <li class="breadcrumb-item active"><span>{{selected.level }}</span></li>
                    <li class="breadcrumb-item active"><span class="fw-bold text-primary">{{selected.semester }}</span></li>
                </ol>
            </div> 
        </div>
        <div class="col-md-6">
            <div class="hstack float-end gap-2 mt-4 mt-sm-0">
                <button v-if="selected.is_locked == 0" @click="locked(selected.id)" :disabled="!selected.is_clear" v-b-tooltip.hover title="Lock" class="btn btn-primary btn-md float-end me-0" type="button">
                    <div class="btn-content"><i class="bx bxs-lock-alt"></i></div>
                </button>
                <button v-if="selected.is_locked == 0" @click="save()" class="btn btn-success btn-md btn-label" type="button">
                    <div class="btn-content"><i class="ri-check-double-line label-icon align-middle fs-16 me-2"></i> Save </div>
                </button>
            </div>
        </div>
    </b-row>
    <hr />
    <div class="todo-content position-relative px-4 mx-n4" id="todo-content">
        <div class="col-md-12" style="overflow: auto;">
            <div class="table-responsive">
                <table class="table table-centered table-bordered table-nowrap mb-0">
                    <thead class="thead-light">
                        <tr class="font-size-11">
                            <th style="width: 5%;" class="text-center">#</th>
                            <th style="width: 15%;" class="text-center">Code</th>
                            <th class="text-center" style="width: 60%;">Subject</th>
                            <th class="text-center" style="width: 10%;">Unit</th>
                            <th class="text-center" style="width: 10%;">Grade</th>
                        </tr>
                    </thead>
                </table>
                <SimpleBar class="align-items-center d-flex justify-content-center">
                    <table class="table table-centered table-bordered table-nowrap">
                        <tbody class="fs-12">
                            <tr v-for="(list,index) in lists" v-bind:key="list.id" :class="[(list.grade == 5 || list.grade == 'F' || list.grade == 'f') ? 'table-danger' : '']">
                                <td style="width: 5%;" class="text-center">{{ index+1 }}</td>
                                <td style="width: 15%;" class="text-center fw-bold">{{list.code}} <span v-if="list.is_lab == true" class="text-warning fw-bold">(Lab)</span></td>
                                <td style="width: 60%;" class="text-center">{{list.subject}} <span v-if="list.is_lab == true" class="text-warning fw-bold">(Lab)</span></td>
                                <td style="width: 10%;" class="text-center">{{list.unit}}</td>
                                <td style="width: 10%;" class="text-center">
                                    <input type="text" v-model="list.grade" :disabled="selected.is_locked == 1" class="text-center mt-n1 mb-n2 form-control form-control-sm" style="text-transform: uppercase">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </SimpleBar>
                    <table class="table table-centered table-bordered table-nowrap mt-n3 mb-0">
                        <tfoot class="thead-light">
                            <tr class="font-size-12">
                                <th style="width: 80%;" colspan="3"></th>
                                <th class="text-center text-primary" style="width: 10%;">{{ units }}</th>
                                <th class="text-center text-primary" style="width: 10%;">{{ total }}</th>
                            </tr>
                        </tfoot>
                    </table>
            </div>
        </div>
    </div>
    <!-- <b-row class="mb-3 mt-3">
        <div class="col-md-6">
            <input v-if="selected.is_locked == 0" multiple ref="fileupload" type="file" @change="uploadFieldChange" class="form-control " :class="[($page.props.errors['files.'+0]) ? 'text-danger' : '']" style="width: 300px;" id="formFileSm"/>          
        </div>
        <div class="col-md-6">
            <div class="hstack float-end gap-2 mt-4 mt-sm-0">
                <a v-if="selected.is_locked == 1" href="javascript: void(0);" class="text-dark fw-medium"><i class="mdi mdi-file-document font-size-16 align-middle text-primary"></i> index.html</a>
                <b-button v-if="selected.is_locked == 0" @click="save" type="button" class="w-lg float-end me-0" variant="primary">Save</b-button>
                <b-button v-if="selected.is_locked == 0" @click="locked(selected.id)" :disabled="!selected.is_clear" type="button" class="float-end" variant="primary">
                    <i class="bx bxs-lock-alt "></i>
                </b-button>
            </div>
        </div>
    </b-row> -->
    <Lock :lists="lists" @status="message" ref="lock"/>
    <Confirm ref="confirm"/>
</template>
<script>
import Lock from '../Modals/Lock.vue';
import Confirm from '../Modals/Confirm.vue';
import { SimpleBar } from 'simplebar-vue3';
export default {
    components: { SimpleBar, Lock, Confirm },
    props: ['selected','user'],
    data() {
        return {
            currentUrl: window.location.origin,
            errors: [],
            attachments: [],
            lists: [],
            type: null,
        }
    },
    watch: {
        datares: {
            deep: true,
            handler(val = null) {
                if(val != null && val !== ''){
                    this.message(val.data);
                    // console.log(val)
                }
            },
        },
    },
    computed: {
        units: function () {
            var sum = 0;
            if(this.lists != undefined){
                this.lists.forEach(e => {
                    sum += Number(e.unit);
                });
            }
            return sum
        },
        total: function () {
            var sum = 0; var tot = 0;
            if(this.lists != undefined){
                this.lists.forEach(e => {
                    if(e.is_nonacademic == false){
                        tot++;
                        if(e.grade == 'F' || e.grade == 'f'){

                        }else{
                            sum += Number(e.grade);
                        }
                    }
                });
            }
            return (sum/tot).toFixed(3);
            this.$forceUpdate();
        },
        failed: function(){
            var count = 0;
            this.lists.forEach(e => {
                if(e.grade == 'F' || e.grade == 'f' || e.grade > 3){
                    count = count + 1;
                }
            });
            return count;
        },
        datares() {
            return this.$page.props.flash.datares;
        },
    },

    methods: {
        uploadFieldChange(e) {
            e.preventDefault();
            var files = e.target.files || e.dataTransfer.files;

            if (!files.length)
                return;
            for (var i = files.length - 1; i >= 0; i--) {
                this.attachments.push(files[i]);
            }
        },

        save() {
            this.type = 'save';
            let data = new FormData();
            if(this.attachments.length > 0){
                for (var i = this.attachments.length - 1; i >= 0; i--) {
                    data.append('files[]', this.attachments[i]);
                }
            }else{
                data.append('files[]', []);
            }
            data.append('enrollment_id', (this.selected != undefined) ? this.selected.id : '');
            data.append('scholar_id', (this.user != undefined) ? this.user : '');
            data.append('lists', (this.lists.length != 0) ? JSON.stringify(this.lists) : '');
            data.append('file_type', 'grade');
            data.append('type', 'grade');

            this.$refs.confirm.set(data);

            // this.$inertia.post('/enrollments',data,{
            //     preserveScroll: true,
            //     forceFormData: true,
            //     onSuccess: (response) => {
            //         this.attachments = [];
            //         this.$refs.fileupload.value=null;
            //     }
            // });
        },
        locked(id){
            this.type = 'lock';
            this.$refs.lock.set(id);
        },
        message(list){
            this.selected.is_locked = list.is_locked;
            this.selected.is_clear = list.is_clear;
            if(this.type == 'lock'){
                this.$emit('status', list);  
            }
        },
        back(){
            this.$parent.set('prospectus');
        },
        set(lists){
            this.lists = lists;
        }
    }
}
</script>
<style>
    .todo-content {
        height: calc(100vh - 280px);
    }
    .multiselect__single {
        font-size: 12px;
    }
</style>