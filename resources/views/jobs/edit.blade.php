@extends('layouts.app')

@section('title', 'Edit Job - Fazztrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-edit mr-3 text-primary-500"></i>
                    Edit Job
                </h1>
                <p class="mt-2 text-gray-600">Update job #{{ $job->job_id }} details.</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('jobs.show', $job) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Job
                </a>
            </div>
        </div>
    </div>

    <!-- Job Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('jobs.update', $job) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Job Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-primary-500"></i>
                    Job Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Order <span class="text-red-500">*</span>
                        </label>
                        <select id="order_id" 
                                name="order_id" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('order_id') border-red-300 @enderror"
                                required>
                            <option value="">Select Order</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->order_id }}" {{ old('order_id', $job->order_id) == $order->order_id ? 'selected' : '' }}>
                                    #{{ $order->order_id }} - {{ $order->job_name }} ({{ $order->client->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('order_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phase" class="block text-sm font-medium text-gray-700 mb-2">
                            Production Phase <span class="text-red-500">*</span>
                        </label>
                        <select id="phase" 
                                name="phase" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('phase') border-red-300 @enderror"
                                required>
                            <option value="">Select Phase</option>
                            <option value="PRINT" {{ old('phase', $job->phase) == 'PRINT' ? 'selected' : '' }}>PRINT</option>
                            <option value="PRESS" {{ old('phase', $job->phase) == 'PRESS' ? 'selected' : '' }}>PRESS</option>
                            <option value="CUT" {{ old('phase', $job->phase) == 'CUT' ? 'selected' : '' }}>CUT</option>
                            <option value="SEW" {{ old('phase', $job->phase) == 'SEW' ? 'selected' : '' }}>SEW</option>
                            <option value="QC" {{ old('phase', $job->phase) == 'QC' ? 'selected' : '' }}>QC</option>
                            <option value="IRON/PACKING" {{ old('phase', $job->phase) == 'IRON/PACKING' ? 'selected' : '' }}>IRON/PACKING</option>
                        </select>
                        @error('phase')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="start_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="start_quantity" 
                               name="start_quantity" 
                               value="{{ old('start_quantity', $job->start_quantity) }}"
                               min="1"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('start_quantity') border-red-300 @enderror"
                               placeholder="Enter start quantity"
                               required>
                        @error('start_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="assigned_user_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Assign To
                        </label>
                        <select id="assigned_user_id" 
                                name="assigned_user_id" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Unassigned</option>
                            @foreach(\App\Models\User::where('role', 'Production Staff')->get() as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_user_id', $job->assigned_user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->phase }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Remarks -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-comment mr-2 text-primary-500"></i>
                    Remarks
                </h3>
                
                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea id="remarks" 
                              name="remarks" 
                              rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('remarks') border-red-300 @enderror"
                              placeholder="Enter any special instructions or notes...">{{ old('remarks', $job->remarks) }}</textarea>
                    @error('remarks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('jobs.show', $job) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-primary-500 border border-transparent rounded-md font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Job
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 