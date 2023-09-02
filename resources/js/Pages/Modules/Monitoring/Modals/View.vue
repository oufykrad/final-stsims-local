<template>
    <b-modal v-model="showModal" hide-footer class="v-modal-custom" modal-class="zoomIn" fullscreen>
        <template v-slot:header>
            <h5 class="modal-title text-primary">{{user.profile.name}}</h5>
            <!-- <button @click="showModal=false" class="float-end btn btn-danger btn-sm" type="button">
                <div class="btn-content">Close</div>
            </button> -->
            <i @click="showModal=false" class="ri-close-circle-fill float-end" style="cursor:pointer; font-size: 40px; margin-top: -10px; margin-bottom: -20px;"></i>
        </template>
        <div class="hstack gap-3 flex-wrap mt-n3 mb-n2">
            <div class="text-muted">Spas ID :
                <span class="text-body fw-medium">{{user.spas_id}}</span>
            </div>
            <div class="vr"></div>
            <div class="text-muted">Program : 
                <span class="text-body fw-medium">{{user.program}}</span>
            </div>
            <div class="vr"></div>
            <div class="text-muted">Subprogram : 
                <span class="text-body fw-medium">{{user.subprogram}}</span>
            </div>
            <div class="vr"></div>
            <div class="text-muted">Awarded Year : 
                <span class="text-body fw-medium">{{user.awarded_year}}</span>
            </div>
        </div>    
       
        <div class="row">
            <div class="col-md-12"><hr /></div>
            <div class="col-md-4">
                <!-- <ul class="list-unstyled mb-0 vstack gap-3">
                
                    <li><i class="ri-building-line me-2 align-middle text-primary fs-16"></i><span class="fs-12">{{user.education.school.name}}</span></li>
                    <li class="mt-n3"><i class="mdi mdi-school-outline me-2 align-middle text-primary fs-16"></i><span class="fs-12">{{user.education.course.name}}</span></li>
                    <li class="mt-n3"><i class="ri-map-pin-fill me-2 align-middle text-primary fs-16"></i><span class="fs-12">{{user.addresses[0]}}</span></li>
                </ul> -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex mt-1">
                            <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                                <div class="avatar-title bg-light rounded-circle fs-16 text-primary">
                                    <i class="ri-service-line"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-1">Status :</p> <span :class="'badge '+user.status.color+' '+user.status.others">{{user.status.name}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex mt-4">
                            <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                                <div class="avatar-title bg-light rounded-circle fs-16 text-primary">
                                    <i class="ri-building-line"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-1">School :</p><h6 class="text-truncate mb-0"> {{user.education.school.name}} </h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex mt-4">
                            <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                                <div class="avatar-title bg-light rounded-circle fs-16 text-primary">
                                    <i class="mdi mdi-school-outline"></i></div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-1">Course :</p> <h6 class="text-truncate mb-0"> {{user.education.course.name}} </h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex mt-4">
                            <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                                <div class="avatar-title bg-light rounded-circle fs-16 text-primary">
                                    <i class="ri-calendar-line"></i></div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-1">Level :</p> <h6 class="text-truncate mb-0"> {{user.education.level.name}} <span class="text-muted fs-11">({{user.education.level.others}})</span></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row gy-3 mt-4">
                    <div class="col-sm-12" v-for="address in user.addresses" v-bind:key="address.id">
                        <div class="form-check card-radio">
                            <label class="form-check-label" for="shippingAddress01">
                                <span v-if="address.is_permanent" class="mb-2 fw-semibold d-block text-muted text-uppercase">Permanent Address</span>
                                <span v-else class="mb-2 fw-semibold d-block text-muted text-uppercase">Home Address</span>
                                <span class="text-muted fw-normal text-wrap mb-1 d-block">{{address.address}}, {{address.municipality.name}}</span>
                                <span class="text-muted fw-normal d-block">{{address.province.name}}, {{address.region.name}}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="col-md-8">
                <b-row class="mt-0">
                    <div class="col-md-6">
                        <div class="page-title-left mt-2">
                            <ol class="breadcrumb m-0 fs-15">
                                <li class="breadcrumb-item fw-bold">test</li>
                                <li class="breadcrumb-item active"><span>adsad</span></li>
                                <li class="breadcrumb-item active"><span class="fw-bold text-primary">dasdas</span></li>
                            </ol>
                        </div> 
                    </div>
                    <div class="col-md-6">
                        <div class="hstack float-end gap-2 mt-4 mt-sm-0">
                            <button @click="viewGrades()" class="btn btn-light btn-sm btn-label" type="button">
                                <div class="btn-content"><i class="ri-list-check label-icon align-middle fs-12 me-2"></i>Full Grades</div>
                            </button>
                            <button @click="viewFull()" class="btn btn-light btn-sm btn-label" type="button">
                                <div class="btn-content"><i class="ri-eye-line label-icon align-middle fs-12 me-2"></i>Full Financial Benefits</div>
                            </button>
                        </div>
                    </div>
                </b-row>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered align-middle table-centered table-nowrap mb-3">
                        <thead class="table-dark fs-10">
                            <tr>
                                <th class="text-center" width="20%" scope="col">Level</th>
                                <th class="text-center" width="18%" scope="col">Semester</th>
                                <th width="15%" class="text-center">Enrollments</th>
                                <th width="15%" class="text-center">Grades</th>
                                <th width="15%" class="text-center">Benefits (Stipends)</th>
                                <th width="17%" class="text-center">Total Financial Benefit</th>
                            </tr>
                        </thead>
                        <tbody class="fs-11" v-for="(p,index) in prospectuses" v-bind:key="index">
                            <tr>
                                <td style="cursor: pointer;" class="fw-bold text-center" width="20%" :rowspan="p.semesters.length+1">{{p.year_level}}</td>
                            </tr>
                            <tr v-for="(s,index2) in p.semesters" v-bind:key="index2" rowspan="6">
                                <td width="15%" class="text-center">{{s.semester}}</td>
                                <td class="text-center" :class="(s.courses.length == 0) ? 'unselectable' : ''">
                                    <i v-if="s.is_enrolled" class="fs-15 ri-checkbox-circle-fill text-success"></i>
                                    <i v-else :class="(s.courses.length == 0) ? '' : 'fs-15 ri-close-circle-fill text-danger'"></i>
                                </td>
                                <td class="text-center" :class="(s.courses.length == 0) ? 'unselectable' : ''">
                                    {{(s.courses.length == 0) ? '' : 'testing'}}
                                </td>
                                <td class="text-center" :class="(s.courses.length == 0) ? 'unselectable' : ''">
                                    <!-- {{(s.courses.length == 0) ? '' : s.benefit_count+' of 5 released'}} -->
                                    <i v-if="s.benefit_count == s.benefit_counter" v-b-tooltip.hover :title="s.benefit_count+' of '+s.benefit_counter+' released'" class="fs-15 ri-checkbox-circle-fill text-success"></i>
                                    <i v-else-if="s.benefit_count > 0 && s.benefit_count < s.benefit_counter" v-b-tooltip.hover :title="s.benefit_count+' of '+s.benefit_counter+' released'" class="fs-15 ri-checkbox-circle-fill text-warning"></i>
                                    <i v-else :class="(s.courses.length == 0) ? '' : 'fs-15 ri-close-circle-fill text-danger'" v-b-tooltip.hover :title="s.benefit_count+' of 5 released'"></i>
                                </td>
                                <td class="text-center" :class="(s.courses.length == 0) ? 'unselectable' : ''">
                                    {{(s.courses.length == 0) ? '' : '₱ '+formatMoney(s.benefit_total)}}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                                <td class="text-center">12% Intereset</td>
                                <td class="text-center">₱ {{formatMoney(monitoring.total*.12)}}</td>
                            </tr>
                            <tr class="table-light fs-12 fw-semibold">
                                <td colspan="4"></td>
                                <td class="text-center">Total</td>
                                <td class="text-center">₱ {{formatMoney(monitoring.total)}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </b-modal>
    <Full ref="view"/>
    <Grades ref="grades"/>
</template>
<script>
import Full from './Fullview.vue';
import Grades from './Grades.vue'
export default {
    components : { Full, Grades },
    data(){
        return {
            currentUrl: window.location.origin,
            showModal: false,
            user: {
                profile: {},
                status: {},
                education: {
                    school: {},
                    course: {},
                    level: {},
                    info: {}
                },
                addresses: []
            },
            monitoring: {
                benefits: [],
                enrollments: []
            },
            prospectuses: [],
            enrollment: 0,
            listE: []
        }
    },
    methods: {
        show(user){
            this.user = user;
            this.prospectuses = this.user.education.info.prospectus;
            this.fetch();
            this.showModal = true;
        },
        hide(){
            this.showModal = false;
        },
        fetch(){
            axios.get(this.currentUrl+'/monitoring', {
                params: {
                    id: this.user.id,
                    type: 'monitoring'
                }
            })
            .then(response => {
                this.monitoring = response.data.data;

                this.prospectuses.map((list) => {
                    list.semesters.map((semester) => {
                        var a = [];
                        var a = this.monitoring.enrollments.map((enrollment) => {
                           return (semester.semester == enrollment.semester.semester.name) ? (list.year_level == enrollment.level.others) ? true : false : false;
                        });

                        const count = a.filter(Boolean).length;
                        (count > 0) ? this.listE.push(true) : this.listE.push(false);
                        semester['is_enrolled'] = ((count > 0) ? true : false);

                        var count1 = 0; var total = 0;
                        this.monitoring.enrollments.map((enrollment) => {
                            if (semester.semester == enrollment.semester.semester.name) {
                                if (list.year_level == enrollment.level.others) {
                                    enrollment.semester.benefits.map((benefit) => {
                                        if (1 == benefit.benefit_id) {
                                            count1++;
                                        }
                                        total = parseInt(total) + parseInt(benefit.amount);
                                    });
                                }
                            }
                            semester['benefit_counter'] = (enrollment.semester.semester.others == 'regular') ? 5 : 2;
                        });
                        semester['benefit_count'] = count1;
                        semester['benefit_total'] = total;                    
                    });
                });
                
            })
            .catch(err => console.log(err));
        },
        formatMoney(value) {
            let val = (value/1).toFixed(2).replace(',', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },
        viewFull(){
            this.$refs.view.show(this.monitoring);
        },
        viewGrades(){
            this.$refs.grades.show(this.monitoring);
        }
    }
}
</script>
<style>
    table .unselectable {
        background-color: #f9f9f9;
        cursor: not-allowed;
    }
    .table td {
        padding: 0.55rem 0.55rem;
    }
</style>