<template>
    <h6 class="fs-11 text-muted text-uppercase mb-3">Ongoing Scholars</h6>
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0">
            <i class="ri-team-fill text-primary fs-17"></i>
        </div>
        <div class="flex-grow-1 ms-3 overflow-hidden">
            <div class="progress mb-2 progress-sm">
                <div class="progress-bar bg-primary" role="progressbar" :style="'width: '+(counts.enrolled.length/counts.scholars)*100+'%'" :aria-valuenow="(counts.enrolled.length/counts.scholars)*100"
                aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <span class="text-muted fs-12 d-block text-truncate"><b>{{counts.scholars}}</b> out of <b>{{counts.total}}</b> are ongoing scholars.</span>
        </div>
    </div>

    <hr class="text-muted"/>

    <h6 class="fs-11 text-muted text-uppercase mb-3">Scholars Enrolled</h6>
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0">
            <i class="ri-group-2-fill text-primary fs-17"></i>
        </div>
        <div class="flex-grow-1 ms-3 overflow-hidden">
            <div class="progress mb-2 progress-sm">
                <div class="progress-bar bg-primary" role="progressbar" :style="'width: '+(counts.enrolled.length/counts.scholars)*100+'%'" :aria-valuenow="(counts.enrolled.length/counts.scholars)*100"
                aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <span class="text-muted fs-12 d-block text-truncate"><b>{{counts.enrolled.length}}</b> out of <b>{{counts.scholars}}</b> ongoing scholars are enrolled.</span>
        </div>
    </div>

    <hr class="text-muted"/>

    <h6 class="fs-11 text-muted text-uppercase mb-3">Schools Semester</h6>
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0">
            <i class="ri-hotel-line text-primary fs-17"></i>
        </div>
        <div class="flex-grow-1 ms-3 overflow-hidden">
            <div class="progress mb-2 progress-sm">
                <div class="progress-bar bg-primary" role="progressbar" :style="'width: '+(counts.semesters.length/counts.schools)*100+'%'" :aria-valuenow="(counts.semesters.length/counts.schools)*100"
                aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <span class="text-muted fs-12 d-block text-truncate"><b>{{counts.semesters.length}}</b> out of <b>{{counts.schools}}</b> schools with active semester.</span>
        </div>
    </div>

    <hr class="text-muted"/>

    <div class="mt-2">
        <ul class="list-unstyled vstack gap-3">
            <li>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-xs">
                            <div class="avatar-title rounded bg-soft-secondary text-secondary">
                            <i class="ri-file-text-line fs-17"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-0 fs-13">Lacking Grades</h5>
                        <p class="mb-0 fs-12 text-muted">2348 files</p>
                    </div>
                    <div class="avatar-group"><div class="avatar-group-item"><a class="d-inline-block" href="javascript: void(0);" target="_self" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stine Nielsen"><img src="/img/avatar-1.7e66bd07.jpg" alt="" class="rounded-circle avatar-xxs"></a></div><div class="avatar-group-item"><a class="d-inline-block" href="javascript: void(0);" target="_self" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stine Nielsen"><img src="/img/avatar-2.0c4e66a9.jpg" alt="" class="rounded-circle avatar-xxs"></a></div><div class="avatar-group-item"><a class="d-inline-block" href="javascript: void(0);" target="_self" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stine Nielsen"><img src="/img/avatar-3.910af76d.jpg" alt="" class="rounded-circle avatar-xxs"></a></div><div class="avatar-group-item"><a class="" href="javascript: void(0);" target="_self"><div class="avatar-xxs"><span class="avatar-title rounded-circle bg-info text-white">5</span></div></a></div></div>
                </div>
            </li>
            <li>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-xs">
                            <div class="avatar-title rounded bg-soft-success text-success">
                            <i class="ri-gallery-line fs-17"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-0 fs-13">Unreleased Benefits</h5>
                        <p class="mb-0 fs-12 text-muted">12480 files</p>
                    </div>
                    <div class="avatar-group"><div class="avatar-group-item"><a class="d-inline-block" href="javascript: void(0);" target="_self" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stine Nielsen"><img src="/img/avatar-1.7e66bd07.jpg" alt="" class="rounded-circle avatar-xxs"></a></div><div class="avatar-group-item"><a class="d-inline-block" href="javascript: void(0);" target="_self" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stine Nielsen"><img src="/img/avatar-2.0c4e66a9.jpg" alt="" class="rounded-circle avatar-xxs"></a></div><div class="avatar-group-item"><a class="d-inline-block" href="javascript: void(0);" target="_self" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stine Nielsen"><img src="/img/avatar-3.910af76d.jpg" alt="" class="rounded-circle avatar-xxs"></a></div><div class="avatar-group-item"><a class="" href="javascript: void(0);" target="_self"><div class="avatar-xxs"><span class="avatar-title rounded-circle bg-info text-white">5</span></div></a></div></div>
                </div>
            </li>
             <li>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-xs">
                            <div class="avatar-title rounded bg-soft-danger text-danger">
                            <i class="ri-gallery-line fs-17"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-0 fs-13">Missed Enrollment</h5>
                        <p class="mb-0 fs-12 text-muted">12480 files</p>
                    </div>
                    <div class="avatar-group"><div class="avatar-group-item"><a class="d-inline-block" href="javascript: void(0);" target="_self" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stine Nielsen"><img src="/img/avatar-1.7e66bd07.jpg" alt="" class="rounded-circle avatar-xxs"></a></div><div class="avatar-group-item"><a class="d-inline-block" href="javascript: void(0);" target="_self" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stine Nielsen"><img src="/img/avatar-2.0c4e66a9.jpg" alt="" class="rounded-circle avatar-xxs"></a></div><div class="avatar-group-item"><a class="d-inline-block" href="javascript: void(0);" target="_self" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stine Nielsen"><img src="/img/avatar-3.910af76d.jpg" alt="" class="rounded-circle avatar-xxs"></a></div><div class="avatar-group-item"><a class="" href="javascript: void(0);" target="_self"><div class="avatar-xxs"><span class="avatar-title rounded-circle bg-info text-white">5</span></div></a></div></div>
                </div>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    data(){
        return {
            currentUrl: window.location.origin,
            counts: { semesters: [], enrolled: []},
        }
    },
    created(){
        this.fetch();
    },
    methods: {
        fetch(){
            axios.get(this.currentUrl+'/monitoring', {
                params: {
                    type: 'counts'
                }
            })
            .then(response => {
                this.counts = response.data;
            })
            .catch(err => console.log(err));
        },
    }
}
</script>