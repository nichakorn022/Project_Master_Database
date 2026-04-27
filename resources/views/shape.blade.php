@extends('layouts.sidebar')
@section('title', __('sidebar.shapes'))
@section('header', __('sidebar.shapes'))
@section('content')
    <main x-data="shapePage()" x-init="initSelect2()">
        <!-- Filters -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-3 dark:bg-gray-800 dark:shadow-gray-900/50">
            <form method="GET" action="{{ route('shape.index') }}" class="space-y-4">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-64">
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('content.search_by') }}{{ __('content.shape_code') }}{{ __('content.etc') }}"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            />
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hoverScale hover:bg-green-700"
                        >
                            <span class="material-symbols-outlined">search</span>
                            <span>{{ __('content.search') }}</span>
                        </button>

                        <button
                            type="button"
                            @click="showFilter = !showFilter"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hoverScale hover:bg-yellow-700 dark:bg-amber-600 dark:hover:bg-amber-500"
                        >
                            <span class="material-symbols-outlined">filter_list</span>
                            <span>{{ __('content.filter') }}</span>
                        </button>

                        <a
                            href="{{ route('shape.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hoverScale hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500"
                        >
                            <span class="material-symbols-outlined">refresh</span>
                            <span>{{ __('content.reset') }}</span>
                        </a>
                    </div>

                    <div>
                        <select
                            name="per_page"
                            onchange="this.form.submit()"
                            class="w-32 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                        >
                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 {{ __('content.items') }}</option>
                            <option value="10" {{ request('per_page') == 10 || !request('per_page') ? 'selected' : '' }}>10 {{ __('content.items') }}</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 {{ __('content.items') }}</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 {{ __('content.items') }}</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 {{ __('content.items') }}</option>
                        </select>
                    </div>

                    @if ($hasCreate)
                        <div class="ml-auto">
                            <button
                                type="button"
                                @click="openCreateModal()"
                                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hoverScale hover:bg-blue-700"
                            >
                                <span class="material-symbols-outlined">add</span>
                                <span>{{ __('content.add') }}</span>
                            </button>
                        </div>
                    @endif
                </div>

                <div
                    x-show="showFilter"
                    x-cloak
                    class="border border-gray-200 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/40 dark:border-gray-600"
                >
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="shape_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('content.type') }}
                            </label>
                            <select
                                id="shape_type_id"
                                name="shape_type_id"
                                onchange="this.form.submit()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            >
                                <option value="all" {{ empty($shapeType) ? 'selected' : '' }}>All</option>
                                @foreach ($shapeTypes as $type)
                                    <option value="{{ $type->id }}" {{ (string) $shapeType === (string) $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="shape_collection_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('content.collection_2') }}
                            </label>
                            <select
                                id="shape_collection_id"
                                name="shape_collection_id"
                                onchange="this.form.submit()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            >
                                <option value="">All</option>
                                @foreach ($shapeCollections as $collection)
                                    <option value="{{ $collection->id }}" {{ request('shape_collection_id') == $collection->id ? 'selected' : '' }}>
                                        {{ $collection->collection_name ?? $collection->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="item_group_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('content.group') }}
                            </label>
                            <select
                                id="item_group_id"
                                name="item_group_id"
                                onchange="this.form.submit()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            >
                                <option value="">All</option>
                                @foreach ($itemGroups as $group)
                                    <option value="{{ $group->id }}" {{ request('item_group_id') == $group->id ? 'selected' : '' }}>
                                        {{ $group->item_group_name ?? $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('content.status') }}
                            </label>
                            <select
                                id="status_id"
                                name="status_id"
                                onchange="this.form.submit()"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            >
                                <option value="all" {{ empty($statusId) ? 'selected' : '' }}>All</option>
                                <option value="unknown" {{ $statusId === 'unknown' ? 'selected' : '' }}>Unknown</option>
                                @foreach ($statusFilters as $status)                                    
                                    <option value="{{ $status->id }}" {{ (string) $statusId === (string) $status->id ? 'selected' : '' }}>
                                        {{ $status->status }}
                                    </option>
                                @endforeach                       
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Tag Filter -->
        @if ($shapeType || request('shape_collection_id') || request('item_group_id') || $statusId)
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                    {{ __('content.filter') }}:
                </span>
                @if ($shapeType)
                    <a
                        href="{{ route('shape.index', array_merge(request()->except('shape_type_id', 'page'), ['shape_type_id' => 'all', 'per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800"
                    >
                        <span>{{ $shapeTypes->firstWhere('id', $shapeType)->name ?? 'Unknown' }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif
                @if (request('shape_collection_id'))
                    <a
                        href="{{ route('shape.index', array_merge(request()->except('shape_collection_id', 'page'), ['per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800"
                    >
                        <span>{{ $shapeCollections->firstWhere('id', request('shape_collection_id'))->collection_name ?? $shapeCollections->firstWhere('id', request('shape_collection_id'))->name ?? 'Unknown' }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif

                @if (request('item_group_id'))
                    <a
                        href="{{ route('shape.index', array_merge(request()->except('item_group_id', 'page'), ['per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200 dark:bg-purple-900 dark:text-purple-300 dark:hover:bg-purple-800"
                    >
                        <span>{{ $itemGroups->firstWhere('id', request('item_group_id'))->item_group_name ?? $itemGroups->firstWhere('id', request('item_group_id'))->name ?? 'Unknown' }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif

                @if ($statusId)
                    <a
                        href="{{ route('shape.index', array_merge(request()->except('status_id', 'page'), ['status_id' => 'all', 'per_page' => request('per_page', 10)])) }}"
                        class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-900 dark:text-yellow-300 dark:hover:bg-yellow-800"
                    >
                        <span>{{ $statusId === 'unknown' ? 'Unknown' : ($statuses->firstWhere('id', $statusId)->status ?? 'Unknown') }}</span>
                        <span class="material-symbols-outlined text-[8px]">close</span>
                    </a>
                @endif
            </div>
        @endif
        <!-- Table -->
        <div class="rounded-xl shadow-md bg-white dark:shadow-gray-900/50 dark:bg-gray-800">
            <div class="overflow-x-auto rounded-xl">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200 uppercase text-xs dark:bg-gray-700 dark:border-gray-700 dark:text-gray-400 text-black">
                        <tr>
                            <th class="px-4 py-3 text-left">{{ __('content.shape_code') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('content.description_th') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('content.description_en') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('content.type') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('content.status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('content.customer') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('content.updated_by') }}</th>
                            <th class="px-4 py-3 text-end w-[80px]">{{ __('content.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shapes as $shape)
                            @php
                                $status = $shape->status->status ?? 'Unknown';
                                $type = $shape->shapeType->name ?? '-';
                                $customer = $shape->customer->name ?? '-';
                                $process = $shape->process->process_name ?? '-';
                                $updatedBy = $shape->updater->name ?? '-';
                                $statusColor = match ($status) {
                                    'Active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'close' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                };
                            @endphp
                            <tr class="dark:text-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 bg-white border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $shape->item_code }}</td>
                                <td class="px-4 py-3">{{ Str::limit($shape->item_description_thai, 15) ?? '-' }}</td>
                                <td class="px-4 py-3">{{ Str::limit($shape->item_description_eng, 15) ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $type }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $statusColor }} px-2 py-1 rounded-full text-xs font-semibold">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">{{ $customer }}</td>
                                <td class="px-4 py-3 text-right">{{ $updatedBy }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-1">
                                        <button
                                            @click="openDetailModal({{ $shape->toJson() }})"
                                            class="flex items-center gap-1 px-2 py-1 text-sm font-medium text-white bg-blue-500 rounded-lg shadow-sm hover:bg-green-600 hover:shadow-md hoverScale"
                                        >
                                            <span class="material-symbols-outlined text-white">feature_search</span>
                                        </button>
                                        @if ($hasEdit)
                                            <button @click="openEditModal({{ $shape->toJson() }})" class="text-blue-600 hover:text-blue-700">
                                                <span class="material-symbols-outlined">edit</span>
                                            </button>
                                        @endif
                                        @if ($hasDelete)
                                            <button
                                                @click="DeleteShapeModal = true; shapeIdToDelete = {{ $shape->id }}; itemCodeToDelete = '{{ $shape->item_code }}'"
                                                class="text-red-500 hover:text-red-700"
                                            >
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-sm text-gray-500 text-center dark:text-gray-400">
                                    @if (request('search'))
                                        {{ __('content.not_found') }} "{{ request('search') }}".
                                    @else
                                        {{ __('content.not_found') }}
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4 flex justify-end pb-2">
                    {{ $shapes->links('vendor.pagination.tailwind-custom') }}
                </div>
            </div>
        </div>
        <!-- Modals -->
        @include('components.Edit-modals.edit-shape')
        @include('components.Detail-modals.detail-shape')
        @include('components.Create-modals.create-shape')
        <x-modals.delete-modal
            show="DeleteShapeModal"
            itemName="itemCodeToDelete"
            deleteFunction="deleteShape"
        />
    </main>
@endsection
