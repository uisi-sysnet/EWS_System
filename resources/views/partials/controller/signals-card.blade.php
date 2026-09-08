<div class="card bg-white dark:bg-gray-900 backdrop-blur-md border border-munti-blue-1/20 dark:border-blue-slate/30 rounded-md shadow-sm dark:shadow-none flex flex-col overflow-hidden col-span-1 lg:col-span-2">
    <div class="bg-munti-blue-0 dark:bg-munti-blue-0 text-white text-base font-medium px-3 py-2 md:px-4 md:py-2 text-center border-b border-munti-blue-1/20 dark:border-blue-slate/30 shrink-0 tracking-wide">SIGNALS</div>
    <div id="signals-container" class="flex-1 p-1 md:p-3 overflow-y-auto space-y-1.5 md:space-y-2.5 bg-gradient-to-b from-slate-grey/5 to-transparent dark:from-blue-slate/10 dark:to-transparent custom-scroll">
        @php
        
        foreach ($signals as $signal) {
            if ($signal->oid === '62a98bc3-7859-4552-be53-7fc55e899775') {
                $signal->name = 'Nuclear/Chemical/Biological Hazard';
                break;
            }
        }

        $filteredSignals = collect($signals)->filter(function($signal) {
            $oidsToHide = [
                'aff81c44-173d-49bd-af06-2a79dbf5d177',
                '7a45ccd2-c74d-4f9d-b9d0-78ebb2dbd786',
                '961fcc9a-77e8-413f-9415-831069b3e3bb',
                '53d93e27-7077-46d7-a4da-8743255b9ff7',
                '77b6a87c-6936-4ab3-8457-7bcf2080c0aa',
                '722f0270-e233-4ec6-80e7-51c135903a87'
            ];
            return !in_array($signal->oid, $oidsToHide);
        });

        if (!function_exists('isColorDark')) {
            function isColorDark($hexColor) {
                $hexColor = ltrim($hexColor, '#');
                
                if (strlen($hexColor) === 3) {
                    $hexColor = $hexColor[0] . $hexColor[0] . $hexColor[1] . $hexColor[1] . $hexColor[2] . $hexColor[2];
                }
                
                $r = hexdec(substr($hexColor, 0, 2));
                $g = hexdec(substr($hexColor, 2, 2));
                $b = hexdec(substr($hexColor, 4, 2));
                
                $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b);
                
                return $luminance < 128;
            }
        }
        @endphp
        
        @forelse ($filteredSignals as $signal)
        @php
        $isIncomplete = is_null($signal->button_color)
        || is_null($signal->beacon_color_1)
        || is_null($signal->delay_1)
        || is_null($signal->beacon_color_2)
        || is_null($signal->delay_2);
        
        $buttonColor = $signal->button_color ?? '#99a1af';
        $textColorClass = (!is_null($signal->button_color) && isColorDark($buttonColor)) ? 'text-white' : 'text-munti-black-0';
        @endphp

        <button type="button"
            class="select-items signal-btn w-full p-1 text-center uppercase font-semibold rounded-md transition-all duration-150 ease-in-out cursor-pointer {{ $textColorClass }} shadow-[0_6px_0_0_var(--signal-shadow)] hover:shadow-[0_3px_0_0_var(--signal-shadow)] hover:translate-y-[3px] active:shadow-[0_1px_0_0_var(--signal-shadow)] active:translate-y-[5px] border border-gray-600/50 bg-gray-400 hover:bg-gray-300
            {{ $isIncomplete ? 'cursor-not-allowed pointer-events-none' : '' }}"
            data-color="{{ $signal->button_color ?? '#99a1af' }}"
            data-signal-id="{{ $signal->id }}"
            data-oid="{{ $signal->oid }}"
            data-color1="{{ $signal->beacon_color_1 ?? '0,0,0,0' }}"
            data-delay1="{{ $signal->delay_1 ?? 0 }}"
            data-color2="{{ $signal->beacon_color_2 ?? '0,0,0,0' }}"
            data-delay2="{{ $signal->delay_2 ?? 0 }}"
            data-duration="{{ $signal->duration ?? 0 }}"
            {{ $isIncomplete ? 'disabled' : '' }}>
            {{ $signal->name }}
        </button>
        @empty
        <div class="col-span-full text-center py-10 text-munti-black-0 text-sm italic">
            No signals added yet
        </div>
        @endforelse
    </div>
</div>
