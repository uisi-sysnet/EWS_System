<div class="card bg-white dark:bg-gray-900 backdrop-blur-md border border-slate-grey/20 dark:border-munti-blue-1/30 rounded-md shadow-sm dark:shadow-none p-2 flex flex-col gap-2">
    <div class="flex flex-col gap-1.5">
        <div class="flex items-center justify-between bg-munti-blue-1/5 dark:bg-munti-blue-1/10 border-b border-munti-blue-1/20 dark:border-munti-blue-1/30 px-4 py-2 uppercase tracking-wide font-medium text-munti-blue-0 dark:text-munti-blue-1">
            <span>Logs</span>
            <div class="flex items-center gap-4">
                <span id="liveIndicator" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 font-medium rounded-md text-munti-blue-1 dark:text-munti-blue-1">
                    <span class="relative flex size-2.5"><span class="absolute size-full rounded-md bg-munti-blue-1/50 dark:bg-munti-blue-1/50 opacity-75"></span><span class="relative size-2.5 rounded-md bg-munti-blue-1 dark:bg-munti-blue-1"></span></span>Offline
                </span>
            </div>
        </div>
        @auth
        @php $isAdmin = in_array(auth()->user()->user_level, ['superadmin', 'admin']); @endphp
        <div id="logContainer" class="flex-1 bg-white dark:bg-black border border-gray-400 dark:border-gray-600 font-mono text-[10px] text-munti-black-0 dark:text-munti-blue-1 overflow-hidden">
            <div id="terminal" class="p-4 overflow-y-auto whitespace-pre-wrap break-words leading-tight tracking-tight custom-scroll {{ $isAdmin ? 'h-40' : 'h-52' }}"></div>
        </div>
        <form id="sendForm" class="{{ $isAdmin ? 'flex' : 'hidden' }} items-center gap-2">
            <input id="command" type="text" placeholder="Type command and press Send or Enter" class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-900 border border-munti-blue-1/20 dark:border-munti-blue-1/30 rounded-md focus:ring-2 focus:ring-munti-blue-0 dark:focus:ring-munti-blue-0 focus:border-munti-blue-0 dark:focus:border-munti-blue-0 outline-none text-munti-black-0 dark:text-munti-white-0 placeholder-slate-grey dark:placeholder-munti-blue-1/50" />
            <button type="button" id="btnSend" disabled class="px-8 py-2.5 bg-munti-blue-0 dark:bg-munti-blue-1 hover:bg-munti-blue-1 dark:hover:bg-munti-blue-0 text-white rounded-md font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">Send</button>
        </form>
        @endauth
    </div>
</div>