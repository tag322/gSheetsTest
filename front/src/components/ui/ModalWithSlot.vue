<template>
    <div class="modal fade" :id="modalName" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <slot name="header"> 
                    <!-- root elemnt should be div with class modal-header, use RowCreationForm as exmaple -->
                </slot>
                <slot name="body"> 
                    <!-- root elemnt should be div with class modal-body, use RowCreationForm as exmaple -->
                </slot>
                <slot name="footer"> 
                    <!-- root elemnt should be div with class modal-footer, use RowCreationForm as exmaple -->
                </slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        modalName: {
            type: String,
            required: true
        }
    }
}
</script>

<style></style>