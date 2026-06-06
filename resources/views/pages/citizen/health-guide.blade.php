<x-app-layout>
    <x-slot name="title">Health Guide</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Health Guide</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Disclaimer --}}
            <div class="border-l-4 border-primary-400 bg-primary-50 px-5 py-4">
                    <p class="text-base font-bold text-primary-900">For information only. This is not a medical diagnosis.</p>
                <p class="text-sm text-primary-700 mt-1 leading-relaxed">The tips below are general health guidance. If you have symptoms, please visit your nearest health center immediately.</p>
            </div>

            {{-- Disease accordion list --}}
            <div
                x-data="{ active: null, selectDisease(id) { this.active = this.active === id ? null : id; } }"
                class="space-y-3"
            >
                <p class="text-sm text-gray-500">Tap a disease to expand prevention tips and what to do if infected.</p>

                @foreach($healthCategories as $cat)
                    @if(($cat->prevention_tips && count($cat->prevention_tips) > 0) || ($cat->action_steps && count($cat->action_steps) > 0))
                        @php
                            $defaults = [
                                'Dengue'       => ['bg' => 'bg-red-50',   'border' => 'border-red-200',   'heading' => 'text-red-700'],
                                'Tuberculosis' => ['bg' => 'bg-blue-50',  'border' => 'border-blue-200',  'heading' => 'text-blue-700'],
                                'Malnutrition' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'heading' => 'text-green-700'],
                                'Hypertension' => ['bg' => 'bg-pink-50',  'border' => 'border-pink-200',  'heading' => 'text-pink-700'],
                                'Diarrhea'     => ['bg' => 'bg-sky-50',   'border' => 'border-sky-200',   'heading' => 'text-sky-700'],
                            ];
                            $colors = $defaults[$cat->name] ?? ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'heading' => 'text-gray-700'];
                            $prevention = $cat->prevention_tips ?? [];
                            $actions    = $cat->action_steps ?? [];
                            $hasGuide   = count($prevention) > 0 || count($actions) > 0;
                            $uniqueId   = 'guide-' . $cat->id;
                        @endphp

                        @if($hasGuide)
                        <div
                            class="border {{ $colors['border'] }} rounded-xl overflow-hidden shadow-sm"
                            x-bind:class="active === '{{ $uniqueId }}' ? '{{ $colors['bg'] }}' : 'bg-white'"
                        >
                            <button type="button" x-on:click="selectDisease('{{ $uniqueId }}')" class="w-full flex items-center justify-between px-5 py-4 text-left gap-4">
                                <div class="flex flex-col min-w-0 text-left">
                                    <span class="font-bold text-gray-900 text-lg leading-snug">{{ $cat->name }}</span>
                                    @if($cat->description)
                                        <span class="text-sm text-gray-500 mt-1 leading-relaxed">{{ $cat->description }}</span>
                                    @endif
                                </div>
                                <x-ui.icon name="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" x-bind:class="active === '{{ $uniqueId }}' ? 'rotate-180' : ''" />
                            </button>

                            <div x-show="active === '{{ $uniqueId }}'" x-collapse x-cloak class="px-5 pb-6 space-y-6">
                                @if(count($prevention) > 0)
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide {{ $colors['heading'] }} mb-3">How to Prevent {{ $cat->name }}</p>
                                    <ul class="space-y-2">
                                        @foreach($prevention as $tip)
                                        <li class="flex gap-2.5 text-base text-gray-700">
                                            <x-ui.icon name="check-circle" class="w-4 h-4 text-green-500 shrink-0 mt-0.5" />
                                            <span>{{ $tip }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                @if(count($actions) > 0)
                                <div class="border-t {{ $colors['border'] }} pt-5">
                                    <p class="text-xs font-bold uppercase tracking-wide {{ $colors['heading'] }} mb-3">If You Have {{ $cat->name }}</p>
                                    <ul class="space-y-2">
                                        @foreach($actions as $step)
                                        <li class="flex gap-2.5 text-base text-gray-700">
                                            <x-ui.icon name="arrow-right" class="w-4 h-4 text-primary-500 shrink-0 mt-0.5" />
                                            <span>{{ $step }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                    <div class="mt-4 flex items-center gap-2 px-4 py-3 bg-primary-700 text-white text-sm font-semibold rounded-lg w-fit">
                                            <span>If you are unsure, go to your nearest health center</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endif
                @endforeach

                @if($healthCategories->every(fn ($c) => empty($c->prevention_tips) && empty($c->action_steps)))
                    <p class="text-base text-gray-400 text-center py-10">No health guide content available yet.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
