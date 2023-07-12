<template>
    <Head title="Monitoring" />
    <PageHeader :title="title" :items="items" />
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
        <div class="file-manager-sidebar">
            <div class="p-4 d-flex flex-column h-100">
                <Sidebar :semester_year="semester_year"/>
            </div>
         </div>
        <div class="file-manager-content w-100 p-3 pb-0" style="height: calc(100vh - 180px)" ref="box">
            <div class="row mb-n3">
                <div @click="updateStatus(s.id)" class="col-md-3" v-for="(s,i) in statuses" v-bind:key="s.id" style="cursor: pointer;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-light text-primary rounded-circle fs-3">
                                        <i :class="icons[i]" class="align-middle"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">{{s.name}}</p>
                                    <h4 class="mb-0"><span class="counter-value">{{s.status_count}}</span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <b-row class="g-2 mb-3 mt-1">
                <b-col lg>
                    <div class="input-group mb-1">
                        <span class="input-group-text"> <i class="ri-search-line search-icon"></i></span>
                        <input type="text" v-model="keyword" placeholder="Search scholar" class="form-control" style="width: 30%;">
                        <select v-model="program" @change="fetch()" class="form-select" id="inputGroupSelect01" style="width: 120px;">
                            <option :value="null" selected>Select Program</option>
                            <option :value="list.id" v-for="list in program_list" v-bind:key="list.id">{{list.name}}</option>
                        </select>
                        <span @click="refresh" class="input-group-text" v-b-tooltip.hover title="Refresh" style="cursor: pointer;"> 
                            <i class="bx bx-refresh search-icon"></i>
                        </span>
                        <b-button type="button" variant="primary" @click="show()">
                            <i class="ri-filter-fill align-bottom me-1"></i> Filter
                        </b-button>
                    </div>
                </b-col>
            </b-row>
            <div class="table-responsive">
                <table class="table table-nowrap align-middle mb-0">
                    <thead class="table-light">
                        <tr class="fs-11">
                            <th></th>
                            <th style="width: 30%;">Name</th>
                            <th style="width: 15%;" class="text-center">Benefits</th>
                            <th style="width: 15%;" class="text-center">Enrollments</th>
                            <th style="width: 15%;" class="text-center">Awarded Year</th>
                            <th style="width: 15%;" class="text-center">Status</th>
                            <th style="width: 10%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in lists" v-bind:key="user.id" :class="[(user.is_completed == 0) ? 'table-warnings' : '']">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 chat-user-img online user-own-img align-self-center me-3 ms-0">
                                        <img :src="currentUrl+'/images/avatars/'+user.profile.avatar" class="rounded-circle avatar-xs" alt="">
                                        <span class="user-status" :style="(user.profile.sex == 'Male') ? 'background-color: #5cb0e5;' : 'background-color: #e55c7f;'"></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <h5 class="fs-13 mb-0 text-dark">{{user.profile.name}}</h5>
                                <p class="fs-12 text-muted mb-0">{{user.spas_id}}</p>
                            </td>
                            <td class="text-center">
                                test 
                            </td>
                            <td class="text-center">{{user.program}}</td>
                            <td class="text-center">{{user.awarded_year}}</td>
                            <td class="text-center">
                                <span :class="'badge '+user.status.color+' '+user.status.others">{{user.status.name}}</span>
                            </td>
                            <td class="text-end">
                                <b-button v-if="user.user == null" @click="authenticate(user)" variant="soft-primary" v-b-tooltip.hover title="Create Scholar Account" size="sm" class="edit-list me-1"><i class="ri-user-add-fill align-bottom"></i> </b-button>
                                <b-button v-if="user.account_no == null && user.status.type == 'Ongoing'" @click="update(user,'account_no')" variant="soft-danger" v-b-tooltip.hover title="Update Account No." size="sm" class="remove-list me-1"><i class="ri-bank-card-2-fill align-bottom"></i></b-button>
                                <b-button v-if="user.education.is_completed == 0" @click="update(user,'education')" variant="soft-danger" v-b-tooltip.hover title="Update Education" size="sm" class="remove-list me-1"><i class="ri-hotel-fill align-bottom"></i></b-button>
                                <b-button v-if="user.addresses[0].is_completed == 0" @click="update(user,'address')" variant="soft-danger" v-b-tooltip.hover title="Update Address" size="sm" class="remove-list me-1"><i class="ri-map-pin-fill align-bottom"></i></b-button>
                                <Link v-if="user.is_completed == 1" :href="`/scholars/${user.code}`"><b-button variant="soft-info" v-b-tooltip.hover title="View" size="sm" class="remove-list me-1"><i class="ri-eye-fill align-bottom"></i></b-button></Link>
                                <!-- <b-button variant="soft-primary" v-b-tooltip.hover title="Edit" size="sm" class="edit-list"><i class="ri-pencil-fill align-bottom"></i> </b-button> -->
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Pagination class="ms-2 me-2" @fetch="fetch" :lists="lists.length" :links="links" :pagination="meta" />
            </div>
        </div>
        <!-- <div class="file-manager-sidebar">
            <div class="p-4 d-flex flex-column h-100">
               
                        
            </div>
         </div> -->
    </div>
</template>
<script>
import Sidebar from './Sidebar.vue';
import PageHeader from "@/Shared/Components/PageHeader.vue";
import Pagination from "@/Shared/Components/Pagination.vue";
export default {
    components: { PageHeader, Sidebar, Pagination },
    props: ['semester_year'],
    data() {
        return {
            currentUrl: window.location.origin,
            title: "Monitoring",
            items: [{text: "Monitor", href: "/",},{text: "Dasboard",active: true,},],
            statuses: [],
            icons: ['ri-checkbox-circle-fill text-success','ri-question-line text-warning','ri-close-circle-fill text-danger','ri-error-warning-fill text-info'],
            lists: [],
            meta: {},
            links: {},
            status: null,
        };
    },
    created(){
        this.fetch();
        this.fetchScholars();
    },
    methods: {
        fetch(){
            axios.get(this.currentUrl+'/monitoring', {
                params: {
                    type: 'statuses'
                }
            })
            .then(response => {
                this.statuses = response.data;
            })
            .catch(err => console.log(err));
        },
        fetchScholars(page_url){
            page_url = page_url || '/scholars';
            axios.get(page_url, {
                params: {
                    type: 'ongoing',
                    counts: ((window.innerHeight-450)/56),
                    status: this.status
                }
            })
            .then(response => {
                this.lists = response.data.data;
                this.meta = response.data.meta;
                this.links = response.data.links;
            })
            .catch(err => console.log(err));
        },
        updateStatus(status){
            this.status = status;
            this.fetchScholars();
        }
    }
}
</script>
<style>
    .file-manager-sidebar {
        min-width: 450px;
        max-width: 450px;
        height: calc(100vh - 180px);
    }
</style>