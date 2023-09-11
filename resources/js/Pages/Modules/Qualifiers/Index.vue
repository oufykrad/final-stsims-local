<template>

    <Head title="Qualifiers" />
    <PageHeader :title="title" :items="items" />
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
        <div class="file-manager-sidebar">
            
            <div class="p-4 d-flex flex-column">
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-light text-primary rounded-circle fs-3">
                            <i :class="`bx ${item.icon} align-middle`"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">
                            {{item.year}} {{ item.name }}
                        </p>
                        <h4 class="mb-0">
                            <span class="counter-value">
                            {{ item.current }}
                            </span>
                        </h4>
                    </div>
                    <div class="flex-shrink-0 align-self-end">
                        <apexchart class="apex-charts" height="40" width="100" type="area" dir="ltr" :series="item.series" :options="chartOptions"></apexchart>
                    </div>
                </div>
                <hr class="text-muted mt-n1 mb-3"/>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm table-centered align-middle table-nowrap">
                        <tbody class="border-0">
                            <tr v-for="(count,index) in item.statistics" v-bind:key="index">
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
                <hr class="text-muted mt-n1 mb-3"/>
                 <div class="table-responsive">
                    <table class="table table-borderless table-sm table-centered align-middle table-nowrap">
                        <tbody class="border-0">
                            <tr v-for="(count,index) in item.statistics2" v-bind:key="index">
                                <td>
                                    <h4 class="text-truncate fs-14 fs-medium mb-0"><i class="ri-stop-fill align-middle fs-18 me-2" :class="colors2[index]"></i>{{options2[index]}}</h4>
                                </td>
                                <td class="text-end">
                                    <p class="fw-bold mb-0" :class="colors2[index]">{{count}}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <hr class="text-muted mt-n1 mb-3"/>
                <h6 v-if="item.endorsements" class="fw-semibold text-primary">ENDORSEMENTS (<span class="text-danger">{{item.endorsements.length}}</span>):</h6>
                <div data-simplebar :style="'height: '+hayt+'px'">
                    <div class="p-3">

                    <template v-for="(item, index) of item.endorsements" :key="index">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1 ms-n2">
                                <h6 class="fs-14 mb-0">{{ item.name }}</h6>
                                <p class="text-muted fs-12 mb-0">
                                <i class="ri-map-pin-fill text-primary fs-12 align-middle"></i>
                                {{ item.endorser.name}}, {{item.endorser.region }}
                                </p>
                            </div>
                            <div class="text-end">
                               <button @click="viewendorse(item)" class="btn btn-sm btn-soft-secondary mt-n2 me-n2"><i class="ri-eye-fill"></i></button>
                            </div>
                        </div>
                    </template>
                    </div>
                </div>
            </div>
        </div>
        <div class="file-manager-content w-100 p-4 pb-0" style="height: calc(100vh - 180px)" ref="box">
            <b-row class="g-2 mb-2 mt-n1">
                <b-col lg>
                    <div class="input-group mb-1">
                        <span class="input-group-text"> <i class="ri-search-line search-icon"></i></span>
                        <input type="text" v-model="keyword" placeholder="Search scholar" class="form-control" style="width: 30%;">
                        <select v-model="program" @change="fetch()" class="form-select" id="inputGroupSelect01" style="width: 120px;">
                            <option :value="null" selected>Select Program</option>
                            <option :value="list.id" v-for="list in program_list" v-bind:key="list.id">{{list.name}}</option>
                        </select>
                         <select v-model="type" @change="fetch()" class="form-select" id="inputGroupSelect02" style="width: 120px;">
                            <option :value="null" selected>Select Type</option>
                            <option :value="list.id" v-for="list in listtypes" v-bind:key="list.id">{{list.name}}</option>
                        </select>
                        <select v-model="status" @change="fetch()" class="form-select" id="inputGroupSelect02" style="width: 120px;">
                            <option :value="null" selected>Select Status</option>
                            <option :value="list.id" v-for="list in liststatuses" v-bind:key="list.id">{{list.name}}</option>
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
                            <th style="width: 25%;">Name</th>
                            <!-- <th style="width: 20%;" class="text-center">High School</th> -->
                            <th style="width: 25%;" class="text-center">Address</th>
                            <th style="width: 15%;" class="text-center">Program</th>
                            <th style="width: 15%;" class="text-center">Awarded Year</th>
                            <th style="width: 10%;" class="text-center">Type</th>
                            <th style="width: 10%;" class="text-center">Status</th>
                            <th style="width: 10%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="list in lists" v-bind:key="list.id" :class="[(list.is_active == 0) ? 'table-warnings' : '']">
                                <td>
                                <div class="avatar-xs" v-if="list.profile.avatar == 'n/a'">
                                    <span class="avatar-title rounded-circle">{{list.profile.lastname.charAt(0)}}</span>
                                </div>
                                <div v-else>
                                    <img class="rounded-circle avatar-xs" :src="currentUrl+'/images/avatars/'+list.profile.avatar" alt="">
                                </div>
                            </td>
                            <td>
                                <h5 class="fs-13 mb-0 text-dark">{{list.profile.lastname}}, {{list.profile.firstname}} {{list.profile.middlename[0]}}.</h5>
                                <p class="fs-11 text-muted mb-0">{{list.spas_id }}</p>
                            </td>
                            <!-- <td class="text-center fs-12">
                                {{list.address.hs_school}}
                            </td> -->
                                <td class="text-center">
                                <h5 class="fs-11 mb-0 text-dark">{{list.address.name}}</h5>
                                <p class="fs-11 text-muted mb-0">
                                    {{(list.address.province) ? list.address.province.name+',' : ''}}
                                    {{(list.address.region) ? list.address.region.region : ''}}
                                </p>
                            </td>
                            <td class="text-center">
                                <h5 class="fs-12 mb-0 text-dark">{{list.program.name}}</h5>
                                <p class="fs-11 text-muted mb-0">{{list.subprogram.name }}</p>
                            </td>
                            <td class="text-center">{{list.qualified_year}}</td>
                                <td class="text-center">
                                <span :class="'badge '+list.type.color+' '+list.type.others">{{list.type.name}}</span>
                            </td>
                            <td class="text-center">
                                <span :class="'badge '+list.status.color+' '+list.status.others">{{list.status.name}}</span>
                            </td>
                            <td class="text-end">
                                <b-button v-if="list.status.name != 'Complete'" variant="soft-primary" @click="endorse(list)" v-b-tooltip.hover title="Endorse" size="sm" class="edit-list me-1"><i class="ri-swap-fill align-bottom"></i> </b-button>
                                <b-button v-if="list.type.name != 'Enrolled'" @click="add(list)" variant="soft-primary" v-b-tooltip.hover title="Add Scholar" size="sm" class="edit-list me-1"><i class="ri-user-add-fill align-bottom"></i> </b-button>
                                <b-button v-if="list.address.is_completed == 0" @click="update(list)" variant="soft-danger" v-b-tooltip.hover title="Update Address" size="sm" class="remove-list me-1"><i class="ri-map-pin-fill align-bottom"></i></b-button>
                                <b-button variant="soft-primary" v-b-tooltip.hover title="Edit" size="sm" class="edit-list"><i class="ri-pencil-fill align-bottom"></i> </b-button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Pagination class="ms-2 me-2" v-if="meta" @fetch="extractPageNumber" :lists="lists.length" :links="links" :pagination="meta" />
            </div>
        </div>
    </div>
    <Add ref="add"/>
    <Endorse ref="endorse"/>
    <ViewEndorse ref="viewendorse"/>
