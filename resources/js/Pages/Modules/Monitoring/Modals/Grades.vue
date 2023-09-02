<template>
    <b-modal v-model="showModal" title="Grades" style="--vz-modal-width: 60%;" hide-footer header-class="p-3 bg-light" class="v-modal-custom" modal-class="zoomIn" centered>    
             
    </b-modal>
</template>

<script>
    export default {
        data() {
            return {
                currentUrl: window.location.origin,
                showModal: false,
                lists: '',
            }
        },
        methods: {
            show(data) {
                this.lists = data;
                this.showModal = true;
            },
        }
    }
</script>
