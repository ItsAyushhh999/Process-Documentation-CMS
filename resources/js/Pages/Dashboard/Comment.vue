<script setup>
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import AppEditor from "../../components/AppEditor.vue";

const props = defineProps({
    task : {
        type: Object,
        default: {}
    }
});

const selectedImage = ref();
const comment = ref('')

const selectFiles = (e) => {
    selectedImage.value = e.target.files;
};

const createComment = ()=> {
    console.log('create comment',)
    // action="{{route('tasks.comments.store')}}"

    const data = {
        "taskId": props.task.value.task.id,
        "comment": comment.value,
        "status": props.task.value.task.status,
        "attachments[]": selectedImage.value

    };

    console.log('data', data)

    const form = useForm({
        "taskId": props.task.value.task.id,
        "comment": comment.value,
        "status": props.task.value.task.status,
        "attachments[]": selectedImage.value
    });

    form.post(route('tasks.comments.store'), {
        onFinish: () => {
            console.log('success');
        }
    });
}
</script>

<template>
    <div  class="p-5 border dark:border-gray-600 border-primary rounded-lg bg-white dark:bg-neutral-800" >
        <form
            id="commentForm"
            @submit.prevent="createComment"
        >
            <x-label for="comment" required>Add Comment:</x-label>
            <!-- <textarea name="comment" data-quilljs placeholder="Please enter text"></textarea> -->
            <!-- <x-editor name="comment" id="comment" height="180px"> -->
            <AppEditor v-model="comment"/>

            <div class="grid">
                <span>{{ assignedStatusArr }}</span>
                <div class="">
                    <label
                        class="block text-sm font-medium text-gray-700 capitalize mb-1"
                    >Priority*</label
                    >
                    <select
                        v-model="taskDetail.task.status"
                        class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 text-primary-900 dark:text-gray-300 text-sm rounded-md outline-none focus:ring-0 hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-primary-500 block w-full py-3 px-2 h-[44px]"
                        name="priority"
                    >
                        <template
                            v-for="elem in assignedStatusArr"
                            :key="elem.id"
                        >
                            <option :value="elem.value">
                                {{ elem.name }} {{ elem.value }}
                            </option>
                        </template>
                    </select>
                </div>
                <div class="gird">
                    <div class="grid mb-5">
                        <x-label for="attachments"
                        >Attachments:</x-label
                        >
                        <!-- AppTextInput -->
                        <!-- <input
                            type="file"
                            id="attachments"
                            name="attachments[]"
                            accept="application/pdf,image/jpeg,image/png, .csv"
                            multiple
                        /> -->
                        <input
                            ref="image"
                            type="file"
                            @change="selectFiles"
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            name="attachments[]"
                            accept="application/pdf,image/jpeg,image/png, .csv"
                            multiple
                        />
                    </div>
                </div>
            </div>

            <AppButton type="submit">Comment</AppButton>
        </form>
    </div>

</template>
