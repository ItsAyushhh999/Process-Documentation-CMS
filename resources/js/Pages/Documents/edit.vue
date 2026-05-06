<script setup>
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppEditor from '@/components/AppEditor.vue';
import AppTextInput from '@/components/AppTextInput.vue';
import AppButton from '@/components/AppButton.vue';
import AppAutocomplete from '@/components/AppAutocomplete.vue';
import { computed } from 'vue';

const props = defineProps({
    // project: {
    //     type: Object,
    //     default: null
    // },
    // categories: {
    //     type: Array,
    //     default: []
    // }
    document: {
        type: Object,
        default: null
    },
    isPermitted: {
        type: Array,
        default: []
    },
    categories: {
        type: Array,
        default: []
    },
    documentCategory: {
        type: Array,
        default: []
    },
    isPublished: {
        type: Boolean,
    }
});
// console.log('props', props)
const categoriesObjArr = computed(() => {
    let arr = []
    props.categories.map(elem => {
        props.documentCategory.map(item => {
            if(item == elem.id){
                arr.push(elem)
            }
        })
    })
    return arr
})

// console.log('categoriesObjArr', categoriesObjArr.value)

const form = useForm({
    documentId: props.document.id,
    description: props.document.description,
    name: props.document.name,
    categories: categoriesObjArr.value,
    isPublish : props.document.isPublished
});

const handleSubmit = () => {
    // action=" {{ isset($document) ? route('document.update', $document->id) : route('document.store') }}"
    // console.log('form', form.data())
    const categoriesIds = [];
    form.categories.map((elem) => {
        categoriesIds.push(elem.id);
    });
    form.categories = categoriesIds;

    console.log('form', form.data(), props.document.id)

// return
    form.put(route('document.update',props.document.id), {
        onFinish: () => {
            console.log('success');
            // btnLoading.value = false
        },
        onError: (error) => {
            console.log(error);
            // errors.value = error;
        },
        onSuccess: () => {
            // emit('reloadPage')
            location.href = route('documents.index', props.document.project_id);
            // console.log('success')
        }
    });
};
</script>

<template>
    <Head title="Document Create" />
    <AuthenticatedLayout>
        <div class="dark:bg-slate-900">
            <form method="post" id="documentForm" @submit.prevent="handleSubmit">
                <div class="documents_quell relative flex justify-center">
                    <div class="w-[900px] bg-white dark:bg-slate-800 min-h-[calc(100vh-70px)]">
                        <AppEditor
                            fullHeight
                            noBorder
                            name="description"
                            v-model="form.description"
                        />
                    </div>

                    <div class="xl:w-[360px] lg:w-[300px] rounded z-10">
                        <div class="sticky top-0 flex flex-col gap-y-3 p-5">
                            <h3 class="text-xl font-bold mb-4 dark:text-gray-300">New Documents</h3>
                            <!-- <span>{{ project }}</span> -->
                            <div class="grid">
                                <AppTextInput
                                    label="Name"
                                    required
                                    v-model="form.name"
                                    name="name"
                                />
                            </div>
                            <div class="grid mb-3">
                                <app-autocomplete
                                    v-model="form.categories"
                                    placeholder="Select Categories"
                                    :options="categories"
                                    select-label="Enter"
                                    track-by="id"
                                    name="Categories"
                                    label="name"
                                    multiple
                                >
                                    <template #default="{ option }">
                                        <div class="flex items-center gap-2">
                                            <div class="grow">
                                                {{ option.name }}
                                            </div>
                                        </div>
                                    </template>
                                </app-autocomplete>
                            </div>
                          
                            <div>
                                <select name="isPublish" id="isPublish" class="w-full rounded" v-model="form.isPublish">
                                    <!-- <option value="" default>Select Status</option> -->
                                    <option value="1">Publish</option>
                                    <option value="0">UnPublish</option>
                                </select>
                            </div>
                            <div class="grid">
                                <!-- <x-button type="submit">
                            {{ isset($document) ? 'Update document' : 'Create document' }}
                        </x-button> -->
                                <AppButton type="submit" :loading="form.processing">Update Document</AppButton>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
