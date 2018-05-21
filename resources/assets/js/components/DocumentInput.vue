<template>
    <div class="input-group my-2">
        <div class="custom-file">
            <input type="file" class="custom-file-input" v-on:change="updateFile" :id="'file' + file" name="file[]">
            <label class="custom-file-label" :for="'file' + file">Choose file</label>
        </div>
        <div class="input-group-append">
            <button class="btn btn-danger remove-document" v-on:click="removeButton" type="button" :id="'remove' + file">-</button>
        </div>
      </div>
</template>

<script>
    export default
    {
        props: ['file'],
        methods: {
            removeButton: function(event)
            {
                var regex = /remove(\d+)/;

                var id = parseInt(event.target.id.replace(regex, '$1'));

                appRegistration.fileList.splice(id,1);

                if($(".add-document").is(":hidden") && appRegistration.fileList.length < appRegistration.maxFileNum)
                {
                    $(".add-document").show();
                }
            },
            updateFile: function(event)
            {
                var fileName = event.target.files[0].name;
                $(event.target).next(".custom-file-label").html(fileName);
            }
        }
    }
</script>
