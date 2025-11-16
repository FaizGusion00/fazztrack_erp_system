@extends('layouts.app')

@section('title', 'Edit Job - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6">
        <!-- Compact Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-900 bg-clip-text text-transparent flex items-center">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg sm:rounded-xl flex items-center justify-center mr-2 sm:mr-3 lg:mr-4 shadow-lg">
                            <i class="fas fa-edit text-white text-sm sm:text-base lg:text-xl"></i>
                        </div>
                        <span class="hidden sm:inline">Edit Job</span>
                        <span class="sm:hidden">Edit</span>
                    </h1>
                    <p class="mt-2 sm:mt-3 text-sm sm:text-base lg:text-lg text-gray-600">Update job #{{ $job->job_id }} details and settings.</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                    <a href="{{ route('jobs.show', $job) }}" 
                       class="inline-flex items-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span class="hidden sm:inline">Back to Job</span>
                        <span class="sm:hidden">Back</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Job Form -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-sm border border-white/20 overflow-hidden">
            <form method="POST" action="{{ route('jobs.update', $job) }}" class="p-4 sm:p-6 lg:p-8">
                @csrf
                @method('PUT')
                
                <!-- Job Information -->
                <div class="mb-6 sm:mb-8">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Job Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Order Selection -->
                        <div>
                            <label for="order_id" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Order <span class="text-red-500">*</span>
                            </label>
                            <select id="order_id" 
                                    name="order_id" 
                                    class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('order_id') border-red-300 @enderror"
                                    required>
                                <option value="">Select Order</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->order_id }}" {{ old('order_id', $job->order_id) == $order->order_id ? 'selected' : '' }}>
                                        #{{ $order->order_id }} - {{ $order->job_name }} ({{ $order->client->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('order_id')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Production Phase -->
                        <div>
                            <label for="phase" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Production Phase <span class="text-red-500">*</span>
                            </label>
                            <select id="phase" 
                                    name="phase" 
                                    class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phase') border-red-300 @enderror"
                                    required>
                                <option value="">Select Phase</option>
                                <option value="PRINT" {{ old('phase', $job->phase) == 'PRINT' ? 'selected' : '' }}>PRINT</option>
                                <option value="PRESS" {{ old('phase', $job->phase) == 'PRESS' ? 'selected' : '' }}>PRESS</option>
                                <option value="CUT" {{ old('phase', $job->phase) == 'CUT' ? 'selected' : '' }}>CUT</option>
                                <option value="SEW" {{ old('phase', $job->phase) == 'SEW' ? 'selected' : '' }}>SEW</option>
                                <option value="QC" {{ old('phase', $job->phase) == 'QC' ? 'selected' : '' }}>QC</option>
                            </select>
                            @error('phase')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Job Status -->
                        <div>
                            <label for="status" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Job Status <span class="text-red-500">*</span>
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-300 @enderror"
                                    required>
                                <option value="">Select Status</option>
                                <option value="Pending" {{ old('status', $job->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ old('status', $job->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ old('status', $job->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="On Hold" {{ old('status', $job->status) == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Assigned User -->
                        <div>
                            <label for="assigned_user_id" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Assign To
                            </label>
                            <select id="assigned_user_id" 
                                    name="assigned_user_id" 
                                    class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('assigned_user_id') border-red-300 @enderror">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_user_id', $job->assigned_user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->phase }})
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_user_id')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Quantities Section -->
                <div class="mb-6 sm:mb-8">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center">
                        <i class="fas fa-boxes mr-2 text-green-500"></i>
                        Quantities
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                        <!-- Start Quantity -->
                        <div>
                            <label for="start_quantity" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Start Quantity
                            </label>
                            <input type="number" 
                                   id="start_quantity" 
                                   name="start_quantity" 
                                   value="{{ old('start_quantity', $job->start_quantity) }}"
                                   min="1"
                                   class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_quantity') border-red-300 @enderror"
                                   placeholder="Enter start quantity">
                            @error('start_quantity')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Quantity -->
                        <div>
                            <label for="end_quantity" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                End Quantity
                            </label>
                            <input type="number" 
                                   id="end_quantity" 
                                   name="end_quantity" 
                                   value="{{ old('end_quantity', $job->end_quantity) }}"
                                   min="0"
                                   class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('end_quantity') border-red-300 @enderror"
                                   placeholder="Enter end quantity">
                            @error('end_quantity')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reject Quantity -->
                        <div>
                            <label for="reject_quantity" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Reject Quantity
                            </label>
                            <input type="number" 
                                   id="reject_quantity" 
                                   name="reject_quantity" 
                                   value="{{ old('reject_quantity', $job->reject_quantity) }}"
                                   min="0"
                                   class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('reject_quantity') border-red-300 @enderror"
                                   placeholder="Enter reject quantity">
                            @error('reject_quantity')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Reject Status -->
                    <div class="mt-4 sm:mt-6">
                        <label for="reject_status" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                            Reject Status
                        </label>
                        <input type="text" 
                               id="reject_status" 
                               name="reject_status" 
                               value="{{ old('reject_status', $job->reject_status) }}"
                               class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('reject_status') border-red-300 @enderror"
                               placeholder="Enter reject status or reason">
                        @error('reject_status')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Time Tracking Section -->
                <div class="mb-6 sm:mb-8">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center">
                        <i class="fas fa-clock mr-2 text-orange-500"></i>
                        Time Tracking
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Start Time -->
                        <div>
                            <label for="start_time" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                Start Time
                            </label>
                            <input type="datetime-local" 
                                   id="start_time" 
                                   name="start_time" 
                                   value="{{ old('start_time', $job->start_time ? $job->start_time->format('Y-m-d\TH:i') : '') }}"
                                   class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_time') border-red-300 @enderror">
                            @error('start_time')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div>
                            <label for="end_time" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                End Time
                            </label>
                            <input type="datetime-local" 
                                   id="end_time" 
                                   name="end_time" 
                                   value="{{ old('end_time', $job->end_time ? $job->end_time->format('Y-m-d\TH:i') : '') }}"
                                   class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('end_time') border-red-300 @enderror">
                            @error('end_time')
                                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Duration Display -->
                    @if($job->duration)
                    <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="font-medium text-blue-800">Current Duration:</span>
                            <span class="text-blue-600 font-semibold">{{ $job->duration }} minutes</span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Remarks Section -->
                <div class="mb-6 sm:mb-8">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center">
                        <i class="fas fa-comment mr-2 text-purple-500"></i>
                        Remarks & Notes
                    </h3>
                    
                    <div>
                        <label for="remarks" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Additional Notes</label>
                        <textarea id="remarks" 
                                  name="remarks" 
                                  rows="4"
                                  class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('remarks') border-red-300 @enderror"
                                  placeholder="Enter any special instructions, notes, or remarks about this job...">{{ old('remarks', $job->remarks) }}</textarea>
                        @error('remarks')
                            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row items-center justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('jobs.show', $job) }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 border border-transparent rounded-lg font-medium text-white hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Update Job
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Add form validation and enhancement
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const startQuantity = document.getElementById('start_quantity');
    const endQuantity = document.getElementById('end_quantity');
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');

    // Validate quantities
    function validateQuantities() {
        if (startQuantity.value && endQuantity.value) {
            if (parseInt(endQuantity.value) > parseInt(startQuantity.value)) {
                endQuantity.setCustomValidity('End quantity cannot be greater than start quantity');
                return false;
            } else {
                endQuantity.setCustomValidity('');
            }
        }
        return true;
    }

    // Validate times
    function validateTimes() {
        if (startTime.value && endTime.value) {
            if (new Date(endTime.value) < new Date(startTime.value)) {
                endTime.setCustomValidity('End time cannot be before start time');
                return false;
            } else {
                endTime.setCustomValidity('');
            }
        }
        return true;
    }

    // Add event listeners
    startQuantity.addEventListener('input', validateQuantities);
    endQuantity.addEventListener('input', validateQuantities);
    startTime.addEventListener('input', validateTimes);
    endTime.addEventListener('input', validateTimes);

    // Form submission
    form.addEventListener('submit', function(e) {
        if (!validateQuantities() || !validateTimes()) {
            e.preventDefault();
            alert('Please fix the validation errors before submitting.');
        }
    });

    // Auto-calculate duration when both times are provided
    function calculateDuration() {
        if (startTime.value && endTime.value) {
            const start = new Date(startTime.value);
            const end = new Date(endTime.value);
            const duration = Math.round((end - start) / (1000 * 60)); // minutes
            
            if (duration > 0) {
                console.log('Calculated duration:', duration, 'minutes');
            }
        }
    }

    startTime.addEventListener('change', calculateDuration);
    endTime.addEventListener('change', calculateDuration);
});
</script>
@endsection 