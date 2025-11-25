<?php

require_once 'vendor/autoload.php';

use App\Models\Job;
use Carbon\Carbon;

// Fix jobs with negative duration
$jobsWithNegativeDuration = Job::where('duration', '<', 0)->get();

echo "Found " . $jobsWithNegativeDuration->count() . " jobs with negative duration\n";

foreach ($jobsWithNegativeDuration as $job) {
    if ($job->start_time && $job->end_time) {
        // Recalculate duration correctly
        $correctDuration = $job->start_time->diffInMinutes($job->end_time, false);
        
        echo "Job ID: {$job->job_id}, Phase: {$job->phase}\n";
        echo "  Old duration: {$job->duration} minutes\n";
        echo "  New duration: {$correctDuration} minutes\n";
        
        // Update the job with correct duration
        $job->update(['duration' => $correctDuration]);
        
        echo "  ✓ Fixed\n\n";
    } else {
        echo "Job ID: {$job->job_id} - Missing start_time or end_time, cannot fix\n\n";
    }
}

echo "Duration fix completed!\n"; 