</template>
<script>
import Add from './Modals/Add.vue';
import Endorse from './Modals/Endorse';
import ViewEndorse from './Modals/ViewEndorse.vue';
import Pagination from "@/Shared/Components/Pagination.vue";
import PageHeader from "@/Shared/Components/PageHeader.vue";
export default {
    components: { PageHeader, Pagination, Add, Endorse, ViewEndorse },
    props: ['statuses','programs'],
    data() {
        return {
            currentUrl: window.location.origin,
            hayt: (window.innerHeight-630),
            title: "List of Qualifiers",
            items: [{text: "List",href: "/"},{text: "Qualifier",active: true}],
            options: ['Waiting','Deferment','Not Avail','Enrolled'],
            options2: ['Completed','Lacking','Potential'],
            colors2: ['text-success','text-warning','text-danger'],
            colors: ['text-warning','text-danger','text-dark','text-success'],
            lists: [],
            meta: {},
            links: {},
            keyword: '',
            program: null,
            subprogram: null,
            status: null,
            type: null,
            year: null,
            sorty: 'asc',
            arr: {},
            pageNo: '',
            item: { series: []},
            chartOptions: {
                chart: {
                    type: 'area',
                    height: 40,
                    sparkline: {
                        enabled: true
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                dataLabels: {
                    enabled: false
                },
                colors: ['#f1b44c'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        inverseColors: false,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [25, 100, 100, 100]
                    },
                },
                tooltip: {
                    fixed: {
                        enabled: false
                    },
                    x: {
                        show: true
                    },
                    marker: {
                        show: false
                    }
                }
            },
        };
    },
    created(){
        this.fetch();
        this.fetchInsights();
    },
    watch: {
        keyword(newVal){
            this.checkSearchStr(newVal)
        }
    },
     computed: {
        listprograms : function() {
            return this.programs.filter(x => x.is_sub === 1);
        },
        listsubprograms() {
            return this.programs;
        },
        listtypes : function() {
            return this.statuses.filter(x => x.type === 'Qualifier');
        },
        liststatuses : function() {
            return this.statuses.filter(x => x.type === 'Qualifier Status');
        }
    },
    methods: {
        checkSearchStr: _.debounce(function(string) {
            this.fetch();
        }, 500),
        fetch(page_url) {
            let info = {
                'keyword' : this.keyword,
                'type' : (this.type ==  null) ? null : this.type, 
                'status' : (this.status ==  null) ? null : this.status, 
                'program' : (this.program ==  null) ? null : this.program, 
                'subprogram' : (this.subprogram ==  null) ? null : this.subprogram,
                'counts' : this.count2,
                'sorty' : this.sorty
            };

            info = (Object.keys(info).length == 0) ? '-' : JSON.stringify(info);
            let location = (Object.keys(this.arr).length == 0) ? '-' : JSON.stringify(this.arr);

            page_url = page_url || 'qualifiers';
            axios.get(page_url, {
                params: {
                    type: 'lists',
                    counts: ((window.innerHeight-350)/56),
                    info : info,
                    location: location,
                    page: this.pageNo
                }
            })
            .then(response => {
                this.lists = response.data.data;
                this.meta = response.data.meta;
                this.links = response.data.links;
            })
            .catch(err => console.log(err));
        },
        fetchInsights() {
            axios.get(this.currentUrl + '/qualifiers',{
                params: { type: 'insights' }
            })
            .then(response => {
                this.item = response.data;
            })
            .catch(err => console.log(err));
        },
        add(data){
            this.$refs.add.show(data);
        },
        extractPageNumber(page_url) {
            this.pageNo = null;
            const nextUrl = page_url;
            const regex = /page=(\d+)/;
            const match = nextUrl.match(regex);

            if (match) {
                this.pageNo = match[1];
            } else {
                this.pageNo = null;
            }
            this.fetch();
        },
        endorse(data){
            this.$refs.endorse.show(data);
        },
        viewendorse(data){
            this.$refs.viewendorse.show(data);
        },
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
