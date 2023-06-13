<template>
    <Head title="Monitoring" />
    <PageHeader :title="title" :items="items" />
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
        <div class="file-manager-sidebar">
            <div class="p-4 d-flex flex-column h-100">
                <Sidebar />
            </div>
         </div>
        <div class="file-manager-content w-100 p-3 pb-0" style="height: calc(100vh - 180px)" ref="box">
            <div class="row mb-n3">
                <div class="col-md-3" v-for="(s,i) in statuses" v-bind:key="s.id">
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
export default {
    components: { PageHeader, Sidebar },
    data() {
        return {
            currentUrl: window.location.origin,
            title: "Monitoring",
            items: [{text: "Monitor", href: "/",},{text: "Dasboard",active: true,},],
            statuses: [],
            icons: ['ri-checkbox-circle-fill text-success','ri-question-line text-warning','ri-close-circle-fill text-danger','ri-error-warning-fill text-info']
        };
    },
    created(){
        this.fetch();
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