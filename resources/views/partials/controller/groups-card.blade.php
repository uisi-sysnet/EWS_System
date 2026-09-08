<div class="card bg-white dark:bg-gray-900 backdrop-blur-md border border-munti-blue-1/20 dark:border-blue-slate/30 rounded-md shadow-sm dark:shadow-none flex flex-col overflow-hidden col-span-1 lg:col-span-2">
    <div class="bg-munti-blue-0 dark:bg-munti-blue-0 text-white text-base font-medium px-3 py-2 md:px-4 md:py-2 text-center border-b border-munti-blue-1/20 dark:border-blue-slate/30 shrink-0 tracking-wide">LOCATIONS</div>
    <div class="flex flex-1 min-h-0">
        <div class="w-16 flex flex-col gap-1 p-1 h-full">
            <button type="button" id="select-all-beacons" class="flex-1 bg-munti-white border-2 border-munti-[#b2b2b2]/50 flex items-center justify-center hover:bg-munti-yellow-1 transition-all duration-200 group">
                <span id="select-all-beacon" class="font-bold tracking-wider text-munti-black-0 uppercase text-[9px]">Select All Beacons</span>
            </button>
            <button type="button" id="select-all-groups" class="flex-1 bg-munti-white border-2 border-munti-[#b2b2b2]/50 flex items-center justify-center hover:bg-munti-yellow-1 transition-all duration-200 group">
                <span id="select-all-text" class="font-bold tracking-wider text-munti-black-0 uppercase text-[9px]">Select All</span>
            </button>
            <button type="button" id="select-all-sirens" class="flex-1 bg-munti-white border-2 border-munti-[#b2b2b2]/50 flex items-center justify-center hover:bg-munti-yellow-1 transition-all duration-200 group">
                <span id="select-all-siren" class="font-bold tracking-wider text-munti-black-0 uppercase text-[9px]">Select All Sirens</span>
            </button>
        </div>
        <div id="groups-container" class="flex-1 p-1 md:p-3 overflow-y-auto space-y-1.5 md:space-y-2.5 bg-gradient-to-b from-munti-blue-1/5 to-transparent dark:from-blue-slate/10 dark:to-transparent custom-scroll">
            @forelse ($locations ?? [] as $location)
            <button type="button" class="select-all-items w-full p-1 text-center uppercase font-medium rounded-md transition-all duration-150 ease-in-out cursor-pointer shadow-[0_6px_0_0_#b2b2b2] hover:shadow-[0_3px_0_0_#ca8a04] hover:translate-y-[3px] active:shadow-[0_1px_0_0_#b2b2b2] active:translate-y-[5px] border border-munti-[#b2b2b2]/30 bg-white hover:bg-munti-yellow-1 text-munti-black-0 border-[#b2b2b2]/70 shadow-[0_6px_0_0_#b2b2b2]">{{ $location->location_name }}</button>
            @empty
            <div class="col-span-full text-center py-10 text-gray-600 dark:text-gray-400 text-sm italic">No locations added yet</div>
            @endforelse
        </div>
    </div>
</div>












