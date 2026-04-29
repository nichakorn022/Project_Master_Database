@extends('layouts.sidebar')
@section('title', __('sidebar.glazes'))
@section('header', __('sidebar.glazes'))
@section('content')
    <main class="flex-1 bg-gray-50 dark:bg-gray-900" x-data="glazePage()" x-init="initSelect2()">
        <!-- Filters -->
        <div class="dark:bg-gray-800 dark:shadow-gray-900/50 bg-white p-6 rounded-lg shadow-md mb-3">
            <form method="GET" action="{{ route('glaze.index') }}" class="space-y-4">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-64">
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('content.search_by') }}{{ __('content.glaze_code') }}{{ __('content.etc') }}"
                                class="dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hoverScale hover:bg-green-700">
                            <span class="material-symbols-outlined">search</span>
                            <span>{{ __('content.search') }}</span>
                        </button>

                        <button type="button" @click="showFilter = !showFilter"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hoverScale hover:bg-yellow-700 dark:bg-amber-600 dark:hover:bg-amber-500">
                            <span class="material-symbols-outlined">filter_list</span>
                            <span>{{ __('content.filter') }}</span>
                        </button>

                        <a href="{{ route('glaze.index') }}"
                            class="dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500 flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hoverScale hover:bg-gray-300">
                            <span class="material-symbols-outlined">refresh</span>
                            <span>{{ __('content.reset') }}</span>
                        </a>
                    </div>

                    <div>
                        <select name="per_page" onchange="this.form.submit()"
                            class="dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 w-32 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 {{ __('content.items') }}</option>
                            <option value="10" {{ request('per_page') == 10 || !request('per_page') ? 'selected' : '' }}>10 {{ __('content.items') }}</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 {{ __('content.items') }}</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 {{ __('content.items') }}</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 {{ __('content.items') }}</option>
                        </select>
                    </div>

                    @if ($hasCreate)
                        <div class="ml-auto">
                            <button type="button" @click="openCreateModal()"
                                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hoverScale hover:bg-blue-700">
                                <span class="material-symbols-outlined">add</span>
                                <span>{{ __('content.add') }}</span>
                            </button>
                        </div>
                    @endif
                </div>

                <div x-show="showFilter" x-cloak class="border border-gray-200 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/40 dark:border-gray-600">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="effect_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('content.effect_code') }}</label>
                            <select id="effect_id" name="effect_id" onchange="this.form.submit()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">All</option>
                                @foreach ($effects as $effect)
                                    <option value="{{ $effect->id }}" {{ (string) $effectId === (string) $effect->id ? 'selected' : '' }}>{{ $effect->effect_code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="glaze_inside_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('content.inside_color_code') }}</label>
                            <select id="glaze_inside_id" name="glaze_inside_id" onchange="this.form.submit()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">All</option>
                                @foreach ($glazeInsides as $glazeInside)
                                    <option value="{{ $glazeInside->id }}" {{ (string) $glazeInsideId === (string) $glazeInside->id ? 'selected' : '' }}>{{ $glazeInside->glaze_inside_code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="glaze_outer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('content.outside_color_code') }}</label>
                            <select id="glaze_outer_id" name="glaze_outer_id" onchange="this.form.submit()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">All</option>
                                @foreach ($glazeOuters as $glazeOuter)
                                    <option value="{{ $glazeOuter->id }}" {{ (string) $glazeOuterId === (string) $glazeOuter->id ? 'selected' : '' }}>{{ $glazeOuter->glaze_outer_code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('content.status') }}</label>
                            <select id="status_id" name="status_id" onchange="this.form.submit()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="all" {{ empty($statusId) ? 'selected' : '' }}>All</option>
                                <option value="unknown" {{ $statusId === 'unknown' ? 'selected' : '' }}>Unknown</option>
                                @foreach ($statusFilters as $status)
                                    <option value="{{ $status->id }}" {{ (string) $statusId === (string) $status->id ? 'selected' : '' }}>{{ $status->status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @if ($effectId || $glazeInsideId || $glazeOuterId || $statusId)
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                    {{ __('content.filter') }}:
                </span>
                @if ($effectId)
                    <a href="{{ route('glaze.index', array_merge(request()->except('effect_id', 'page'), ['per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800">
                        <span>{{ $effects->firstWhere('id', $effectId)->effect_code ?? 'Unknown' }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif
                @if ($glazeInsideId)
                    <a href="{{ route('glaze.index', array_merge(request()->except('glaze_inside_id', 'page'), ['per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800">
                        <span>{{ $glazeInsides->firstWhere('id', $glazeInsideId)->glaze_inside_code ?? 'Unknown' }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif
                @if ($glazeOuterId)
                    <a href="{{ route('glaze.index', array_merge(request()->except('glaze_outer_id', 'page'), ['per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200 dark:bg-purple-900 dark:text-purple-300 dark:hover:bg-purple-800">
                        <span>{{ $glazeOuters->firstWhere('id', $glazeOuterId)->glaze_outer_code ?? 'Unknown' }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif
                @if ($statusId)
                    <a href="{{ route('glaze.index', array_merge(request()->except('status_id', 'page'), ['status_id' => 'all', 'per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-900 dark:text-yellow-300 dark:hover:bg-yellow-800">
                        <span>{{ $statusId === 'unknown' ? 'Unknown' : ($statuses->firstWhere('id', $statusId)->status ?? 'Unknown') }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif
            </div>
        @endif

        <!-- Table -->
        <div class="dark:shadow-gray-900/50 dark:bg-gray-800 rounded-xl shadow-md bg-white">
            <div class="overflow-x-auto rounded-xl">
                <table class="min-w-full text-sm">
                    <thead class="dark:bg-gray-700 dark:border-gray-700 text-xs text-black dark:text-gray-400 uppercase bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left">{{ __('content.glaze_code') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('content.inside_color_code') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('content.outside_color_code') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('content.effect_code') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('content.fire_temp') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('content.status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('content.updated_by') }}</th>
                            <th class="px-4 py-3 text-end w-[80px]">{{ __('content.action') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($glazes as $glaze)
                            @php
                                $status = $glaze->status->status ?? 'Unknown';
                                $statusColor = match ($status) {
                                    'Active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'Cancel' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                };
                            @endphp
                            <tr class="dark:text-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 bg-white border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $glaze->glaze_code }}</td>
                                <td class="px-4 py-3">{{ $glaze->glazeInside->glaze_inside_code ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $glaze->glazeOuter->glaze_outer_code ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $glaze->effect->effect_code ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    {{ $glaze->fire_temp ? $glaze->fire_temp . ' °C' : '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $statusColor }} px-2 py-1 rounded-full text-xs font-semibold">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $glaze->updater->name ?? 'System' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-1">
                                        <button @click="openDetailModal({{ $glaze->toJson() }})"
                                            class="flex items-center gap-1 px-2 py-1 text-sm font-medium text-white bg-blue-500 rounded-lg shadow-sm hover:bg-green-600 hover:shadow-md hoverScale">
                                            <span class="material-symbols-outlined text-white">feature_search</span>
                                        </button>
                                        @if ($hasEdit)
                                            <button @click="openEditModal({{ $glaze->toJson() }})"
                                                class="text-blue-600 hover:text-blue-700">
                                                <span class="material-symbols-outlined">edit</span>
                                            </button>
                                        @endif
                                        @if ($hasDelete)
                                            <button @click="DeleteGlazeModal = true; glazeIdToDelete = {{ $glaze->id }}; itemCodeToDelete = '{{ $glaze->glaze_code }}'"
                                                class="text-red-500 hover:text-red-700">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                    @if(request('search'))
                                        {{ __('content.not_found') }} "{{ request('search') }}".
                                    @else
                                        {{ __('content.not_found') }}
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-4 flex justify-end pb-2">
                    {{ $glazes->links('vendor.pagination.tailwind-custom') }}
                </div>
            </div>
        </div>
        {{-- include modal --}}
        @include('components.Create-modals.create-glaze')
        @include('components.Edit-modals.edit-glaze')
        @include('components.Detail-modals.detail-glaze')
        <x-modals.delete-modal 
            show="DeleteGlazeModal"
            itemName="itemCodeToDelete"
            deleteFunction="deleteGlaze"
        />    
    </main>
@endsection
