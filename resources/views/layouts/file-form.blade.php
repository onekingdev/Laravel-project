<div class="container is-fluid box">
    <div class="new-file">
        <form id="new-file-form" action="#" method="#" @submit.prevent="submitForm">
            <div class="field is-grouped">
                <div class="file is-info has-name">
                    <label class="file-label">
                        <input class="file-input" 
                            type="file" 
                            name="files" 
                            @change="addFile($event)"
                            multiple>
                        <span class="file-cta">
                            <span class="file-icon">
                                <i class="fa fa-upload"></i>
                            </span>
                            <span class="file-label">
                                Upload new file
                            </span>
                        </span>
                        <!-- <span class="file-name" v-if="attachment.name" v-html="attachment.name"></span> -->
                        <!-- <span class="file-name" v-if="form.files.length" v-for="(item, index) in form.files" :key="index" v-html="item.name"></span> -->
                    </label>
                </div>
                <p class="control">
                    <button type="submit" class="button is-primary">
                        Add new file
                    </button>
                </p>
                <p class="control is-expanded">
                    <input class="input" 
                        type="text" 
                        placeholder="File name" 
                        v-for="(item, index) in form.filenames" 
                        :key="index" 
                        v-model="form.filenames[index]" 
                        required>
                </p>
            </div>
        </form>
    </div>
</div>
