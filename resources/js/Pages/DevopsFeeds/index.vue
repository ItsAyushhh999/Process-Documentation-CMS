<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import AppDataTable from '@/components/AppDataTable.vue';
import ActionButton from './ActionButton.vue';
import { inject} from 'vue';
import formatterComponent from '@/utils/formatterComponent';
import axios from 'axios';


const props = defineProps({
    tasks: {
        type: Array,
        default: []
    }
});


const filters = inject('filters');

const customHeaderFilter = (headerValue, rowValue, rowData, filterParams) => {
    const nameArr = rowValue.map(elem => {
        elem.collaborator
        if (filterParams.flag == elem.flag) {
            return elem.collaborator.toLowerCase()
        }
        return ''
    })
    let isIncluded = nameArr.map((char) => {
        return char.includes(headerValue.toLowerCase());
    });

    return isIncluded.some((e) => e == true)
}
const columns = [
    {
        title: 'Task No',
        field: 'id',
        headerFilter: true,
        minWidth: 120,
        formatter: 'link',
        formatterParams: (cell) => {
            return {
                'target': '_blank',
                'url': `/tasks/${cell.getData().id}/edit`
            }
        }
    },
    {
        title: 'Title',
        field: 'title',
        minWidth: 380,
        headerFilter: true,
        formatter: (cell) => {
            return formatterComponent({
                component: ActionButton,
                props: {
                    isTitle: true,
                    data: cell.getData(),
                    dateFilter: filters
                }
            });
        }
    },
        {
        title: 'Action',
        headerFilter: true,
        minWidth: 400,
        formatter: (cell) => {
            return formatterComponent({
                component: ActionButton,
                props: {
                isAction: true,
                            data: cell.getData(),
                            dateFilter: filters
                }
            });
        }
    },
    {
        title: 'Assignee(s)',
        field: 'collaborators',
        minWidth: 180,
        headerFilter: true,
        headerFilterFunc: customHeaderFilter,
        headerFilterFuncParams: { 'flag': '0' },
        formatter: (cell) => {
            const collaborators = cell.getData().collaborators;
            const assignee = collaborators.filter((elem) => elem.flag == '0')
            // console.log('assigne', assignee)
            return formatterComponent({
                component: ActionButton,
                props: {
                    isCollaborators: true,
                    data: assignee,
                }
            });
        }
    },
    {
        title: 'Reviewer(s)',
        field: 'collaborators',
        minWidth: 180,
        headerFilter: true,
        headerFilterFunc: customHeaderFilter,
        headerFilterFuncParams: { 'flag': '1' },
        formatter: (cell) => {
            const collaborators = cell.getData().collaborators;
            const assignee = collaborators.filter((elem) => elem.flag == '1')
            return formatterComponent({
                component: ActionButton,
                props: {
                    isCollaborators: true,
                    data: assignee,
                }
            });
        }
    }
];

</script>
<template>

    <Head title="Devops Feeds" />
    <AuthenticatedLayout>
        <template #pageTitle>{{ 'Devops Feeds' }}</template>

        <div class="w-full h-full" v-if="tasks && tasks.length > 0">
            <AppDataTable :data="tasks" :columns="columns">
            </AppDataTable>
        </div>
    </AuthenticatedLayout>
</template>
