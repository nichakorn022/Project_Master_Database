@extends('layouts.sidebar')
@section('title', __('sidebar.patterns'))
@section('header', __('sidebar.patterns'))
@section('content')
    <main class="flex-1 bg-gray-50 dark:bg-gray-900" x-data="patternPage()" x-init="initSelect2()">
        <!-- Filters -->
        <div class="dark:bg-gray-800 dark:shadow-gray-900/50 bg-white p-6 rounded-lg shadow-md mb-3">
            <form method="GET" action="{{ route('pattern.index') }}" class="space-y-4">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-64">
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('content.search_by') }}{{ __('content.pattern_code') }}{{ __('content.etc') }}"
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

                        <a href="{{ route('pattern.index') }}"
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
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('content.customer') }}</label>
                            <select id="customer_id" name="customer_id" onchange="this.form.submit()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">All</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ (string) $customerId === (string) $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="designer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('content.designer') }}</label>
                            <select id="designer_id" name="designer_id" onchange="this.form.submit()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">All</option>
                                @foreach ($designers as $designer)
                                    <option value="{{ $designer->id }}" {{ (string) $designerId === (string) $designer->id ? 'selected' : '' }}>{{ $designer->designer_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="requestor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('content.requestor') }}</label>
                            <select id="requestor_id" name="requestor_id" onchange="this.form.submit()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">All</option>
                                @foreach ($requestors as $requestor)
                                    <option value="{{ $requestor->id }}" {{ (string) $requestorId === (string) $requestor->id ? 'selected' : '' }}>{{ $requestor->name }}</option>
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

                        <div>
                            <label for="exclusive" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('content.exclusive') }}</label>
                            <select id="exclusive" name="exclusive" onchange="this.form.submit()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">All</option>
                                <option value="1" {{ $exclusive === '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ $exclusive === '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @if ($customerId || $designerId || $requestorId || $statusId || ($exclusive !== null && $exclusive !== ''))
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                    {{ __('content.filter') }}:
                </span>
                @if ($customerId)
                    <a href="{{ route('pattern.index', array_merge(request()->except('customer_id', 'page'), ['per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800">
                        <span>{{ $customers->firstWhere('id', $customerId)->name ?? 'Unknown' }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif
                @if ($designerId)
                    <a href="{{ route('pattern.index', array_merge(request()->except('designer_id', 'page'), ['per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-orange-100 text-orange-800 hover:bg-orange-200 dark:bg-orange-900 dark:text-orange-300 dark:hover:bg-orange-800">
                        <span>{{ $designers->firstWhere('id', $designerId)->designer_name ?? 'Unknown' }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif
                @if ($requestorId)
                    <a href="{{ route('pattern.index', array_merge(request()->except('requestor_id', 'page'), ['per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800">
                        <span>{{ $requestors->firstWhere('id', $requestorId)->name ?? 'Unknown' }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif
                @if ($statusId)
                    <a href="{{ route('pattern.index', array_merge(request()->except('status_id', 'page'), ['status_id' => 'all', 'per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-900 dark:text-yellow-300 dark:hover:bg-yellow-800">
                        <span>{{ $statusId === 'unknown' ? 'Unknown' : ($statuses->firstWhere('id', $statusId)->status ?? 'Unknown') }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif
                @if ($exclusive !== null && $exclusive !== '')
                    <a href="{{ route('pattern.index', array_merge(request()->except('exclusive', 'page'), ['per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800">
                        <span>{{ __('content.exclusive') }}: {{ $exclusive === '1' ? 'Yes' : 'No' }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif
            </div>
        @endif

        <!-- Table -->
        <div class="rounded-xl shadow-md bg-white
            dark:shadow-gray-900/50 dark:bg-gray-800">
            <div class="overflow-x-auto rounded-xl">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200 uppercase text-xs
                            dark:bg-gray-700 dark:border-gray-700 dark:text-gray-400 text-black">    
                        <tr>
                            <th class="px-4 py-3 text-left">{{ __('content.pattern_code') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('content.description') }}</th>
                            <th class="px-4 py-3 text-center">{{ __('content.exclusive') }}</th>
                            <th class="px-4 py-3 text-center">{{ __('content.status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('content.customer') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('content.updated_by') }}</th>
                            <th class="px-4 py-3 text-end w-[80px]">{{ __('content.action') }}</th>
                        </tr>
                    </thead>
                    <!-- Table Body -->
                    <tbody>
                        @forelse ($patterns as $pattern)
                            @php
                                $status = $pattern->status->status ?? 'Unknown';
                                $statusColor = match ($status) {
                                    'Active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'Cancel' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                };
                                $customer = $pattern->customer->name ?? '-';
                            @endphp

                            <tr class="dark:text-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 bg-white border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $pattern->pattern_code }}</td>
                                <td class="px-4 py-3">{{ $pattern->pattern_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($pattern->exclusive)
                                        <span class="material-symbols-outlined text-green-500">radio_button_checked</span>
                                    @else
                                        <span class="material-symbols-outlined text-gray-400">radio_button_unchecked</span>
                                    @endif
                                </td>                                
                                <!-- Status -->
                                <td class="px-4 py-3 text-center">
                                    <span class="{{ $statusColor }} px-2 py-1 rounded-full text-xs font-semibold">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">{{ $customer }}</td>

                                <!-- UPDATED BY -->
                                <td class="px-4 py-3 text-right ">{{ $pattern->updater->name ?? 'System' }}</td>

                                <!-- Action -->
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button @click="openDetailModal({{ $pattern->toJson() }})"
                                            class="flex items-center gap-1 px-2 py-1 text-sm font-medium text-white bg-blue-500 rounded-lg shadow-sm hover:bg-green-600 hover:shadow-md hoverScale">
                                            <span class="material-symbols-outlined text-white">feature_search</span>
                                        </button>
                                        @if ($hasEdit)
                                            <button @click="openEditModal({{ $pattern->toJson() }})"
                                                class="text-blue-600 hover:text-blue-700">
                                                <span class="material-symbols-outlined">edit</span>
                                            </button>
                                        @endif
                                        @if ($hasDelete)
                                            <button
                                                @click="DeletePatternModal = true; patternIdToDelete = {{ $pattern->id }}; itemCodeToDelete = '{{ $pattern->pattern_code }}'"
                                                class="text-red-500 hover:text-red-700">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-sm text-gray-500 text-center 
                                                dark:text-gray-400">
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
                    {{ $patterns->links('vendor.pagination.tailwind-custom') }}
                </div>
            </div>
        </div>

        {{-- include modal --}}
        @include('components.Create-modals.create-pattern')
        @include('components.Detail-modals.detail-pattern')
        @include('components.Edit-modals.edit-pattern')    
        <x-modals.delete-modal 
            show="DeletePatternModal"
            itemName="itemCodeToDelete"
            deleteFunction="deletePattern"
        />        
    </main>
@endsection
