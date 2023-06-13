<template>
    <b-row>
        <b-col xxl="12">

            <b-row class="g-1 mb-2">
                <b-card no-body class="card-height-100">
                    <b-card-body>
                        <div class="p-3 mt-n3 mx-n3 bg-soft-info rounded-top">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fs-14"><a class="text-dark">{{ latest.month }}</a></h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="d-flex gap-1 align-items-center my-n2">
                                        <button class="btn btn-transparent btn-md btn avatar-xs p-0 favourite-btn active" type="button">
                                            <div class="btn-content">
                                                <span class="avatar-title bg-transparent fs-15">
                                                    <i class="ri-star-fill"></i>
                                                </span>
                                            </div>
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex mt-2 align-items-center">
                            <div class="flex-grow-1">
                                <div class="avatar-group">
                                    <b-link v-for="(list, index) of latest.scholars" :key="index" href="javascript: void(0);" class="avatar-group-item" v-b-tooltip.hover :title="list.name">
                                        <div class="avatar-xxs" v-if="list.avatar != 'avatar.jpg'">
                                            <img :src="currentUrl+'/images/avatars/'+list.avatar" alt="" class="rounded-circle img-fluid" />
                                        </div>
                                        <div class="avatar-xxs" v-else>
                                            <div class="avatar-title fs-16 rounded-circle bg-primary border-dashed border text-white">
                                                {{ list.name[0] }}
                                            </div>
                                        </div>
                                    </b-link>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="text-muted">
                                    <i class="ri-account-circle-fill  me-1 align-bottom"></i> {{latest.scholars.length}} Scholars
                                </div>
                            </div>
                        </div>
                    </b-card-body>
                </b-card>
                 <b-col lg="12" class="mt-n1">
                    <button @click="newRelease" class="btn btn-primary w-100" type="button">
                        <div class="btn-content">Generate </div>
                    </button>
                    <hr class="mt-3 mb-3"/>   
                </b-col>
                <b-col lg="6" class="mt-0">
                    <button @click="reimburse()" class="btn btn-light w-100" type="button">
                        <div class="btn-content"> Reimbursement </div>
                    </button>
                </b-col>
                <b-col lg="6" class="mt-0">
                    <button class="btn btn-light w-100" type="button">
                        <div class="btn-content"> Payee </div>
                    </button>
                </b-col>
            </b-row>
            
            <hr class="mt-3"/>
            
        </b-col>
    </b-row>
    <Reimbursement :privileges="privileges" ref="reimbursement" @info="update"/>
</template>
<script>
import Reimbursement from './Modals/Reimbursement.vue';
export default {
    components: { Reimbursement },
    props: ['privileges'],
    data(){
        return {
            currentUrl: window.location.origin,
            latest: { pending: [], scholars: [] },
        }
    },
    methods: {
        set(data){
            this.latest = data;
        },
        newRelease() {
           this.$emit('info',true);
        },
        reimburse(){
            this.$refs.reimbursement.set();
        },
        update(){
            this.$emit('update',true);
        }
    }
}
</script>