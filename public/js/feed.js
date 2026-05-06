
let feedList = document.getElementById('feed-list');
const channel = pusher.subscribe('feed');

channel.bind('my-event', function(data) {
    appendFeed(data);
});

function appendFeed(data) {
    const { feed } = data;
    const userName = getUserName(feed.source);
    const createdAt = moment(feed.created_at).format('MMMM Do YYYY, h:mm:ss A');

    const div = `
        <div class="py-5 rounded overflow-y-auto max-h-[80vh] pr-1">
            <div class="flex flex-col space-y-4">
               <div class="flex space-x-6">
                  <div class="flex flex-col items-center mt-4">
                        <span class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                            ${userName}
                        </span>
                    <span class="w-[1px] block bg-gray-300 dark:bg-gray-600 grow mt-2"></span>
               </div>
                    <div class="border border-gray-300 dark:border-gray-600 w-20 flex grow relative rounded-lg mt-4">
                        <div class="absolute border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-neutral-700 w-4 h-4 rotate-45 top-3 -left-2 z-10">
                        </div>
                        <div class="z-20 w-full h-full overflow-hidden rounded-lg">
                            <div
                                class="flex justify-between items-center bg-gray-100 dark:bg-neutral-700 py-2 px-3">
                                <div class="flex gap-3">
                                    <span class="text-base dark:text-gray-300">
                                        <strong>
                                            ${feed.source}
                                        </strong>
                                    </span>
                                </div>
                                <div class="px-3 py-2 text-end flex items-center gap-2">
                                    <span class="text-base text-gray-500 dark:text-gray-300 pr-3">
                                        ${createdAt}
                                    </span>
                                </div>
                            </div>
                            <div class="px-3 py-2">
                                <div class="scrolling-touch overflow-x-auto scroll-none">
                                    <span class="dark:text-slate-300 description  ">
                                        ${feed.title}
                                    </span>
                                </div>
                            </div>
                            <div class="px-3 py-2">
                                ${buildChips(feed)}
                            </div>
                        </div>
                    </div>
               </div>
             </div>
        </div>`
    feedList.innerHTML += div
}

function buildChips(feed) {
    let status = {
        'SUCCESS': 'bg-blue-500',
        'FAILURE': 'bg-red-500',
        'REVERT': 'bg-red-500',
        'CREATED': 'bg-green-500',
        'CLOSE': 'bg-red-500',
        'MERGE': 'bg-green-500',
        'COMPLETED': 'bg-green-500',
        'INPROGRESS': 'bg-green-500',
    }

    let statusBgColor = status[feed.status];

    let chipsHtml = `<div class="inline-flex gap-1 flex-wrap">`;
    chipsHtml += createChip(feed.type, 'bg-green-500');
    chipsHtml += createChip(feed.status, statusBgColor);

    if(feed.task &&  Object.keys(feed.task).length){
        chipsHtml += createLinkChip(`/tasks/${feed.task.id}/edit`,`Task: ${feed.task.id} : ${feed.task.title}`, 'bg-blue-500');
    }

    if (feed.project && Object.keys(feed.project).length) {
        chipsHtml += createLinkChip(`/project/${feed.project.id}/document`, `Project: ${feed.project.name}`, 'bg-blue-800');
    }

    if (feed.created_by && Object.keys(feed.created_by).length) {
        chipsHtml += createChip(`CREATED BY:${feed.created_by.name}`, 'bg-blue-900');
    }

    chipsHtml += `</div>`

    return chipsHtml;
}

function getUserName(source){
    const sourceSplit = source.split(' ');
    return source.length > 3 ? (sourceSplit.length > 1 ? `${sourceSplit[0][0]}${sourceSplit[1][0]}` : 'BOT') : source;
}

function createChip(content, bgColor) {
    return `<div class="center relative inline-block select-none whitespace-nowrap rounded-lg ${bgColor} py-2 px-3.5 p-2 align-baseline font-sans text-xs font-bold uppercase leading-none text-white">
        ${content}
    </div>`;
}

function createLinkChip(url, content, bgColor) {
    return `
        <a href="${url}">
            ${createChip(content, bgColor)}
        </a>`;
}
