<template>
    <b-modal v-model="showModal" title="Switch Prospectus" header-class="p-3 bg-light" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop>    
        <div class="modal-body">
           
        </div>
        <template v-slot:footer>
            <b-button @click="hide()" variant="light" block>Cancel</b-button>
            <b-button @click="switcha()" variant="primary" block>Switch</b-button>
        </template>
    </b-modal>
</template>
<script>
export default {
    data(){
        return {
            showModal: false,
            form: {}
        }
    },
    methods: {
        show(){
            this.showModal = true;
        },
        switcha(){

        },
        hide(){
            this.showModal = false;
        }
    }
}
</script>